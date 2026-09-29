<?php
declare(strict_types=1);

namespace Flexgrid\Modules\AdminBanking\Application\Query;

final class BankTransactionListQuery
{
    private const SORTS=['booked_on','counterparty','description','account','amount','status'];
    private const DIRECTIONS=['all','incoming','outgoing'];
    private const STATUSES=['','unmatched','matched','ignored'];
    private const PAGE_SIZES=[10,25,50];
    private$search,$accountPublicId,$status,$flow,$year,$sort,$direction,$page,$perPage;
    public function __construct(string$search='',string$accountPublicId='',string$status='',string$flow='all',?int$year=null,string$sort='booked_on',string$direction='desc',int$page=1,int$perPage=10)
    {
        $search=trim($search);$accountPublicId=trim($accountPublicId);$status=strtolower(trim($status));$flow=strtolower(trim($flow));$sort=strtolower(trim($sort));$direction=strtolower(trim($direction));
        if(strlen($search)>120||strlen($accountPublicId)>64){throw new \InvalidArgumentException('Ongeldige banktransactiefilters.');}
        if(!in_array($status,self::STATUSES,true)||!in_array($flow,self::DIRECTIONS,true)){throw new \InvalidArgumentException('Ongeldige banktransactiefilters.');}
        if($year!==null&&($year<1900||$year>2200)){throw new \InvalidArgumentException('Ongeldig boekingsjaar.');}
        if(!in_array($sort,self::SORTS,true)||!in_array($direction,['asc','desc'],true)){throw new \InvalidArgumentException('Ongeldige sortering.');}
        if($page<1||!in_array($perPage,self::PAGE_SIZES,true)){throw new \InvalidArgumentException('Ongeldige paginering.');}
        $this->search=$search;$this->accountPublicId=$accountPublicId;$this->status=$status;$this->flow=$flow;$this->year=$year;$this->sort=$sort;$this->direction=$direction;$this->page=$page;$this->perPage=$perPage;
    }
    public function getSearch():string{return$this->search;}public function getAccountPublicId():string{return$this->accountPublicId;}public function getStatus():string{return$this->status;}public function getFlow():string{return$this->flow;}public function getYear():?int{return$this->year;}public function getSort():string{return$this->sort;}public function getDirection():string{return$this->direction;}public function getPage():int{return$this->page;}public function getPerPage():int{return$this->perPage;}public static function sorts():array{return self::SORTS;}public static function pageSizes():array{return self::PAGE_SIZES;}
}
