<?php
declare(strict_types=1);
namespace Flexgrid\Modules\AdminBanking\Application\ReadModel;
final class BankTransactionListItem
{
    private$publicId,$bookedOn,$accountName,$counterpartyName,$counterpartyIban,$description,$reference,$amountMinor,$currency,$status;
    public function __construct(string$publicId,string$bookedOn,string$accountName,string$counterpartyName,string$counterpartyIban,string$description,string$reference,int$amountMinor,string$currency,string$status){$this->publicId=$publicId;$this->bookedOn=$bookedOn;$this->accountName=$accountName;$this->counterpartyName=$counterpartyName;$this->counterpartyIban=$counterpartyIban;$this->description=$description;$this->reference=$reference;$this->amountMinor=$amountMinor;$this->currency=$currency;$this->status=$status;}
    public function getPublicId():string{return$this->publicId;}public function getBookedOn():string{return$this->bookedOn;}public function getAccountName():string{return$this->accountName;}public function getCounterpartyName():string{return$this->counterpartyName;}public function getCounterpartyIban():string{return$this->counterpartyIban;}public function getDescription():string{return$this->description;}public function getReference():string{return$this->reference;}public function getAmountMinor():int{return$this->amountMinor;}public function getCurrency():string{return$this->currency;}public function getStatus():string{return$this->status;}
}
