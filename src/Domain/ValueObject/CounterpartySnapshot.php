<?php
declare(strict_types=1);
namespace Flexgrid\Modules\AdminBanking\Domain\ValueObject;
final class CounterpartySnapshot
{
    private const VERSION=1;private$name,$iban,$bic;
    public function __construct(string$name='',string$iban='',string$bic=''){$name=trim($name);$iban=strtoupper(preg_replace('/\s+/','',$iban));$bic=strtoupper(trim($bic));if(strlen($name)>255){throw new \InvalidArgumentException('Naam van de tegenpartij is maximaal 255 tekens.');}if($iban!==''&&!self::validIban($iban)){throw new \InvalidArgumentException('Tegenpartij heeft een ongeldig IBAN.');}if($bic!==''&&preg_match('/^[A-Z0-9]{8}([A-Z0-9]{3})?$/D',$bic)!==1){throw new \InvalidArgumentException('Tegenpartij heeft een ongeldige BIC.');}$this->name=$name;$this->iban=$iban;$this->bic=$bic;}
    public function toArray():array{return['schema_version'=>self::VERSION,'name'=>$this->name,'iban'=>$this->iban,'bic'=>$this->bic];}public static function fromArray(array$data):self{if((int)($data['schema_version']??0)!==self::VERSION){throw new \UnexpectedValueException('Onbekende tegenpartijsnapshotversie.');}return new self((string)($data['name']??''),(string)($data['iban']??''),(string)($data['bic']??''));}public function getName():string{return$this->name;}public function getIban():string{return$this->iban;}public function getBic():string{return$this->bic;}
    public static function validIban(string$iban):bool{$iban=strtoupper(preg_replace('/\s+/','',$iban));if(preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{11,30}$/D',$iban)!==1){return false;}$rearranged=substr($iban,4).substr($iban,0,4);$numeric='';for($i=0,$length=strlen($rearranged);$i<$length;$i++){$character=$rearranged[$i];$numeric.=ctype_alpha($character)?(string)(ord($character)-55):$character;}$remainder=0;for($i=0,$length=strlen($numeric);$i<$length;$i++){$remainder=($remainder*10+(int)$numeric[$i])%97;}return$remainder===1;}
}
