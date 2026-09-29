<?php
namespace Flexgrid\Modules\AdminBanking\Repository;use Flexgrid\Modules\AdminBanking\Entity\BankAccountRecord;use Repository\Repository;class BankAccountRecordRepository extends Repository{public function getEntity(){return new BankAccountRecord();}}
