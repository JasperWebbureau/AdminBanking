<?php
declare(strict_types=1);
namespace Flexgrid\Modules\AdminBanking\Application\ReadModel;
use Flexgrid\Modules\AdminBanking\Domain\Model\BankImport;
final class BankImportResult{private$import,$existing;public function __construct(BankImport$import,bool$existing=false){$this->import=$import;$this->existing=$existing;}public function getImport():BankImport{return$this->import;}public function wasAlreadyImported():bool{return$this->existing;}}
