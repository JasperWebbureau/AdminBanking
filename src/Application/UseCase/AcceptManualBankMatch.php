<?php

declare(strict_types=1);

namespace Flexgrid\Modules\AdminBanking\Application\UseCase;

use Flexgrid\Modules\AdminBanking\Application\ReadModel\BankMatchContext;
use Flexgrid\Modules\AdminBanking\Contract\BankMatchProcessorInterface;
use Flexgrid\Modules\AdminBanking\Contract\BankMatchTargetProviderInterface;
use Flexgrid\Modules\AdminBanking\Contract\BankTransactionRepositoryInterface;
use Flexgrid\Modules\AdminBanking\Domain\Model\BankTransaction;
use Flexgrid\Modules\AdminBanking\Domain\ValueObject\BankTransactionStatus;
use Flexgrid\Modules\AdminCore\Context\TenantContext;
use Flexgrid\Modules\AdminCore\Contract\TransactionManagerInterface;
use Flexgrid\Utils\_Time;

final class AcceptManualBankMatch
{
    private $tenant;
    private $transactionManager;
    private $transactions;
    private $providers;
    private $processors;
    private $clock;

    public function __construct(
        TenantContext $tenant,
        TransactionManagerInterface $transactionManager,
        BankTransactionRepositoryInterface $transactions,
        array $providers,
        array $processors,
        _Time $clock
    ) {
        foreach ($providers as $provider) {
            if (!$provider instanceof BankMatchTargetProviderInterface) {
                throw new \InvalidArgumentException('Ongeldige handmatige bankmatchprovider.');
            }
        }
        foreach ($processors as $processor) {
            if (!$processor instanceof BankMatchProcessorInterface) {
                throw new \InvalidArgumentException('Ongeldige bankmatchprocessor.');
            }
        }
        $this->tenant = $tenant;
        $this->transactionManager = $transactionManager;
        $this->transactions = $transactions;
        $this->providers = $providers;
        $this->processors = $processors;
        $this->clock = $clock;
    }

    public function execute(string $transactionPublicId, string $targetType, string $targetPublicId): BankTransaction
    {
        $transactionPublicId = trim($transactionPublicId);
        $targetType = strtolower(trim($targetType));
        $targetPublicId = trim($targetPublicId);
        if ($transactionPublicId === '' || strlen($transactionPublicId) > 64
            || preg_match('/^[a-z][a-z0-9_]{1,31}$/D', $targetType) !== 1
            || $targetPublicId === '' || strlen($targetPublicId) > 64
        ) {
            throw new \InvalidArgumentException('Banktransactie en een geldig afletterdoel zijn verplicht.');
        }

        $tenant = $this->tenant->getTenantId();
        return $this->transactionManager->transactional(function () use ($tenant, $transactionPublicId, $targetType, $targetPublicId): BankTransaction {
            $transaction = $this->transactions->findByPublicIdForUpdate($tenant, $transactionPublicId);
            if ($transaction === null) {
                throw new \DomainException('Banktransactie is niet gevonden.');
            }
            if ($transaction->getStatus()->getValue() === BankTransactionStatus::MATCHED) {
                if ($transaction->getMatchedTargetType() === $targetType && $transaction->getMatchedTargetPublicId() === $targetPublicId) {
                    return $transaction;
                }
                throw new \DomainException('Banktransactie is al aan een ander doel afgeletterd.');
            }
            if ($transaction->getStatus()->getValue() !== BankTransactionStatus::UNMATCHED) {
                throw new \DomainException('Deze banktransactie is niet meer open.');
            }

            $provider = $this->provider($targetType);
            $target = $provider->find($tenant, $this->context($transaction), $targetPublicId);
            if ($target === null || $target->getTargetType() !== $targetType || $target->getTargetPublicId() !== $targetPublicId) {
                throw new \DomainException('Het gekozen afletterdoel bestaat niet of is niet meer beschikbaar.');
            }
            if (!$target->isSelectable()) {
                throw new \DomainException($target->getWarning() !== '' ? $target->getWarning() : 'Het gekozen doel past niet bij deze banktransactie.');
            }

            $this->processor($targetType)->process($tenant, $transaction, $targetPublicId);
            $timestamp = (int)$this->clock->get();
            $transaction->match($targetType, $targetPublicId, $timestamp);
            $this->transactions->updateMatchState($transaction, $timestamp);
            return $transaction;
        });
    }

    private function provider(string $targetType): BankMatchTargetProviderInterface
    {
        $matches = array_values(array_filter($this->providers, function (BankMatchTargetProviderInterface $provider) use ($targetType): bool {
            return $provider->supports($targetType);
        }));
        if (count($matches) !== 1) {
            throw new \LogicException(count($matches) === 0 ? 'Geen zoekprovider beschikbaar voor dit afletterdoel.' : 'Meerdere zoekproviders claimen hetzelfde afletterdoel.');
        }
        return $matches[0];
    }

    private function processor(string $targetType): BankMatchProcessorInterface
    {
        $matches = array_values(array_filter($this->processors, function (BankMatchProcessorInterface $processor) use ($targetType): bool {
            return $processor->supports($targetType);
        }));
        if (count($matches) !== 1) {
            throw new \LogicException(count($matches) === 0 ? 'Geen verwerker beschikbaar voor dit afletterdoel.' : 'Meerdere verwerkers claimen hetzelfde afletterdoel.');
        }
        return $matches[0];
    }

    private function context(BankTransaction $transaction): BankMatchContext
    {
        return new BankMatchContext(
            $transaction->getPublicId(),
            $transaction->getAmount()->getMinorUnits(),
            $transaction->getAmount()->getCurrency()->getCode(),
            $transaction->getBookedOn()->getValue(),
            $transaction->getCounterparty()->getName(),
            $transaction->getCounterparty()->getIban(),
            $transaction->getDescription(),
            $transaction->getReference()
        );
    }
}
