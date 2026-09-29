<?php
namespace Flexgrid\Modules\AdminBanking\Repository;use Flexgrid\Modules\AdminBanking\Entity\BankMatchProposalRecord;use Repository\Repository;class BankMatchProposalRecordRepository extends Repository{public function getEntity(){return new BankMatchProposalRecord();}}
