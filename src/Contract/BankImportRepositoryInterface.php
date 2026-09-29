<?php
declare(strict_types=1);
namespace Flexgrid\Modules\AdminBanking\Contract;
use Flexgrid\Modules\AdminBanking\Domain\Model\BankImport;use Flexgrid\Modules\AdminCore\ValueObject\TenantId;
interface BankImportRepositoryInterface{public function insert(BankImport$import):void;public function findByChecksum(TenantId$tenant,string$accountPublicId,string$format,string$checksum):?BankImport;}
