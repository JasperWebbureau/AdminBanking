<?php

declare(strict_types=1);

namespace Flexgrid\Modules\AdminBanking\Integration\Dashboard;

use Flexgrid\Modules\AdminDashboard\Contract\DashboardProviderInterface;
use Flexgrid\Modules\AdminDashboard\Provider\AbstractPdoDashboardProvider;

final class DashboardProvider extends AbstractPdoDashboardProvider implements DashboardProviderInterface
{
    public function getDashboardContribution(): array
    {
        $tenant = $this->tenant->toString();
        $countStatement = $this->connection->prepare("SELECT COUNT(*) FROM `admin_bank_transaction` WHERE `tenant_id`=:tenant AND `status`='unmatched'");
        $countStatement->execute([':tenant' => $tenant]);
        $unmatchedCount = (int)$countStatement->fetchColumn();

        $recent = $this->connection->prepare("SELECT `public_id`,`amount_minor`,`currency`,`counterparty_snapshot`,`description`,`status`,`updated_at` FROM `admin_bank_transaction` WHERE `tenant_id`=:tenant ORDER BY `updated_at` DESC,`id` DESC LIMIT 2");
        $recent->execute([':tenant' => $tenant]);
        $activities = [];
        foreach ($recent->fetchAll(\PDO::FETCH_ASSOC) as $transaction) {
            $name = $this->snapshotName($transaction['counterparty_snapshot'], 'Banktransactie');
            $activities[] = ['id' => 'bank-' . $transaction['public_id'], 'title' => $transaction['status'] === 'matched' ? 'Bankregel afgeletterd' : 'Bankregel geïmporteerd', 'description' => $name . ' · ' . $this->money((int)$transaction['amount_minor'], (string)$transaction['currency']), 'icon' => 'fas fa-building-columns', 'tone' => $transaction['status'] === 'matched' ? 'success' : 'info', 'href' => $this->url('AdminBanking', 'match/' . rawurlencode((string)$transaction['public_id'])), 'timestamp' => (int)$transaction['updated_at'], 'time' => $this->activityTime((int)$transaction['updated_at'])];
        }

        return [
            'activities' => $activities,
            'quick_actions' => [['id' => 'open-banking', 'label' => 'Bankafschrift importeren', 'icon' => 'fas fa-file-import', 'tone' => 'success', 'href' => $this->url('AdminBanking', 'transactions'), 'priority' => 50]],
            'attention' => $unmatchedCount > 0 ? [['id' => 'unmatched-bank-transactions', 'title' => $unmatchedCount . ' bankregel' . ($unmatchedCount === 1 ? '' : 's') . ' af te letteren', 'description' => 'Controleer voorstellen of kies handmatig een doel.', 'icon' => 'fas fa-link', 'tone' => 'warning', 'badge' => 'Afletteren', 'badge_tone' => 'warning', 'href' => $this->url('AdminBanking', 'transactions?status=unmatched'), 'priority' => 5]] : [],
            'modules' => [['id' => 'admin-banking', 'title' => 'Bankieren', 'description' => $unmatchedCount . ' nog af te letteren', 'icon' => 'fas fa-building-columns', 'status' => 'Actief', 'tone' => $unmatchedCount > 0 ? 'warning' : 'success', 'href' => $this->url('AdminBanking', 'transactions'), 'priority' => 60]],
        ];
    }
}
