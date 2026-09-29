<?php

declare(strict_types=1);

namespace Flexgrid\Modules\AdminBanking\Service;

use Flexgrid\Modules\AdminCore\ValueObject\Currency;
use Flexgrid\Modules\AdminCore\ValueObject\Money;

final class BankMatchPresenter
{
    public function present(
        array $data,
        string $generateAction,
        string $acceptAction,
        string $ignoreAction,
        string $searchAction = '',
        string $acceptManualAction = ''
    ): array {
        $transaction = $data['transaction'];
        $currency = $transaction->getAmount()->getCurrency();
        $proposals = [];
        foreach ($data['proposals'] as $proposal) {
            $proposals[] = [
                'public_id' => $proposal->getPublicId(),
                'target_type' => $proposal->getTargetType(),
                'target_label' => $this->targetLabel($proposal->getTargetType()),
                'target_public_id' => $proposal->getTargetPublicId(),
                'confidence' => number_format($proposal->getConfidenceBasisPoints() / 100, 0, ',', '.') . '%',
                'tone' => $proposal->getConfidenceBasisPoints() >= 9000 ? 'success' : ($proposal->getConfidenceBasisPoints() >= 7000 ? 'info' : 'warning'),
                'reason' => $proposal->getReason(),
                'selected' => $transaction->getMatchedTargetType() === $proposal->getTargetType()
                    && $transaction->getMatchedTargetPublicId() === $proposal->getTargetPublicId(),
            ];
        }

        return [
            'transaction' => $transaction,
            'publicId' => $transaction->getPublicId(),
            'date' => $this->date($transaction->getBookedOn()->getValue()),
            'amount' => ($currency->getCode() === 'EUR' ? '€' : $currency->getCode()) . ' ' . $transaction->getAmount()->format(),
            'counterparty' => $transaction->getCounterparty()->getName() ?: 'Onbekende tegenpartij',
            'counterpartyIban' => $transaction->getCounterparty()->getIban(),
            'description' => $transaction->getDescription(),
            'reference' => $transaction->getReference(),
            'status' => $transaction->getStatus()->getValue(),
            'statusLabel' => $this->status($transaction->getStatus()->getValue()),
            'proposals' => $proposals,
            'generateAction' => $generateAction,
            'acceptAction' => $acceptAction,
            'ignoreAction' => $ignoreAction,
            'searchAction' => $searchAction,
            'acceptManualAction' => $acceptManualAction,
        ];
    }

    public function presentTargets(array $data, string $acceptAction): array
    {
        $targets = [];
        foreach ($data['targets'] as $target) {
            $type = $target->getTargetType();
            $targets[] = [
                'target_type' => $type,
                'target_label' => $this->targetLabel($type),
                'target_public_id' => $target->getTargetPublicId(),
                'title' => $target->getTitle(),
                'party' => $target->getParty(),
                'reference' => $target->getReference(),
                'date' => $this->date($target->getDate()),
                'total' => $this->money($target->getTotalMinor(), $target->getCurrency()),
                'available' => $this->money($target->getAvailableMinor(), $target->getCurrency()),
                'available_label' => $type === 'invoice' ? 'Openstaand' : 'Brutobedrag',
                'show_available' => $type === 'invoice',
                'selectable' => $target->isSelectable(),
                'warning' => $target->getWarning(),
            ];
        }
        return [
            'publicId' => $data['transaction']->getPublicId(),
            'query' => $data['query'],
            'targets' => $targets,
            'acceptManualAction' => $acceptAction,
        ];
    }

    private function targetLabel(string $type): string
    {
        return $type === 'invoice' ? 'Factuur' : ($type === 'expense' ? 'Uitgave' : 'Doel');
    }

    private function money(int $minor, string $currency): string
    {
        $money = new Money($minor, new Currency($currency));
        return ($currency === 'EUR' ? '€' : $currency) . ' ' . $money->format();
    }

    private function status(string $status): string
    {
        return $status === 'matched' ? 'Afgeletterd' : ($status === 'ignored' ? 'Genegeerd' : 'Open');
    }

    private function date(string $date): string
    {
        $parts = explode('-', $date);
        return count($parts) === 3 ? $parts[2] . '-' . $parts[1] . '-' . $parts[0] : $date;
    }
}
