<?php
namespace Flexgrid\Modules\AdminBanking\Repository;use Flexgrid\Modules\AdminBanking\Entity\BankImportRecord;use Repository\Repository;class BankImportRecordRepository extends Repository{public function getEntity(){return new BankImportRecord();}}
