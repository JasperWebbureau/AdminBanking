<?php

declare(strict_types=1);

namespace Flexgrid\Modules\AdminBanking\Service;

use Flexgrid\Modules\AdminBanking\Contract\BankExpenseCreatorInterface;

final class BankExpenseCreatorLoader
{
    private $modulesRoot;

    public function __construct(string $modulesRoot)
    {
        $this->modulesRoot = rtrim(str_replace('\\', '/', $modulesRoot), '/');
    }

    public function getCreator(): ?BankExpenseCreatorInterface
    {
        $found = [];
        foreach (glob($this->modulesRoot . '/*/src/Integration/Banking/ExpenseCreator.php') ?: [] as $file) {
            $module = basename(dirname($file, 4));
            if (preg_match('/^[A-Za-z][A-Za-z0-9]*$/D', $module) !== 1 || $module === 'AdminBanking') {
                continue;
            }
            require_once $file;
            $class = 'Flexgrid\\Modules\\' . $module . '\\Integration\\Banking\\ExpenseCreator';
            if (!class_exists($class)) {
                continue;
            }
            $creator = new $class();
            if (!$creator instanceof BankExpenseCreatorInterface) {
                throw new \LogicException($class . ' moet BankExpenseCreatorInterface implementeren.');
            }
            $found[] = $creator;
        }
        if (count($found) > 1) {
            throw new \LogicException('Meerdere modules bieden bankuitgaven aan.');
        }
        return $found[0] ?? null;
    }
}
