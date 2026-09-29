<?php
declare(strict_types=1);
namespace Flexgrid\Modules\AdminBanking\Contract;
use Flexgrid\Modules\AdminBanking\Domain\Model\BankMatchProposal;use Flexgrid\Modules\AdminCore\ValueObject\TenantId;
interface BankMatchProposalRepositoryInterface{/** @return BankMatchProposal[] */public function findByTransaction(TenantId$tenant,string$transactionPublicId):array;/** @param BankMatchProposal[] $proposals */public function replaceForTransaction(TenantId$tenant,string$transactionPublicId,array$proposals):void;}
