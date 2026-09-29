<?php
declare(strict_types=1);
namespace Flexgrid\Modules\AdminBanking\Application\ReadModel;
final class BankingOverviewSummary
{
    private$count,$incomingMinor,$outgoingMinor,$unmatchedCount,$currency;
    public function __construct(int$count,int$incomingMinor,int$outgoingMinor,int$unmatchedCount,string$currency){if($count<0||$incomingMinor<0||$outgoingMinor<0||$unmatchedCount<0){throw new \InvalidArgumentException('Ongeldige banksamenvatting.');}$this->count=$count;$this->incomingMinor=$incomingMinor;$this->outgoingMinor=$outgoingMinor;$this->unmatchedCount=$unmatchedCount;$this->currency=$currency;}
    public function getCount():int{return$this->count;}public function getIncomingMinor():int{return$this->incomingMinor;}public function getOutgoingMinor():int{return$this->outgoingMinor;}public function getUnmatchedCount():int{return$this->unmatchedCount;}public function getCurrency():string{return$this->currency;}
}
