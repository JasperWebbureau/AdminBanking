<?php
declare(strict_types=1);

namespace Flexgrid\Modules\AdminBanking\Controller;

use Flexgrid\Event\AjaxEvent;
use Flexgrid\Modules\AdminBanking\Application\Command\CreateBankAccountCommand;
use Flexgrid\Modules\AdminBanking\Application\Command\ImportBankStatementCommand;
use Flexgrid\Modules\AdminBanking\Application\Query\BankTransactionListQuery;
use Flexgrid\Modules\AdminBanking\Domain\ValueObject\BankTransactionStatus;
use Flexgrid\Modules\AdminBanking\Service\AdminBankingFactory;
use Flexgrid\Response\AjaxResponse;
use Flexgrid\Response\PageResponse;
use Flexgrid\Response\TemplateResponse;
use Flexgrid\Utils\Request\Request;

/** @FG\Controller [name=AdminBanking,type=Module,icon=fas fa-building-columns,level=2,administrationPanel=true,administrationLabel=Bankieren,administrationRoute=transactions,administrationPriority=60] */
final class AdminBankingController
{
    public function index(){return$this->transactions();}
    public function transactions(){appendIconAndTitleToHeader('fas fa-building-columns','Bankieren','Administratie');$this->assets();return new TemplateResponse('Flexgrid/Modules/AdminBanking/src/Templates/Transactions/Index.php',['content'=>(string)$this->renderContent($this->query())]);}
    public function createAccount()
    {
        try{$request=new Request();$account=AdminBankingFactory::createCreateAccount()->execute(new CreateBankAccountCommand($this->string($request,'name'),$this->string($request,'account_reference'),$this->string($request,'currency')?:'EUR',$this->string($request,'iban')));return$this->contentResponse(new BankTransactionListQuery(),'Bankrekening '.$account->getName().' is beschikbaar.');}catch(\Throwable$throwable){return$this->error($throwable,'De bankrekening kon niet worden opgeslagen.');}
    }
    public function importStatement()
    {
        try{$request=new Request();list($content,$fileName)=$this->statement($request);$result=AdminBankingFactory::createImportStatement()->execute(new ImportBankStatementCommand($this->string($request,'bank_account_public_id'),'abn_tab',$content,$fileName));$import=$result->getImport();$message=$result->wasAlreadyImported()?'Dit afschrift was al volledig geïmporteerd.':$import->getImportedCount().' banktransactie(s) geïmporteerd; '.$import->getSkippedCount().' bestaande regel(s) overgeslagen.';return$this->contentResponse(new BankTransactionListQuery('',$import->getBankAccountPublicId()),$message);}catch(\Throwable$throwable){return$this->error($throwable,'Het bankafschrift kon niet worden geïmporteerd.');}
    }
    public function match($args=[])
    {
        try{$data=AdminBankingFactory::createGetMatches()->execute($this->routeArgument($args));}catch(\Throwable$throwable){return$this->transactions();}appendIconAndTitleToHeader('fas fa-link','Banktransactie afletteren','Administratie');$this->assets();return new TemplateResponse('Flexgrid/Modules/AdminBanking/src/Templates/Match/Index.php',['content'=>(string)$this->renderMatchContent($data),'overviewUrl'=>$this->url('transactions')]);
    }
    public function generateMatches()
    {
        try{$request=new Request();$data=AdminBankingFactory::createGenerateMatches()->execute($this->string($request,'public_id'));$response=new AjaxResponse();$response->success=true;$count=count($data['proposals']);$message=$count===0?'Geen betrouwbare voorstellen gevonden.':$count.' controlevoorstel(len) gevonden.';$response->notifications=['<div class="notification notification--'.($count===0?'warning':'success').'" fade="4000">'.htmlspecialchars($message,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8').'</div>'];$response->setContainer('[data-admin-banking-match-content]',(string)$this->renderMatchContent($data));return$response;}catch(\Throwable$throwable){return$this->error($throwable,'Matchvoorstellen konden niet worden berekend.');}
    }
    public function acceptMatch()
    {
        try{$request=new Request();$transaction=AdminBankingFactory::createAcceptMatch()->execute($this->string($request,'public_id'),$this->string($request,'proposal_id'));return$this->matchResponse($transaction->getPublicId(),'Banktransactie is definitief afgeletterd.');}catch(\Throwable$throwable){return$this->error($throwable,'De banktransactie kon niet worden afgeletterd.');}
    }
    public function searchTargets()
    {
        try{$request=new Request();$data=AdminBankingFactory::createSearchTargets()->execute($this->string($request,'public_id'),substr($this->string($request,'q'),0,120));$response=new AjaxResponse();$response->success=true;$response->setContainer('[data-admin-banking-manual-results]',(string)$this->renderManualResults($data));return$response;}catch(\Throwable$throwable){return$this->error($throwable,'Afletterdoelen konden niet worden gezocht.');}
    }
    public function acceptManualMatch()
    {
        try{$request=new Request();$transaction=AdminBankingFactory::createAcceptManualMatch()->execute($this->string($request,'public_id'),$this->string($request,'target_type'),$this->string($request,'target_public_id'));return$this->matchResponse($transaction->getPublicId(),'Banktransactie is handmatig en definitief afgeletterd.');}catch(\Throwable$throwable){return$this->error($throwable,'De handmatige aflettering kon niet worden verwerkt.');}
    }
    public function ignoreTransaction()
    {
        try{$request=new Request();$transaction=AdminBankingFactory::createIgnoreTransaction()->execute($this->string($request,'public_id'));return$this->matchResponse($transaction->getPublicId(),'Banktransactie is genegeerd.');}catch(\Throwable$throwable){return$this->error($throwable,'De banktransactie kon niet worden genegeerd.');}
    }
    public function refresh(){return$this->contentResponse($this->query());}
    private function renderContent(BankTransactionListQuery$query):TemplateResponse{$data=AdminBankingFactory::createListTransactions()->execute($query);return new TemplateResponse('Flexgrid/Modules/AdminBanking/src/Templates/Transactions/Content.php',AdminBankingFactory::createOverviewPresenter()->present($data,$this->action('refresh'),$this->action('createAccount'),$this->action('importStatement'),$this->url('match')));}
    private function renderMatchContent(array$data):TemplateResponse{return new TemplateResponse('Flexgrid/Modules/AdminBanking/src/Templates/Match/Content.php',AdminBankingFactory::createMatchPresenter()->present($data,$this->action('generateMatches'),$this->action('acceptMatch'),$this->action('ignoreTransaction'),$this->action('searchTargets'),$this->action('acceptManualMatch')));}
    private function renderManualResults(array$data):TemplateResponse{return new TemplateResponse('Flexgrid/Modules/AdminBanking/src/Templates/Match/ManualResults.php',AdminBankingFactory::createMatchPresenter()->presentTargets($data,$this->action('acceptManualMatch')));}
    private function matchResponse(string$publicId,string$message):AjaxResponse{$data=AdminBankingFactory::createGetMatches()->execute($publicId);$response=new AjaxResponse();$response->success=true;$response->notifications=['<div class="notification notification--success" fade="4000">'.htmlspecialchars($message,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8').'</div>'];$response->setContainer('[data-admin-banking-match-content]',(string)$this->renderMatchContent($data));return$response;}
    private function contentResponse(BankTransactionListQuery$query,string$message=''):AjaxResponse{$response=new AjaxResponse();$response->success=true;if($message!==''){$response->notifications=['<div class="notification notification--success" fade="4000">'.htmlspecialchars($message,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8').'</div>'];}$response->setContainer('[data-admin-banking-content]',(string)$this->renderContent($query));$response->replaceUrl=$this->overviewUrl($query);return$response;}
    private function query():BankTransactionListQuery{$request=new Request();$year=$this->string($request,'year');$sort=$this->string($request,'sort');$direction=strtolower($this->string($request,'direction'));$status=strtolower($this->string($request,'status'));$flow=strtolower($this->string($request,'flow'));$perPage=$request->getInt('per_page');return new BankTransactionListQuery(substr($this->string($request,'q'),0,120),$this->string($request,'account'),in_array($status,BankTransactionStatus::values(),true)?$status:'',in_array($flow,['all','incoming','outgoing'],true)?$flow:'all',preg_match('/^[0-9]{4}$/D',$year)===1?(int)$year:null,in_array($sort,BankTransactionListQuery::sorts(),true)?$sort:'booked_on',in_array($direction,['asc','desc'],true)?$direction:'desc',max(1,$request->getInt('page')),in_array($perPage,BankTransactionListQuery::pageSizes(),true)?$perPage:10);}
    private function statement(Request$request):array
    {
        $file=$_FILES['statement_file']??null;if(is_array($file)&&isset($file['error'])&&(int)$file['error']!==UPLOAD_ERR_NO_FILE){if((int)$file['error']!==UPLOAD_ERR_OK){throw new \InvalidArgumentException('Uploaden van het bankbestand is mislukt.');}$size=(int)($file['size']??0);$temporary=(string)($file['tmp_name']??'');if($size<1||$size>10*1024*1024||$temporary===''||!is_uploaded_file($temporary)){throw new \InvalidArgumentException('Bankbestand moet tussen 1 byte en 10 MB zijn.');}$content=file_get_contents($temporary);if($content===false){throw new \RuntimeException('Bankbestand kon niet worden gelezen.');}$name=preg_replace('/[\x00-\x1F\x7F]+/','',basename(str_replace('\\','/',(string)($file['name']??'afschrift.txt'))));return[$content,substr($name?:'afschrift.txt',0,255)];}
        $content=$request->get('statement_content','');if(!is_string($content)){throw new \InvalidArgumentException('Plak geldige afschriftregels of kies een bestand.');}return[$content,'handmatig-geplakt.txt'];
    }
    private function string(Request$request,string$key):string{$value=$request->get($key,'');return is_string($value)||is_int($value)?trim(strip_tags((string)$value)):'';}
    private function routeArgument($args,int$position=0):string{$value=is_array($args)?($args[$position]??''):($position===0?$args:'');return is_string($value)||is_int($value)?trim((string)$value):'';}
    private function action(string$method):string{$event=new AjaxEvent(self::class,$method);$event->setMinimumAccessLevel(2);return$event->getName();}
    private function error(\Throwable$throwable,string$fallback):AjaxResponse{$response=new AjaxResponse();$response->success=false;$message=$throwable instanceof \InvalidArgumentException||$throwable instanceof \DomainException?$throwable->getMessage():$fallback;$response->error=$message;$response->notifications=['<div class="notification notification--error" fade="6000">'.htmlspecialchars($message,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8').'</div>'];return$response;}
    private function assets():void{PageResponse::addAsset('Flexgrid/Flexgrid/src/Html/Admin/Css/AdminUi.scss');PageResponse::addAsset('Flexgrid/Flexgrid/src/Html/Table/Css/Table.scss');PageResponse::addAsset('Flexgrid/Flexgrid/src/Html/Table/Js/Table.js');PageResponse::addAsset('Flexgrid/Modules/AdminBanking/src/Templates/Transactions/Css/Transactions.scss');PageResponse::addAsset('Flexgrid/Modules/AdminBanking/src/Templates/Match/Css/Match.scss');PageResponse::addAsset('Flexgrid/Modules/AdminBanking/src/Templates/Transactions/Js/Transactions.js');PageResponse::addAsset('Flexgrid/Modules/AdminBanking/src/Templates/Match/Js/Match.js');}
    private function url(string$path):string{return rtrim(__DOMAIN__,'/').'/Flexgrid/AdminBanking/'.ltrim($path,'/');}
    private function overviewUrl(BankTransactionListQuery$query):string{$parameters=array_filter(['q'=>$query->getSearch(),'account'=>$query->getAccountPublicId(),'status'=>$query->getStatus(),'flow'=>$query->getFlow()==='all'?'':$query->getFlow(),'year'=>$query->getYear(),'sort'=>$query->getSort(),'direction'=>$query->getDirection(),'page'=>$query->getPage(),'per_page'=>$query->getPerPage()],function($value):bool{return$value!==''&&$value!==null;});return$this->url('transactions').'?'.http_build_query($parameters);}
}
