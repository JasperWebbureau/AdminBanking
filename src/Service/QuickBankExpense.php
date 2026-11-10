<?php

declare(strict_types=1);

namespace Flexgrid\Modules\AdminBanking\Service;

use Flexgrid\Modules\AdminBanking\Contract\BankExpenseCreatorInterface;
use Flexgrid\Modules\AdminBanking\Domain\Model\BankTransaction;
use Flexgrid\Modules\AdminCore\ValueObject\TenantId;

final class QuickBankExpense
{
    private $creator;
    private $tenant;

    public function __construct(BankExpenseCreatorInterface $creator, TenantId $tenant)
    {
        $this->creator = $creator;
        $this->tenant = $tenant;
    }

    public function category(): ?array
    {
        $categories = array_values(array_filter($this->creator->categories($this->tenant), static function (array $category): bool {
            return ($category['reporting_type'] ?? '') === 'operating_cost';
        }));
        usort($categories, static function (array $left, array $right): int {
            $general = ['overig', 'overige', 'algemeen', 'general', 'other'];
            $leftGeneral = in_array(strtolower((string)($left['code'] ?? '')), $general, true);
            $rightGeneral = in_array(strtolower((string)($right['code'] ?? '')), $general, true);
            return ($rightGeneral <=> $leftGeneral)
                ?: strcasecmp((string)$left['name'], (string)$right['name'])
                ?: strcmp((string)$left['public_id'], (string)$right['public_id']);
        });
        return $categories[0] ?? null;
    }

    public function execute(BankTransaction $bank): array
    {
        if (!$bank->getTenantId()->equals($this->tenant) || $bank->getStatus()->getValue() !== 'unmatched' || $bank->getAmount()->getMinorUnits() >= 0) {
            throw new \DomainException('Alleen een open afschrijving kan een uitgave worden.');
        }
        $category = $this->category();
        if ($category === null) { throw new \DomainException('Maak eerst een actieve categorie voor zakelijke kosten aan.'); }
        $name = trim($bank->getCounterparty()->getName());
        $description = trim($bank->getDescription());
        if (strlen($bank->getReference()) > 128) { $description .= "\nBankreferentie: " . $bank->getReference(); }
        return $this->creator->create($this->tenant, $bank->getPublicId(), [
            'category_public_id' => $category['public_id'],
            'title' => mb_strcut($name !== '' ? $name : ($description !== '' ? $description : 'Bankuitgave'), 0, 255, 'UTF-8'),
            'vat_rate' => '21', 'supplier_name' => $name, 'description' => $description,
            'reference' => mb_strcut($bank->getReference(), 0, 128, 'UTF-8'),
        ], null);
    }
}
