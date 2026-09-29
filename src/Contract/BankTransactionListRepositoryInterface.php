<?php
declare(strict_types=1);
namespace Flexgrid\Modules\AdminBanking\Contract;
use Flexgrid\Modules\AdminBanking\Application\Query\BankTransactionListQuery;use Flexgrid\Modules\AdminBanking\Application\ReadModel\BankTransactionListResult;use Flexgrid\Modules\AdminCore\ValueObject\TenantId;
interface BankTransactionListRepositoryInterface{public function search(TenantId$tenant,BankTransactionListQuery$query):BankTransactionListResult;public function getSummaries(TenantId$tenant,BankTransactionListQuery$query):array;public function getYears(TenantId$tenant):array;}
