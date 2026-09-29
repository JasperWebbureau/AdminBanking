<?php

declare(strict_types=1);

namespace Flexgrid\Modules\AdminBanking\Contract;

use Flexgrid\Modules\AdminBanking\Application\ReadModel\BankMatchContext;
use Flexgrid\Modules\AdminBanking\Application\ReadModel\BankMatchTarget;
use Flexgrid\Modules\AdminCore\ValueObject\TenantId;

interface BankMatchTargetProviderInterface
{
    public function supports(string $targetType): bool;

    /** @return BankMatchTarget[] */
    public function search(TenantId $tenant, BankMatchContext $context, string $query, int $limit): array;

    public function find(TenantId $tenant, BankMatchContext $context, string $targetPublicId): ?BankMatchTarget;
}
