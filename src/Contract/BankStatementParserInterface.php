<?php
declare(strict_types=1);
namespace Flexgrid\Modules\AdminBanking\Contract;
interface BankStatementParserInterface{public function supports(string$format):bool;/** @return \Flexgrid\Modules\AdminBanking\Application\ReadModel\ParsedBankTransaction[] */public function parse(string$content):array;}
