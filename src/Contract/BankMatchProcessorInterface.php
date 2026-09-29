<?php
declare(strict_types=1);
namespace Flexgrid\Modules\AdminBanking\Contract;
use Flexgrid\Modules\AdminBanking\Domain\Model\BankTransaction;use Flexgrid\Modules\AdminCore\ValueObject\TenantId;
interface BankMatchProcessorInterface{public function supports(string$targetType):bool;public function process(TenantId$tenant,BankTransaction$transaction,string$targetPublicId):void;}
