<?php

declare(strict_types=1);

namespace Flexgrid\Modules\AdminBanking\Application\ReadModel;

final class BankMatchTarget
{
    private $targetType;
    private $targetPublicId;
    private $title;
    private $party;
    private $reference;
    private $date;
    private $totalMinor;
    private $availableMinor;
    private $currency;
    private $selectable;
    private $warning;

    public function __construct(
        string $targetType,
        string $targetPublicId,
        string $title,
        string $party,
        string $reference,
        string $date,
        int $totalMinor,
        int $availableMinor,
        string $currency,
        bool $selectable,
        string $warning = ''
    ) {
        $targetType = strtolower(trim($targetType));
        $targetPublicId = trim($targetPublicId);
        $title = trim($title);
        $currency = strtoupper(trim($currency));
        $warning = trim($warning);

        if (preg_match('/^[a-z][a-z0-9_]{1,31}$/D', $targetType) !== 1
            || $targetPublicId === ''
            || strlen($targetPublicId) > 64
            || $title === ''
            || strlen($title) > 255
            || preg_match('/^[A-Z]{3}$/D', $currency) !== 1
            || $totalMinor < 0
            || $availableMinor < 0
            || strlen($warning) > 500
        ) {
            throw new \InvalidArgumentException('Ongeldig handmatig afletterdoel.');
        }

        $this->targetType = $targetType;
        $this->targetPublicId = $targetPublicId;
        $this->title = $title;
        $this->party = trim($party);
        $this->reference = trim($reference);
        $this->date = trim($date);
        $this->totalMinor = $totalMinor;
        $this->availableMinor = $availableMinor;
        $this->currency = $currency;
        $this->selectable = $selectable;
        $this->warning = $warning;
    }

    public function getTargetType(): string { return $this->targetType; }
    public function getTargetPublicId(): string { return $this->targetPublicId; }
    public function getTitle(): string { return $this->title; }
    public function getParty(): string { return $this->party; }
    public function getReference(): string { return $this->reference; }
    public function getDate(): string { return $this->date; }
    public function getTotalMinor(): int { return $this->totalMinor; }
    public function getAvailableMinor(): int { return $this->availableMinor; }
    public function getCurrency(): string { return $this->currency; }
    public function isSelectable(): bool { return $this->selectable; }
    public function getWarning(): string { return $this->warning; }
}
