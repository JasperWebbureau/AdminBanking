<?php
declare(strict_types=1);
namespace Flexgrid\Modules\AdminBanking\Infrastructure\Import;
use Flexgrid\Modules\AdminBanking\Application\ReadModel\ParsedBankTransaction;use Flexgrid\Modules\AdminBanking\Contract\BankStatementParserInterface;
final class AbnTabStatementParser implements BankStatementParserInterface
{
    public function supports(string$format):bool{return strtolower(trim($format))==='abn_tab';}
    public function parse(string$content):array
    {
        if(trim($content)===''){throw new \InvalidArgumentException('Bankimport bevat geen regels.');}$lines=preg_split('/\r\n|\n|\r/',$content);$result=[];
        foreach($lines as$index=>$rawLine){$rawLine=trim((string)$rawLine);if($rawLine===''){continue;}$columns=explode("\t",$rawLine);if(count($columns)<8){throw new \InvalidArgumentException('Bankregel '.($index+1).' bevat minder dan acht kolommen.');}$account=strtoupper(preg_replace('/\s+/','',(string)$columns[0]));$currency=strtoupper(trim((string)$columns[1]));$booked=$this->date((string)$columns[2],$index+1);$amount=$this->amount((string)$columns[6],$index+1);$details=$this->details((string)$columns[7]);$rawHash=hash('sha256',$rawLine);$description=trim(($details['description']??'').' '.($details['narrative']??''));$reference=trim((string)($details['reference']??''));$result[]=new ParsedBankTransaction($account,'abn-tab:'.$rawHash,$currency,$booked,$amount,(string)($details['name']??''),(string)($details['iban']??''),(string)($details['bic']??''),$description!==''?$description:trim((string)$columns[7]),$reference,$rawHash,['line_number'=>$index+1,'transaction_type'=>(string)($details['transaction_type']??''),'reference'=>$reference]);}
        if($result===[]){throw new \InvalidArgumentException('Bankimport bevat geen bruikbare regels.');}return$result;
    }
    private function date(string$value,int$line):string{$value=trim($value);if(preg_match('/^(\d{4})(\d{2})(\d{2})$/D',$value,$m)!==1||!checkdate((int)$m[2],(int)$m[3],(int)$m[1])){throw new \InvalidArgumentException('Bankregel '.$line.' bevat een ongeldige datum.');}return$m[1].'-'.$m[2].'-'.$m[3];}
    private function amount(string$value,int$line):string{$value=str_replace(' ','',trim($value));if(strpos($value,',')!==false){$value=str_replace('.','',$value);$value=str_replace(',','.',$value);}if(preg_match('/^[+-]?[0-9]+(?:\.[0-9]{1,2})?$/D',$value)!==1){throw new \InvalidArgumentException('Bankregel '.$line.' bevat een ongeldig bedrag.');}return$value;}
    private function details(string$narrative):array
    {
        $result=['narrative'=>trim($narrative)];if(strpos($narrative,'/')!==false){$map=['TRTP'=>'transaction_type','IBAN'=>'iban','BIC'=>'bic','EREF'=>'reference','NAME'=>'name','REMI'=>'description'];$parts=explode('/',$narrative);for($i=0,$count=count($parts);$i<$count-1;$i++){if(isset($map[$parts[$i]])){$result[$map[$parts[$i]]]=trim($parts[$i+1]);}}}
        $labels=['IBAN'=>'iban','BIC'=>'bic','Naam'=>'name','Omschrijving'=>'description','Kenmerk'=>'reference','SEPA'=>'transaction_type'];foreach($labels as$label=>$key){if(preg_match('/'.preg_quote($label,'/').':\s*(.*?)(?=\s*(?:IBAN|BIC|Naam|Omschrijving|Kenmerk|Machtiging|SEPA):|$)/iu',$narrative,$match)===1){$result[$key]=trim($match[1]);}}
        if(isset($result['iban'])){$result['iban']=strtoupper(preg_replace('/\s+/','',$result['iban']));}if(isset($result['bic'])){$result['bic']=strtoupper(trim($result['bic']));}unset($result['narrative']);return$result;
    }
}
