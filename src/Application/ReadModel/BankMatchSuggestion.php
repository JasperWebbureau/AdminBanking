<?php
declare(strict_types=1);
namespace Flexgrid\Modules\AdminBanking\Application\ReadModel;
final class BankMatchSuggestion
{
    private$targetType,$targetPublicId,$confidenceBasisPoints,$reason;
    public function __construct(string$targetType,string$targetPublicId,int$confidenceBasisPoints,string$reason){$targetType=strtolower(trim($targetType));$targetPublicId=trim($targetPublicId);$reason=trim($reason);if(preg_match('/^[a-z][a-z0-9_]{1,31}$/D',$targetType)!==1||$targetPublicId===''||strlen($targetPublicId)>64||$confidenceBasisPoints<0||$confidenceBasisPoints>10000||$reason===''||strlen($reason)>500){throw new \InvalidArgumentException('Ongeldig bankmatchvoorstel.');}$this->targetType=$targetType;$this->targetPublicId=$targetPublicId;$this->confidenceBasisPoints=$confidenceBasisPoints;$this->reason=$reason;}
    public function getTargetType():string{return$this->targetType;}public function getTargetPublicId():string{return$this->targetPublicId;}public function getConfidenceBasisPoints():int{return$this->confidenceBasisPoints;}public function getReason():string{return$this->reason;}
}
