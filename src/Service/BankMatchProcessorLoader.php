<?php
declare(strict_types=1);
namespace Flexgrid\Modules\AdminBanking\Service;
use Flexgrid\Modules\AdminBanking\Contract\BankMatchProcessorInterface;
final class BankMatchProcessorLoader
{
    private$modulesRoot,$processorClasses;public function __construct(string$modulesRoot,array$processorClasses=[]){$this->modulesRoot=rtrim(str_replace('\\','/',$modulesRoot),'/');$this->processorClasses=$processorClasses;}
    public function getProcessors():array{$processors=[];foreach($this->classes()as$class){if(!class_exists($class)){continue;}$processor=new$class();if(!$processor instanceof BankMatchProcessorInterface){throw new \LogicException($class.' moet BankMatchProcessorInterface implementeren.');}$processors[]=$processor;}return$processors;}
    private function classes():array{if($this->processorClasses!==[]){return array_values(array_unique(array_filter($this->processorClasses,'is_string')));}if($this->modulesRoot===''||!is_dir($this->modulesRoot)){return[];}$classes=[];foreach(glob($this->modulesRoot.'/*/src/Integration/Banking/MatchProcessor.php')?:[]as$file){$module=basename(dirname($file,4));if(preg_match('/^[A-Za-z][A-Za-z0-9]*$/D',$module)!==1||$module==='AdminBanking'){continue;}require_once$file;$classes[]='Flexgrid\\Modules\\'.$module.'\\Integration\\Banking\\MatchProcessor';}sort($classes);return array_values(array_unique($classes));}
}
