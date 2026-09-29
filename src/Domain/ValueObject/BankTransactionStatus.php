<?php
declare(strict_types=1);
namespace Flexgrid\Modules\AdminBanking\Domain\ValueObject;
final class BankTransactionStatus{public const UNMATCHED='unmatched';public const MATCHED='matched';public const IGNORED='ignored';private$value;public function __construct(string$value){$value=strtolower(trim($value));if(!in_array($value,self::values(),true)){throw new \InvalidArgumentException('Ongeldige banktransactiestatus.');}$this->value=$value;}public function getValue():string{return$this->value;}public static function values():array{return[self::UNMATCHED,self::MATCHED,self::IGNORED];}}
