<?php
declare(strict_types=1);

namespace Flexgrid\Modules\AdminBanking\Infrastructure\Persistence;

use Flexgrid\Modules\AdminBanking\Application\Query\BankTransactionListQuery;
use Flexgrid\Modules\AdminBanking\Application\ReadModel\BankingOverviewSummary;
use Flexgrid\Modules\AdminBanking\Application\ReadModel\BankTransactionListItem;
use Flexgrid\Modules\AdminBanking\Application\ReadModel\BankTransactionListResult;
use Flexgrid\Modules\AdminBanking\Contract\BankTransactionListRepositoryInterface;
use Flexgrid\Modules\AdminCore\ValueObject\TenantId;

final class PdoBankTransactionListRepository implements BankTransactionListRepositoryInterface
{
    private$connection;
    public function __construct(\PDO$connection){$this->connection=$connection;}
    public function search(TenantId$tenant,BankTransactionListQuery$query):BankTransactionListResult
    {
        list($where,$parameters)=$this->where($tenant,$query);$sort=['booked_on'=>'t.`booked_on`','counterparty'=>'counterparty_name','description'=>'t.`description`','account'=>'account_name','amount'=>'t.`amount_minor`','status'=>'t.`status`'][$query->getSort()];
        $join=' LEFT JOIN `admin_bank_account` a ON a.`tenant_id`=t.`tenant_id` AND a.`public_id`=t.`bank_account_public_id` ';
        $count=$this->connection->prepare('SELECT COUNT(*) FROM `admin_bank_transaction` t'.$join.$where);$count->execute($parameters);$total=(int)$count->fetchColumn();
        $sql='SELECT t.*,COALESCE(a.`name`,\'Onbekende rekening\') AS account_name,COALESCE(JSON_UNQUOTE(JSON_EXTRACT(t.`counterparty_snapshot`,\'$.name\')),\'\') AS counterparty_name,COALESCE(JSON_UNQUOTE(JSON_EXTRACT(t.`counterparty_snapshot`,\'$.iban\')),\'\') AS counterparty_iban FROM `admin_bank_transaction` t'.$join.$where.' ORDER BY '.$sort.' '.strtoupper($query->getDirection()).',t.`id` DESC LIMIT '.(int)$query->getPerPage().' OFFSET '.(int)(($query->getPage()-1)*$query->getPerPage());
        $statement=$this->connection->prepare($sql);$statement->execute($parameters);$items=[];foreach($statement->fetchAll(\PDO::FETCH_ASSOC)as$row){$items[]=new BankTransactionListItem((string)$row['public_id'],(string)$row['booked_on'],(string)$row['account_name'],(string)$row['counterparty_name'],(string)$row['counterparty_iban'],(string)$row['description'],(string)($row['reference']??''),(int)$row['amount_minor'],(string)$row['currency'],(string)$row['status']);}
        return new BankTransactionListResult($items,$total,$query->getPage(),$query->getPerPage());
    }
    public function getSummaries(TenantId$tenant,BankTransactionListQuery$query):array
    {
        list($where,$parameters)=$this->where($tenant,$query);$statement=$this->connection->prepare('SELECT t.`currency`,COUNT(*) AS transaction_count,COALESCE(SUM(CASE WHEN t.`amount_minor`>0 THEN t.`amount_minor` ELSE 0 END),0) AS incoming_minor,COALESCE(SUM(CASE WHEN t.`amount_minor`<0 THEN -t.`amount_minor` ELSE 0 END),0) AS outgoing_minor,COALESCE(SUM(CASE WHEN t.`status`=\'unmatched\' THEN 1 ELSE 0 END),0) AS unmatched_count FROM `admin_bank_transaction` t LEFT JOIN `admin_bank_account` a ON a.`tenant_id`=t.`tenant_id` AND a.`public_id`=t.`bank_account_public_id` '.$where.' GROUP BY t.`currency` ORDER BY t.`currency`');$statement->execute($parameters);$result=[];foreach($statement->fetchAll(\PDO::FETCH_ASSOC)as$row){$result[]=new BankingOverviewSummary((int)$row['transaction_count'],(int)$row['incoming_minor'],(int)$row['outgoing_minor'],(int)$row['unmatched_count'],(string)$row['currency']);}return$result;
    }
    public function getYears(TenantId$tenant):array{$statement=$this->connection->prepare('SELECT DISTINCT LEFT(`booked_on`,4) AS booked_year FROM `admin_bank_transaction` WHERE `tenant_id`=:tenant ORDER BY booked_year DESC');$statement->execute([':tenant'=>$tenant->toString()]);return array_map('intval',$statement->fetchAll(\PDO::FETCH_COLUMN));}
    private function where(TenantId$tenant,BankTransactionListQuery$query):array
    {
        $parts=['t.`tenant_id`=:tenant'];$parameters=[':tenant'=>$tenant->toString()];
        if($query->getSearch()!==''){$parts[]="CONCAT_WS(' ',t.`description`,COALESCE(t.`reference`,''),t.`counterparty_snapshot`,COALESCE(a.`name`,'')) LIKE :search";$parameters[':search']='%'.$query->getSearch().'%';}
        if($query->getAccountPublicId()!==''){$parts[]='t.`bank_account_public_id`=:account';$parameters[':account']=$query->getAccountPublicId();}
        if($query->getStatus()!==''){$parts[]='t.`status`=:status';$parameters[':status']=$query->getStatus();}
        if($query->getFlow()==='incoming'){$parts[]='t.`amount_minor`>0';}elseif($query->getFlow()==='outgoing'){$parts[]='t.`amount_minor`<0';}
        if($query->getYear()!==null){$parts[]='t.`booked_on` LIKE :year';$parameters[':year']=(string)$query->getYear().'-%';}
        return['WHERE '.implode(' AND ',$parts),$parameters];
    }
}
