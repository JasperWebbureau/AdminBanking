<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use Flexgrid\Modules\AdminBanking\Domain\Model\BankTransaction;
use Flexgrid\Modules\AdminBanking\Domain\ValueObject\BankDate;
use Flexgrid\Modules\AdminBanking\Domain\ValueObject\BankSourceSnapshot;
use Flexgrid\Modules\AdminBanking\Domain\ValueObject\CounterpartySnapshot;
use Flexgrid\Modules\AdminBanking\Service\BankMatchPresenter;
use Flexgrid\Modules\AdminCore\ValueObject\Currency;
use Flexgrid\Modules\AdminCore\ValueObject\Money;
use Flexgrid\Modules\AdminCore\ValueObject\TenantId;

function renderBankExpenseTemplate(BankTransaction $transaction): string
{
    $view = (new BankMatchPresenter())->present(['transaction' => $transaction, 'proposals' => []], 'generate-action', 'accept-action', 'ignore-action', 'search-action', 'manual-action');
    $view += [
        'createExpenseAction' => 'create-expense-action',
        'expenseCreationAvailable' => true,
        'expenseEditBaseUrl' => '/Flexgrid/AdminExpense/edit',
        'expenseCategories' => [['public_id' => 'category-1', 'name' => 'Software', 'type_label' => 'Zakelijke kosten']],
        'existingSuppliers' => [['expense_public_id' => 'earlier-expense', 'name' => 'Hoster BV']],
    ];
    extract($view);
    ob_start();
    include dirname(__DIR__) . '/src/Templates/Match/Content.php';
    return (string)ob_get_clean();
}

$tenant = new TenantId('bank-expense-template-tenant');
$source = new BankSourceSnapshot('abn_tab', str_repeat('a', 64), []);
$outgoing = new BankTransaction('bank-out', $tenant, 'account-1', 'import-1', 'external-out', new BankDate('2026-09-20'), new Money(-12100, Currency::euro()), new CounterpartySnapshot('Hoster BV'), 'Hosting september', 'INV-1', $source);
$open = renderBankExpenseTemplate($outgoing);
adminBankingAssert(strpos($open, 'Maak uitgave') !== false && strpos($open, 'name="vat_rate"') !== false && strpos($open, 'value="21"') !== false, 'Open afschrijving moet het nieuwe uitgavenformulier met 21% tonen.');
adminBankingAssert(strpos($open, 'name="supplier_name"') !== false && strpos($open, 'name="supplier_expense_public_id"') !== false && strpos($open, 'name="attachment"') !== false, 'Leverancier en bewijsstuk moeten optioneel beschikbaar zijn.');
$outgoing->match('expense', 'expense-1', 1770000000);
$matched = renderBankExpenseTemplate($outgoing);
adminBankingAssert(strpos($matched, 'Maak uitgave') === false && strpos($matched, 'Open uitgave') !== false, 'Afgeletterde uitgave moet te openen zijn zonder opnieuw aanmaken.');
$incoming = new BankTransaction('bank-in', $tenant, 'account-1', 'import-1', 'external-in', new BankDate('2026-09-20'), new Money(12100, Currency::euro()), new CounterpartySnapshot('Klant'), 'Ontvangst', 'INV-2', $source);
adminBankingAssert(strpos(renderBankExpenseTemplate($incoming), 'Maak uitgave') === false, 'Bijschrijving mag geen uitgaveactie krijgen.');

echo "AdminBanking expense template tests passed.\n";
