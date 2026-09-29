<?php
declare(strict_types=1);
namespace Flexgrid\Modules\AdminBanking\Service;
use Flexgrid\Modules\AdminBanking\Contract\BankMatchCandidateProviderInterface;
final class BankMatchCandidateProviderLoader
{
    private$modulesRoot,$providerClasses;public function __construct(string$modulesRoot,array$providerClasses=[]){$this->modulesRoot=rtrim(str_replace('\\','/',$modulesRoot),'/');$this->providerClasses=$providerClasses;}
    public function getProviders():array{$providers=[];foreach($this->classes()as$class){if(!class_exists($class)){continue;}$provider=new$class();if(!$provider instanceof BankMatchCandidateProviderInterface){throw new \LogicException($class.' moet BankMatchCandidateProviderInterface implementeren.');}$providers[]=$provider;}return$providers;}
    private function classes():array{if($this->providerClasses!==[]){return array_values(array_unique(array_filter($this->providerClasses,'is_string')));}if($this->modulesRoot===''||!is_dir($this->modulesRoot)){return[];}$classes=[];foreach(glob($this->modulesRoot.'/*/src/Integration/Banking/MatchCandidateProvider.php')?:[]as$file){$module=basename(dirname($file,4));if(preg_match('/^[A-Za-z][A-Za-z0-9]*$/D',$module)!==1||$module==='AdminBanking'){continue;}require_once$file;$classes[]='Flexgrid\\Modules\\'.$module.'\\Integration\\Banking\\MatchCandidateProvider';}sort($classes);return array_values(array_unique($classes));}
}
