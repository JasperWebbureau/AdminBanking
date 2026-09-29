<?php
declare(strict_types=1);
namespace Flexgrid\Modules\AdminBanking\Contract;
use Flexgrid\Modules\AdminBanking\Application\ReadModel\BankMatchContext;use Flexgrid\Modules\AdminCore\ValueObject\TenantId;
interface BankMatchCandidateProviderInterface{/** @return \Flexgrid\Modules\AdminBanking\Application\ReadModel\BankMatchSuggestion[] */public function suggest(TenantId$tenant,BankMatchContext$context):array;}
