<?php
declare(strict_types=1);
namespace Flexgrid\Modules\AdminBanking\Contract;
use Flexgrid\Modules\AdminBanking\Domain\Model\BankAccount;use Flexgrid\Modules\AdminCore\ValueObject\TenantId;
interface BankAccountRepositoryInterface{public function insert(BankAccount$account,int$createdAt):void;public function findByPublicId(TenantId$tenant,string$publicId):?BankAccount;public function findByAccountReference(TenantId$tenant,string$reference):?BankAccount;/** @return BankAccount[] */public function findAll(TenantId$tenant,bool$includeInactive=false):array;}
