<?php
namespace Flexgrid\Modules\AdminBanking\Repository;use Flexgrid\Modules\AdminBanking\Entity\BankTransactionRecord;use Repository\Repository;class BankTransactionRecordRepository extends Repository{public function getEntity(){return new BankTransactionRecord();}}
