<?php

declare(strict_types=1);

namespace Flexgrid\Modules\AdminBanking\Contract;

use Flexgrid\Modules\AdminCore\ValueObject\TenantId;

interface BankExpenseCreatorInterface
{
    public function categories(TenantId $tenant): array;
    public function suppliers(TenantId $tenant): array;
    public function create(TenantId $tenant, string $transactionPublicId, array $fields, ?array $attachment): array;
}
