<?php
declare(strict_types=1);
namespace Flexgrid\Modules\AdminBanking\Application\UseCase;
use Flexgrid\Modules\AdminBanking\Contract\BankTransactionRepositoryInterface;use Flexgrid\Modules\AdminBanking\Domain\Model\BankTransaction;use Flexgrid\Modules\AdminCore\Context\TenantContext;use Flexgrid\Modules\AdminCore\Contract\TransactionManagerInterface;use Flexgrid\Utils\_Time;
final class IgnoreBankTransaction
{
    private$tenant,$transactionManager,$transactions,$clock;public function __construct(TenantContext$tenant,TransactionManagerInterface$transactionManager,BankTransactionRepositoryInterface$transactions,_Time$clock){$this->tenant=$tenant;$this->transactionManager=$transactionManager;$this->transactions=$transactions;$this->clock=$clock;}
    public function execute(string$publicId):BankTransaction{$tenant=$this->tenant->getTenantId();return$this->transactionManager->transactional(function()use($tenant,$publicId):BankTransaction{$transaction=$this->transactions->findByPublicIdForUpdate($tenant,trim($publicId));if($transaction===null){throw new \DomainException('Banktransactie is niet gevonden.');}if($transaction->getStatus()->getValue()==='ignored'){return$transaction;}$transaction->ignore();$this->transactions->updateMatchState($transaction,(int)$this->clock->get());return$transaction;});}
}
