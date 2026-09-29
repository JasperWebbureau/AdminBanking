<?php
declare(strict_types=1);
namespace Flexgrid\Modules\AdminBanking\Contract;
use Flexgrid\Modules\AdminBanking\Domain\Model\BankTransaction;use Flexgrid\Modules\AdminCore\ValueObject\TenantId;
interface BankTransactionRepositoryInterface{public function insert(BankTransaction$transaction,int$createdAt):void;public function findByExternalId(TenantId$tenant,string$accountPublicId,string$externalId):?BankTransaction;public function findByPublicId(TenantId$tenant,string$publicId):?BankTransaction;public function findByPublicIdForUpdate(TenantId$tenant,string$publicId):?BankTransaction;public function updateMatchState(BankTransaction$transaction,int$updatedAt):void;}
