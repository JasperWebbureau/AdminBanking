<?php
declare(strict_types=1);

namespace Flexgrid\Modules\AdminBanking\Service;

use Flexgrid\Modules\AdminBanking\Application\Query\BankTransactionListQuery;
use Flexgrid\Modules\AdminBanking\Application\ReadModel\BankingOverviewSummary;
use Flexgrid\Modules\AdminCore\ValueObject\Currency;
use Flexgrid\Modules\AdminCore\ValueObject\Money;

final class BankingOverviewPresenter
{
    public function present(array $data, string $refreshAction, string $createAccountAction, string $importAction, string $matchBaseUrl = ''): array
    {
        $query = $data['query'];
        $result = $data['result'];
        $rows = [];
        foreach ($result->getItems() as $item) {
            $status = $this->status($item->getStatus());
            $counterparty = $item->getCounterpartyName() !== '' ? $item->getCounterpartyName() : 'Onbekende tegenpartij';
            $rows[] = ['id' => $item->getPublicId(), 'url' => $matchBaseUrl !== '' ? rtrim($matchBaseUrl, '/') . '/' . rawurlencode($item->getPublicId()) : '', 'cells' => ['booked_on' => $this->date($item->getBookedOn()), 'counterparty' => ['value' => $counterparty, 'secondary' => $item->getCounterpartyIban(), 'title' => true], 'description' => ['value' => $item->getDescription() !== '' ? $item->getDescription() : '—', 'secondary' => $item->getReference()], 'account' => $item->getAccountName(), 'amount' => ['value' => $this->money($item->getAmountMinor(), $item->getCurrency()), 'classes' => [$item->getAmountMinor() > 0 ? 'admin-banking-amount--incoming' : 'admin-banking-amount--outgoing']], 'status' => ['value' => $status['label'], 'badge' => $status['tone']]]];
        }
        $columns = [];
        foreach (['booked_on' => ['Datum', 'left', true], 'counterparty' => ['Tegenpartij', 'left', true], 'description' => ['Omschrijving', 'left', true], 'account' => ['Rekening', 'left', true], 'amount' => ['Bedrag', 'right', true], 'status' => ['Status', 'left', true]] as $key => $settings) {
            $columns[] = ['key' => $key, 'label' => $settings[0], 'align' => $settings[1], 'sortable' => $settings[2], 'sort_direction' => $query->getSort() === $key ? $query->getDirection() : ''];
        }
        $summaries = $data['summaries'];
        if ($summaries === []) {
            $summaries = [new BankingOverviewSummary(0, 0, 0, 0, 'EUR')];
        }
        $cards = [];
        $multiple = count($summaries) > 1;
        foreach ($summaries as $summary) {
            $suffix = $multiple ? ' (' . $summary->getCurrency() . ')' : '';
            $cards[] = ['label' => 'Transacties' . $suffix, 'value' => (string)$summary->getCount(), 'meta' => 'binnen huidige filters', 'icon' => 'fas fa-arrow-right-arrow-left', 'tone' => 'accent'];
            $cards[] = ['label' => 'Ontvangen' . $suffix, 'value' => $this->money($summary->getIncomingMinor(), $summary->getCurrency()), 'meta' => 'bijgeschreven', 'icon' => 'fas fa-arrow-trend-up', 'tone' => 'success'];
            $cards[] = ['label' => 'Uitgegeven' . $suffix, 'value' => $this->money($summary->getOutgoingMinor(), $summary->getCurrency()), 'meta' => 'afgeschreven', 'icon' => 'fas fa-arrow-trend-down', 'tone' => 'warning'];
            $cards[] = ['label' => 'Nog afletteren' . $suffix, 'value' => (string)$summary->getUnmatchedCount(), 'meta' => 'openstaande transacties', 'icon' => 'fas fa-link', 'tone' => 'info'];
        }
        $accountOptions = ['' => 'Alle rekeningen'];
        $activeAccounts = [];
        foreach ($data['accounts'] as $account) {
            $label = $account->getName() . ' · ' . $account->getAccountReference() . ($account->isActive() ? '' : ' (inactief)');
            $accountOptions[$account->getPublicId()] = $label;
            if ($account->isActive()) {
                $activeAccounts[] = $account;
            }
        }
        return ['refreshAction' => $refreshAction, 'createAccountAction' => $createAccountAction, 'importAction' => $importAction, 'query' => $query, 'result' => $result, 'accounts' => $data['accounts'], 'activeAccounts' => $activeAccounts, 'accountOptions' => $accountOptions, 'years' => $data['years'], 'summaryCards' => $cards, 'pageSizes' => BankTransactionListQuery::pageSizes(), 'pages' => $this->pages($result), 'table' => ['id' => 'admin-banking-transactions', 'label' => 'Banktransacties', 'columns' => $columns, 'rows' => $rows, 'empty' => ['title' => 'Geen banktransacties gevonden', 'message' => 'Importeer een afschrift of pas de filters aan.', 'icon' => 'fas fa-building-columns']]];
    }

    private function status(string $status): array
    {
        if ($status === 'matched') {
            return ['label' => 'Afgeletterd', 'tone' => 'success'];
        }
        if ($status === 'ignored') {
            return ['label' => 'Genegeerd', 'tone' => 'neutral'];
        }
        return ['label' => 'Open', 'tone' => 'warning'];
    }

    private function money(int $minor, string $code): string
    {
        $prefix = $minor > 0 ? '+ ' : '';
        return $prefix . ($code === 'EUR' ? '€' : $code) . ' ' . (new Money($minor, new Currency($code)))->format();
    }

    private function date(string $date): string
    {
        $parts = explode('-', $date);
        return count($parts) === 3 ? $parts[2] . '-' . $parts[1] . '-' . $parts[0] : $date;
    }

    private function pages($result): array
    {
        $start = max(1, $result->getPage() - 2);
        $end = min($result->getTotalPages(), $start + 4);
        $start = max(1, $end - 4);
        return range($start, $end);
    }
}
