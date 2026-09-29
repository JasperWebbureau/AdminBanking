<?php

declare(strict_types=1);

namespace Flexgrid\Modules\AdminBanking\Application\UseCase;

use Flexgrid\Modules\AdminBanking\Application\ReadModel\BankMatchContext;
use Flexgrid\Modules\AdminBanking\Application\ReadModel\BankMatchTarget;
use Flexgrid\Modules\AdminBanking\Contract\BankMatchTargetProviderInterface;
use Flexgrid\Modules\AdminBanking\Contract\BankTransactionRepositoryInterface;
use Flexgrid\Modules\AdminBanking\Domain\Model\BankTransaction;
use Flexgrid\Modules\AdminBanking\Domain\ValueObject\BankTransactionStatus;
use Flexgrid\Modules\AdminCore\Context\TenantContext;

final class SearchBankMatchTargets
{
    private $tenant;
    private $transactions;
    private $providers;

    public function __construct(TenantContext $tenant, BankTransactionRepositoryInterface $transactions, array $providers)
    {
        foreach ($providers as $provider) {
            if (!$provider instanceof BankMatchTargetProviderInterface) {
                throw new \InvalidArgumentException('Ongeldige handmatige bankmatchprovider.');
            }
        }
        $this->tenant = $tenant;
        $this->transactions = $transactions;
        $this->providers = $providers;
    }

    public function execute(string $transactionPublicId, string $query): array
    {
        $transactionPublicId = trim($transactionPublicId);
        $query = trim($query);
        if ($transactionPublicId === '' || strlen($transactionPublicId) > 64 || strlen($query) > 120) {
            throw new \InvalidArgumentException('Ongeldige zoekopdracht voor handmatig afletteren.');
        }

        $tenant = $this->tenant->getTenantId();
        $transaction = $this->transactions->findByPublicId($tenant, $transactionPublicId);
        if ($transaction === null) {
            throw new \DomainException('Banktransactie is niet gevonden.');
        }
        if ($transaction->getStatus()->getValue() !== BankTransactionStatus::UNMATCHED) {
            throw new \DomainException('Alleen een open banktransactie kan handmatig worden afgeletterd.');
        }
        if ($this->length($query) < 2) {
            return ['transaction' => $transaction, 'query' => $query, 'targets' => []];
        }

        $context = $this->context($transaction);
        $targets = [];
        foreach ($this->providers as $provider) {
            foreach ($provider->search($tenant, $context, $query, 20) as $target) {
                if (!$target instanceof BankMatchTarget || !$provider->supports($target->getTargetType())) {
                    throw new \UnexpectedValueException('Handmatige bankmatchprovider leverde een ongeldig doel.');
                }
                $key = $target->getTargetType() . '|' . $target->getTargetPublicId();
                if (!isset($targets[$key])) {
                    $targets[$key] = $target;
                }
            }
        }

        $targets = array_slice(array_values($targets), 0, 20);
        return ['transaction' => $transaction, 'query' => $query, 'targets' => $targets];
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

    private function length(string $value): int
    {
        return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
    }
}
