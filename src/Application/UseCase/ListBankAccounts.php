<?php
declare(strict_types=1);
namespace Flexgrid\Modules\AdminBanking\Application\UseCase;
use Flexgrid\Modules\AdminBanking\Contract\BankAccountRepositoryInterface;use Flexgrid\Modules\AdminCore\Context\TenantContext;
final class ListBankAccounts{private$tenant,$accounts;public function __construct(TenantContext$tenant,BankAccountRepositoryInterface$accounts){$this->tenant=$tenant;$this->accounts=$accounts;}public function execute(bool$includeInactive=false):array{return$this->accounts->findAll($this->tenant->getTenantId(),$includeInactive);}}
