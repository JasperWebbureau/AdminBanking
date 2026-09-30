<?php

declare(strict_types=1);

namespace Flexgrid\Modules\AdminBanking\Integration\Report;

use Flexgrid\Database\Connection;
use Flexgrid\Modules\AdminCore\ValueObject\TenantId;
use Flexgrid\Modules\AdminReport\Contract\ReportPeriodListProviderInterface;
use Flexgrid\Modules\AdminReport\Contract\ReportProviderInterface;
use Flexgrid\Modules\AdminReport\ValueObject\ReportPeriod;

final class ReportProvider implements ReportProviderInterface, ReportPeriodListProviderInterface
{
    private $tenant;
    private $connection;

    public function __construct(TenantId $tenant, ?\PDO $connection = null)
    {
        $this->tenant = $tenant;
        $this->connection = $connection ?: Connection::getConnections();
    }

    public function getReportContribution(ReportPeriod $period): array
    {
        $statement = $this->connection->prepare('SELECT DISTINCT LEFT(`booked_on`,4) report_year FROM `admin_bank_transaction` WHERE `tenant_id`=:tenant ORDER BY report_year DESC');
        $statement->execute([':tenant' => $this->tenant->toString()]);
        return ['currencies' => [], 'transactions' => [], 'years' => array_map('intval', $statement->fetchAll(\PDO::FETCH_COLUMN))];
    }

    public function getSummaryContribution(ReportPeriod $period): array
    {
        return $this->getReportContribution($period);
    }

    public function getPeriodList(ReportPeriod $period, string $kind): array
    {
        if ($kind !== 'banking') {
            return [];
        }
        $statement = $this->connection->prepare("SELECT t.`public_id`,t.`booked_on`,t.`amount_minor`,t.`currency`,t.`counterparty_snapshot`,t.`description`,t.`reference`,t.`status`,COALESCE(a.`name`,'Onbekende rekening') account_name FROM `admin_bank_transaction` t LEFT JOIN `admin_bank_account` a ON a.`tenant_id`=t.`tenant_id` AND a.`public_id`=t.`bank_account_public_id` WHERE t.`tenant_id`=:tenant AND t.`booked_on` BETWEEN :start AND :end ORDER BY t.`booked_on` DESC,t.`id` DESC");
        $statement->execute([':tenant' => $this->tenant->toString(), ':start' => $period->getStartDate(), ':end' => $period->getEndDate()]);
        $rows = [];
        foreach ($statement->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $counterparty = json_decode((string)$row['counterparty_snapshot'], true);
            $name = is_array($counterparty) ? trim((string)($counterparty['name'] ?? '')) : '';
            $statuses = [
                'matched' => ['Afgeletterd', 'success'],
                'ignored' => ['Genegeerd', 'neutral'],
                'unmatched' => ['Open', 'warning'],
            ];
            $status = $statuses[(string)$row['status']] ?? ['Onbekend', 'neutral'];
            $rows[] = [
                'id' => 'bank-' . $row['public_id'],
                'date' => (string)$row['booked_on'],
                'title' => $name !== '' ? $name : 'Onbekende tegenpartij',
                'description' => (string)$row['description'],
                'type' => $status[0],
                'tone' => $status[1],
                'amount_minor' => (int)$row['amount_minor'],
                'tax_minor' => 0,
                'currency' => (string)$row['currency'],
                'href' => $this->url((string)$row['public_id']),
                'account' => (string)$row['account_name'],
                'reference' => (string)($row['reference'] ?? ''),
            ];
        }
        return $rows;
    }

    private function url(string $publicId): string
    {
        $domain = defined('__DOMAIN__') ? rtrim((string)constant('__DOMAIN__'), '/') : '';
        return $domain . '/Flexgrid/AdminBanking/match/' . rawurlencode($publicId);
    }
}
