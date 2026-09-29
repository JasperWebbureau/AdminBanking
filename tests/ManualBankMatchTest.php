<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use Flexgrid\Modules\AdminBanking\Application\ReadModel\BankMatchContext;
use Flexgrid\Modules\AdminBanking\Application\ReadModel\BankMatchTarget;
use Flexgrid\Modules\AdminBanking\Application\UseCase\AcceptManualBankMatch;
use Flexgrid\Modules\AdminBanking\Application\UseCase\SearchBankMatchTargets;
use Flexgrid\Modules\AdminBanking\Contract\BankMatchProcessorInterface;
use Flexgrid\Modules\AdminBanking\Contract\BankMatchTargetProviderInterface;
use Flexgrid\Modules\AdminBanking\Contract\BankTransactionRepositoryInterface;
use Flexgrid\Modules\AdminBanking\Domain\Model\BankTransaction;
use Flexgrid\Modules\AdminBanking\Domain\ValueObject\BankDate;
use Flexgrid\Modules\AdminBanking\Domain\ValueObject\BankSourceSnapshot;
use Flexgrid\Modules\AdminBanking\Domain\ValueObject\CounterpartySnapshot;
use Flexgrid\Modules\AdminBanking\Service\BankMatchTargetProviderLoader;
use Flexgrid\Modules\AdminCore\Context\TenantContext;
use Flexgrid\Modules\AdminCore\Contract\TransactionManagerInterface;
use Flexgrid\Modules\AdminCore\ValueObject\Currency;
use Flexgrid\Modules\AdminCore\ValueObject\Money;
use Flexgrid\Modules\AdminCore\ValueObject\TenantId;
use Flexgrid\Utils\_Time;

final class ManualMatchTransactions implements BankTransactionRepositoryInterface
{
    public $item;
    public $updates = 0;
    public function insert(BankTransaction $transaction, int $createdAt): void { $this->item = $transaction; }
    public function findByExternalId(TenantId $tenant, string $account, string $externalId): ?BankTransaction { return null; }
    public function findByPublicId(TenantId $tenant, string $publicId): ?BankTransaction { return $this->item !== null && $this->item->getTenantId()->equals($tenant) && $this->item->getPublicId() === $publicId ? $this->item : null; }
    public function findByPublicIdForUpdate(TenantId $tenant, string $publicId): ?BankTransaction { return $this->findByPublicId($tenant, $publicId); }
    public function updateMatchState(BankTransaction $transaction, int $updatedAt): void { $this->item = $transaction; ++$this->updates; }
}

final class ManualMatchProvider implements BankMatchTargetProviderInterface
{
    public $searchCalls = 0;
    public $selectable = true;
    public function supports(string $targetType): bool { return $targetType === 'invoice'; }
    public function search(TenantId $tenant, BankMatchContext $context, string $query, int $limit): array { ++$this->searchCalls; return [$this->target('invoice-1')]; }
    public function find(TenantId $tenant, BankMatchContext $context, string $targetPublicId): ?BankMatchTarget { return $targetPublicId === 'invoice-1' ? $this->target($targetPublicId) : null; }
    private function target(string $id): BankMatchTarget { return new BankMatchTarget('invoice', $id, 'Factuur F-1', 'Klant', 'F-1', '2026-09-20', 12100, 12100, 'EUR', $this->selectable, $this->selectable ? '' : 'Bedrag past niet.'); }
}

final class ManualMatchProcessor implements BankMatchProcessorInterface
{
    public $calls = 0;
    public function supports(string $targetType): bool { return $targetType === 'invoice'; }
    public function process(TenantId $tenant, BankTransaction $transaction, string $targetPublicId): void { ++$this->calls; }
}

final class ManualMatchTransactionManager implements TransactionManagerInterface
{
    public function transactional(callable $operation) { return $operation(); }
}

function manualBankTransaction(string $id, TenantId $tenant): BankTransaction
{
    return new BankTransaction($id, $tenant, 'account-1', 'import-1', 'external-' . $id, new BankDate('2026-09-20'), new Money(12100, Currency::euro()), new CounterpartySnapshot('Klant'), 'Factuurbetaling', 'F-1', new BankSourceSnapshot('abn_tab', str_repeat('d', 64), []));
}

$tenant = new TenantId('manual-match-test');
$repository = new ManualMatchTransactions();
$repository->item = manualBankTransaction('manual-tx-1', $tenant);
$provider = new ManualMatchProvider();
$search = new SearchBankMatchTargets(new TenantContext($tenant), $repository, [$provider]);
$short = $search->execute('manual-tx-1', 'F');
adminBankingAssert($short['targets'] === [] && $provider->searchCalls === 0, 'Zoeken start pas vanaf twee tekens.');
$result = $search->execute('manual-tx-1', 'F-1');
adminBankingAssert(count($result['targets']) === 1 && $result['targets'][0]->getTargetPublicId() === 'invoice-1', 'Handmatig zoeken moet gevalideerde tenantdoelen teruggeven.');

$processor = new ManualMatchProcessor();
$accept = new AcceptManualBankMatch(new TenantContext($tenant), new ManualMatchTransactionManager(), $repository, [$provider], [$processor], new _Time(1770000300));
$matched = $accept->execute('manual-tx-1', 'invoice', 'invoice-1');
$accept->execute('manual-tx-1', 'invoice', 'invoice-1');
adminBankingAssert($matched->getStatus()->getValue() === 'matched' && $processor->calls === 1 && $repository->updates === 1, 'Handmatige bevestiging moet dezelfde idempotente processor en statusmutatie gebruiken.');

$invalidRepository = new ManualMatchTransactions();
$invalidRepository->item = manualBankTransaction('manual-tx-2', $tenant);
$invalidAccept = new AcceptManualBankMatch(new TenantContext($tenant), new ManualMatchTransactionManager(), $invalidRepository, [$provider], [$processor], new _Time(1770000400));
adminBankingAssertThrows(DomainException::class, function () use ($invalidAccept): void { $invalidAccept->execute('manual-tx-2', 'invoice', 'tampered-id'); }, 'Vrije of gemanipuleerde doel-id moet opnieuw door de provider worden geweigerd.');
$provider->selectable = false;
adminBankingAssertThrows(DomainException::class, function () use ($invalidAccept): void { $invalidAccept->execute('manual-tx-2', 'invoice', 'invoice-1'); }, 'Niet-passende bedragen mogen niet handmatig worden bevestigd.');

$loadedTypes = [];
foreach ((new BankMatchTargetProviderLoader(dirname(__DIR__, 2)))->getProviders() as $loadedProvider) {
    foreach (['invoice', 'expense'] as $type) { if ($loadedProvider->supports($type)) { $loadedTypes[$type] = true; } }
}
adminBankingAssert(isset($loadedTypes['invoice'], $loadedTypes['expense']), 'Conventiediscovery moet de handmatige Payment- en Expense-providers vinden.');

echo "AdminBanking manual match tests passed.\n";
