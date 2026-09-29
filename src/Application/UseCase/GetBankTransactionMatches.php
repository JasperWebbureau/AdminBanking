<?php
declare(strict_types=1);
namespace Flexgrid\Modules\AdminBanking\Application\UseCase;
use Flexgrid\Modules\AdminBanking\Contract\BankMatchProposalRepositoryInterface;use Flexgrid\Modules\AdminBanking\Contract\BankTransactionRepositoryInterface;use Flexgrid\Modules\AdminCore\Context\TenantContext;
final class GetBankTransactionMatches{private$tenant,$transactions,$proposals;public function __construct(TenantContext$tenant,BankTransactionRepositoryInterface$transactions,BankMatchProposalRepositoryInterface$proposals){$this->tenant=$tenant;$this->transactions=$transactions;$this->proposals=$proposals;}public function execute(string$publicId):array{$tenant=$this->tenant->getTenantId();$transaction=$this->transactions->findByPublicId($tenant,trim($publicId));if($transaction===null){throw new \DomainException('Banktransactie is niet gevonden.');}return['transaction'=>$transaction,'proposals'=>$this->proposals->findByTransaction($tenant,$transaction->getPublicId())];}}
