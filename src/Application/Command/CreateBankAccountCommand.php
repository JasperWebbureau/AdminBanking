<?php
declare(strict_types=1);
namespace Flexgrid\Modules\AdminBanking\Application\Command;
final class CreateBankAccountCommand{private$name,$accountReference,$currency,$iban;public function __construct(string$name,string$accountReference,string$currency='EUR',string$iban=''){$this->name=trim($name);$this->accountReference=trim($accountReference);$this->currency=trim($currency);$this->iban=trim($iban);}public function getName():string{return$this->name;}public function getAccountReference():string{return$this->accountReference;}public function getCurrency():string{return$this->currency;}public function getIban():string{return$this->iban;}}
