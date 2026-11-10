<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once dirname(__DIR__) . '/src/Contract/BankExpenseCreatorInterface.php';
require_once dirname(__DIR__) . '/src/Service/QuickBankExpense.php';
require_once dirname(__DIR__, 3) . '/Flexgrid/src/Html/Table/TrustedHtml.php';
require_once dirname(__DIR__, 3) . '/Flexgrid/src/Html/Table/TableRenderer.php';

use Flexgrid\Modules\AdminBanking\Contract\BankExpenseCreatorInterface;
use Flexgrid\Modules\AdminBanking\Service\QuickBankExpense;
use Flexgrid\Modules\AdminCore\ValueObject\TenantId;

final class QuickExpenseTestCreator implements BankExpenseCreatorInterface
{
    public $fields = []; public $categories = [];
    public function categories(TenantId $tenant): array { return $this->categories; }
    public function suppliers(TenantId $tenant): array { return []; }
    public function create(TenantId $tenant, string $id, array $fields, ?array $attachment): array { $this->fields = $fields; return ['expense_public_id' => 'test']; }
}
$tenant = new TenantId('quick-expense'); $creator = new QuickExpenseTestCreator();
$creator->categories = [
    ['public_id' => 'private', 'code' => 'overig', 'name' => 'Privé', 'reporting_type' => 'private_withdrawal'],
    ['public_id' => 'software', 'code' => 'software', 'name' => 'Software', 'reporting_type' => 'operating_cost'],
    ['public_id' => 'general', 'code' => 'overig', 'name' => 'Overige kosten', 'reporting_type' => 'operating_cost'],
];
$quick = new QuickBankExpense($creator, $tenant);
adminBankingAssert($quick->category()['public_id'] === 'general', 'Snelactie moet algemene zakelijke kosten prefereren en privé uitsluiten.');
$bank = new \Flexgrid\Modules\AdminBanking\Domain\Model\BankTransaction('quick-bank', $tenant, 'account', 'import', 'external', new \Flexgrid\Modules\AdminBanking\Domain\ValueObject\BankDate('2026-10-01'), new \Flexgrid\Modules\AdminCore\ValueObject\Money(-12100, \Flexgrid\Modules\AdminCore\ValueObject\Currency::euro()), new \Flexgrid\Modules\AdminBanking\Domain\ValueObject\CounterpartySnapshot('Leverancier'), 'Aankoop', 'REF', new \Flexgrid\Modules\AdminBanking\Domain\ValueObject\BankSourceSnapshot('abn_tab', str_repeat('a', 64), []));
$quick->execute($bank);
adminBankingAssert($creator->fields['vat_rate'] === '21' && $creator->fields['supplier_name'] === 'Leverancier' && $creator->fields['description'] === 'Aankoop' && $creator->fields['reference'] === 'REF', 'Snelactie moet 21% en bankgegevens server-side kiezen.');
adminBankingAssertThrows(DomainException::class, static function () use ($creator, $bank): void { (new QuickBankExpense($creator, new TenantId('other')))->execute($bank); }, 'Andere tenant mag geen uitgave maken.');
$bank->ignore();
adminBankingAssertThrows(DomainException::class, static function () use ($quick, $bank): void { $quick->execute($bank); }, 'Genegeerde afschrijving mag geen uitgave maken.');

$query = new \Flexgrid\Modules\AdminBanking\Application\Query\BankTransactionListQuery('"<test>', '', '', 'all', 2026, 'amount', 'asc', 1, 25);
$items = [];
foreach ([['out', -12100, 'unmatched'], ['in', 12100, 'unmatched'], ['matched', -12100, 'matched'], ['ignored', -12100, 'ignored']] as $item) {
    $items[] = new \Flexgrid\Modules\AdminBanking\Application\ReadModel\BankTransactionListItem($item[0], '2026-10-01', 'Rekening', 'Leverancier', '', 'Aankoop', '', $item[1], 'EUR', $item[2]);
}
$data = ['query' => $query, 'result' => new \Flexgrid\Modules\AdminBanking\Application\ReadModel\BankTransactionListResult($items, 4, 1, 25), 'summaries' => [], 'years' => [2026], 'accounts' => []];
$presenter = new \Flexgrid\Modules\AdminBanking\Service\BankingOverviewPresenter();
$view = $presenter->present($data, 'refresh', 'account', 'import', '/match', 'quick-action', 'Overige kosten');
$html = (new \Flexgrid\Html\Table\TableRenderer($view['table']))->render();
adminBankingAssert(substr_count($html, 'action="quick-action"') === 1 && strpos($html, 'class="button button-icon button-secondary"') !== false, 'Alleen open afschrijving krijgt exact de gevraagde icoonknop.');
adminBankingAssert(strpos($html, 'data-q="&quot;&lt;test&gt;"') !== false && strpos($html, 'data-per_page="25"') !== false, 'AJAX-knop moet filters veilig meenemen.');
$without = $presenter->present($data, 'refresh', 'account', 'import', '/match');
adminBankingAssert(!isset($without['table']['rows'][0]['actions']), 'Zonder Expense-integratie geen snelactie.');
echo "AdminBanking quick expense tests passed.\n";
