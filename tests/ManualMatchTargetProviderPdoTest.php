<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/bootstrap.php';

$projectRoot = dirname(__DIR__, 4);
require_once $projectRoot . '/flexgrid/flexgrid/src/Database/Connection.php';
require_once $projectRoot . '/flexgrid/Modules/AdminPayment/src/Integration/Banking/MatchTargetProvider.php';
require_once $projectRoot . '/flexgrid/Modules/AdminExpense/src/Integration/Banking/MatchTargetProvider.php';
require $projectRoot . '/.env.php';

use Flexgrid\Database\Connection;
use Flexgrid\Modules\AdminBanking\Application\ReadModel\BankMatchContext;
use Flexgrid\Modules\AdminCore\ValueObject\TenantId;
use Flexgrid\Modules\AdminExpense\Integration\Banking\MatchTargetProvider as ExpenseTargetProvider;
use Flexgrid\Modules\AdminPayment\Integration\Banking\MatchTargetProvider as InvoiceTargetProvider;

$pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['name'], $db['username'], $db['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
$database = $db['name'];
unset($db);
Connection::$connections[$database] = $pdo;
Connection::$default = $database;
$tenant = new TenantId('admin-banking-manual-provider-test');

$pdo->beginTransaction();
try {
    $invoice = $pdo->prepare("INSERT INTO `admin_invoice` (`tenant_id`,`public_id`,`invoice_number`,`invoice_sequence_value`,`status`,`payment_status`,`currency`,`customer_reference`,`customer_snapshot`,`billing_address_snapshot`,`issue_date`,`due_date`,`finalized_at`,`notes`,`source`,`external_id`,`net_total_minor`,`tax_total_minor`,`gross_total_minor`,`tax_summary_snapshot`,`created_at`,`updated_at`) VALUES (:tenant,'manual-invoice-1','2026-F-0123',123,'final','unpaid','EUR','WEB-123',:customer,:address,'2026-09-01','2026-09-30',1770000000,NULL,NULL,NULL,10000,2100,12100,:tax,1770000000,1770000000)");
    $invoice->execute([
        ':tenant' => $tenant->toString(),
        ':customer' => json_encode(['schema_version' => 2, 'name' => 'Handmatige Klant', 'contact_name' => '', 'email' => '', 'phone' => '', 'registration_number' => '', 'tax_number' => '', 'source_public_id' => '']),
        ':address' => json_encode(['schema_version' => 1, 'street' => 'Straat', 'house_number' => '1', 'addition' => '', 'postal_code' => '1234AB', 'city' => 'Plaats', 'country_code' => 'NL']),
        ':tax' => json_encode(['schema_version' => 1, 'groups' => []]),
    ]);
    $expense = $pdo->prepare("INSERT INTO `admin_expense` (`tenant_id`,`public_id`,`category_public_id`,`currency`,`expense_date`,`title`,`description`,`reference`,`supplier_snapshot`,`net_amount_minor`,`tax_amount_minor`,`gross_amount_minor`,`source`,`external_id`,`created_at`,`updated_at`) VALUES (:tenant,'manual-expense-1','category-1','EUR','2026-09-18','Handmatige hosting',NULL,'HOST-123',:supplier,599,126,725,NULL,NULL,1770000000,1770000000)");
    $expense->execute([
        ':tenant' => $tenant->toString(),
        ':supplier' => json_encode(['schema_version' => 1, 'name' => 'Handmatige Hoster', 'contact_name' => '', 'email' => '', 'registration_number' => '', 'tax_number' => '', 'country_code' => 'NL', 'source_public_id' => '']),
    ]);

    $invoiceContext = new BankMatchContext('manual-bank-in', 12100, 'EUR', '2026-09-20', 'Handmatige Klant', '', '', '');
    $invoiceTargets = (new InvoiceTargetProvider())->search($tenant, $invoiceContext, 'Handmatige', 20);
    adminBankingAssert(count($invoiceTargets) === 1 && $invoiceTargets[0]->isSelectable() && $invoiceTargets[0]->getAvailableMinor() === 12100, 'Factuurzoeker moet tenantgebonden openstaand bedrag en klant tonen.');
    $partialTarget = (new InvoiceTargetProvider())->find($tenant, new BankMatchContext('manual-bank-partial', 5000, 'EUR', '2026-09-20', '', '', '', ''), 'manual-invoice-1');
    adminBankingAssert($partialTarget !== null && $partialTarget->isSelectable() && $partialTarget->getWarning() !== '', 'Een deelbetaling moet selecteerbaar zijn met een duidelijke waarschuwing.');
    $overTarget = (new InvoiceTargetProvider())->find($tenant, new BankMatchContext('manual-bank-over', 13000, 'EUR', '2026-09-20', '', '', '', ''), 'manual-invoice-1');
    adminBankingAssert($overTarget !== null && !$overTarget->isSelectable(), 'Een bankbedrag boven het openstaande factuurbedrag moet niet koppelbaar zijn.');

    $expenseContext = new BankMatchContext('manual-bank-out', -725, 'EUR', '2026-09-20', 'Handmatige Hoster', '', '', '');
    $expenseTargets = (new ExpenseTargetProvider())->search($tenant, $expenseContext, 'HOST-123', 20);
    adminBankingAssert(count($expenseTargets) === 1 && $expenseTargets[0]->isSelectable(), 'Uitgavezoeker moet een exacte tenantgebonden uitgave selecteerbaar tonen.');
    $wrongExpense = (new ExpenseTargetProvider())->find($tenant, new BankMatchContext('manual-bank-wrong', -700, 'EUR', '2026-09-20', '', '', '', ''), 'manual-expense-1');
    adminBankingAssert($wrongExpense !== null && !$wrongExpense->isSelectable() && $wrongExpense->getWarning() !== '', 'Afwijkend uitgavebedrag moet zichtbaar maar niet koppelbaar zijn.');

    $pdo->rollBack();
} catch (Throwable $throwable) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    throw $throwable;
}

echo "AdminBanking manual target provider PDO tests passed.\n";
