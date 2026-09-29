<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$module = dirname(__DIR__);
$controller = (string)file_get_contents($module . '/src/Controller/AdminBankingController.php');
$content = (string)file_get_contents($module . '/src/Templates/Transactions/Content.php');
$match = (string)file_get_contents($module . '/src/Templates/Match/Content.php');
$manual = (string)file_get_contents($module . '/src/Templates/Match/ManualResults.php');
$script = (string)file_get_contents($module . '/src/Templates/Transactions/Js/Transactions.js')
    . (string)file_get_contents($module . '/src/Templates/Match/Js/Match.js');
$style = (string)file_get_contents($module . '/src/Templates/Transactions/Css/Transactions.scss')
    . (string)file_get_contents($module . '/src/Templates/Match/Css/Match.scss');

adminBankingAssert(strpos($controller, '@FG\\Controller [name=AdminBanking') !== false, 'Banking-controller moet als Flexgrid-module zijn geannoteerd.');
adminBankingAssert(strpos($content, 'TableRenderer') !== false, 'Banktransacties moeten de gedeelde TableRenderer gebruiken.');
adminBankingAssert(strpos($content, 'enctype="multipart/form-data"') !== false && strpos($content, 'name="statement_content"') !== false, 'Bankimport moet uploaden en plakken via hetzelfde formulier ondersteunen.');
adminBankingAssert(substr_count($content, 'ajax="true"') === 3, 'Bankrekening, import en filters moeten declaratieve Flexgrid-AJAX gebruiken.');
adminBankingAssert(strpos($content, 'button-outline') === false && strpos($match, 'button-outline') === false && strpos($manual, 'button-outline') === false, 'Bankingschermen mogen button-outline niet gebruiken.');
adminBankingAssert(strpos($match, 'ajax="true"') !== false && strpos($match, 'Match bevestigen') !== false && strpos($match, 'Negeren') !== false, 'Matchscherm moet voorstellen expliciet via centrale AJAX laten bevestigen of negeren.');
adminBankingAssert(strpos($controller, 'createAcceptMatch') !== false && strpos($controller, 'createIgnoreTransaction') !== false, 'Controller moet beide auditbare beslisroutes gebruiken.');
adminBankingAssert(strpos($match, 'data-admin-banking-manual-search') !== false && strpos($manual, 'Handmatig koppelen') !== false && strpos($controller, 'createAcceptManualMatch') !== false, 'Matchscherm moet handmatig zoeken en gevalideerd bevestigen via AJAX ondersteunen.');
foreach (['jQuery', '$(', 'fetch(', 'XMLHttpRequest'] as $forbidden) {
    adminBankingAssert(strpos($script, $forbidden) === false, 'Banking-JavaScript bevat verboden transport of jQuery: ' . $forbidden);
}
adminBankingAssert(strpos($script, 'class AdminBankingOverview') !== false && strpos($script, 'class AdminBankingMatch') !== false && strpos($script, 'requestSubmit') !== false, 'Bankingschermen moeten vanilla JS bovenop centrale AJAX gebruiken.');
adminBankingAssert(strpos($content, '<grid class="admin-banking-setup-grid fluid"') !== false && strpos($match, '<grid class="admin-banking-match-layout fluid"') !== false && strpos($style, '.admin-banking-account-form>.admin-field') !== false && strpos($style, '--cw:6') !== false && strpos($style, '.admin-banking-content>*') === false, 'Bankingschermen moeten Flexgrid-grids en --cw gebruiken zonder form-grid-reset.');
adminBankingAssert(strpos($controller, '10*1024*1024') !== false && strpos($controller, 'is_uploaded_file') !== false, 'Controller moet uploadgrootte en echte HTTP-upload bewaken.');

echo "AdminBanking UI contract tests passed.\n";
