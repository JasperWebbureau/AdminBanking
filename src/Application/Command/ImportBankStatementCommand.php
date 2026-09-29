<?php
declare(strict_types=1);
namespace Flexgrid\Modules\AdminBanking\Application\Command;
final class ImportBankStatementCommand{private$bankAccountPublicId,$format,$content,$fileName;public function __construct(string$account,string$format,string$content,string$fileName=''){$this->bankAccountPublicId=trim($account);$this->format=strtolower(trim($format));$this->content=$content;$this->fileName=trim($fileName);}public function getBankAccountPublicId():string{return$this->bankAccountPublicId;}public function getFormat():string{return$this->format;}public function getContent():string{return$this->content;}public function getFileName():string{return$this->fileName;}}
