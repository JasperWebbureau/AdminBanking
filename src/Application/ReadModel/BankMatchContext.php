<?php
declare(strict_types=1);
namespace Flexgrid\Modules\AdminBanking\Application\ReadModel;
final class BankMatchContext
{
    private$transactionPublicId,$amountMinor,$currency,$bookedOn,$counterpartyName,$counterpartyIban,$description,$reference;
    public function __construct(string$transactionPublicId,int$amountMinor,string$currency,string$bookedOn,string$counterpartyName,string$counterpartyIban,string$description,string$reference){$transactionPublicId=trim($transactionPublicId);$currency=strtoupper(trim($currency));if($transactionPublicId===''||strlen($transactionPublicId)>64||$amountMinor===0||preg_match('/^[A-Z]{3}$/D',$currency)!==1){throw new \InvalidArgumentException('Ongeldige bankmatchcontext.');}$this->transactionPublicId=$transactionPublicId;$this->amountMinor=$amountMinor;$this->currency=$currency;$this->bookedOn=$bookedOn;$this->counterpartyName=trim($counterpartyName);$this->counterpartyIban=trim($counterpartyIban);$this->description=trim($description);$this->reference=trim($reference);}
    public function getTransactionPublicId():string{return$this->transactionPublicId;}public function getAmountMinor():int{return$this->amountMinor;}public function getCurrency():string{return$this->currency;}public function getBookedOn():string{return$this->bookedOn;}public function getCounterpartyName():string{return$this->counterpartyName;}public function getCounterpartyIban():string{return$this->counterpartyIban;}public function getDescription():string{return$this->description;}public function getReference():string{return$this->reference;}public function getSearchText():string{return trim($this->reference.' '.$this->description.' '.$this->counterpartyName);}
}
