<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 2020-05-20
 * Time: 16:51
 */

namespace EasySwoole\CodeGeneration;


use EasySwoole\Component\Timer;
use EasySwoole\EasySwoole\Command\CommandInterface;
use EasySwoole\EasySwoole\Command\Utility;
use EasySwoole\Utility\ArrayToTextTable;
use Swoole\Coroutine\Scheduler;

class GenerationCommand implements CommandInterface
{
    public function commandName(): string
    {
        return "generation";
    }

    public function exec(array $args): ?string
    {
        $ret = '';
        $run = new Scheduler();
        $run->add(function () use (&$ret, $args) {
            $action = array_shift($args);
            switch ($action) {
                case 'init':
                    $result = $this->init($args);
                    break;
                case 'all':
                    $result = $this->all($args);
                    break;
                default:
                    $result = $this->help($args);
                    break;
            }
            $ret = $result;
            Timer::getInstance()->clearAll();
        });
        $run->start();
        return $ret;
    }

    function init($args)
    {
        $result = [];
        $result[0] = ['className' => 'Model', 'filePath' => $this->generationBaseModel()];
        $result[1] = ['className' => 'Controller', 'filePath' => $this->generationBaseController()];
        $result[2] = ['className' => 'UnitTest', 'filePath' => $this->generationBaseUnitTest()];
        return new ArrayToTextTable($result);
    }

    function all($args)
    {
        $mysqlConfig = new \EasySwoole\ORM\Db\Config(\EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL'));
        $connection = new \EasySwoole\ORM\Db\Connection($mysqlConfig);
        $tableName = array_shift($args);
        if (empty($tableName)) {
            return "table not empty";
        }
        $modelPath = array_shift($args);
        $controllerPath = array_shift($args);
        $unitTestPath = array_shift($args);
        $codeGeneration = new CodeGeneration($tableName, $connection);
        $result = [];
        if ($modelPath) {
            $filePath = $codeGeneration->generationModel($modelPath);
            $result[] = ['className' => 'Model', "filePath"=>$filePath];
        }
        if ($controllerPath) {
            $filePath = $codeGeneration->generationController($controllerPath);
            $result[] = ['className' => 'controller', "filePath"=>$filePath];
        }
        if ($unitTestPath) {
            $filePath = $codeGeneration->generationUnitTest($unitTestPath);
            $result[] = ['className' => 'UnitTest', "filePath"=>$filePath];
        }
        return new ArrayToTextTable($result);
    }

    public function help(array $args): ?string
    {
        //输出logo
        $logo = Utility::easySwooleLog();
        return $logo . "
php easyswoole generation all tableName modelPath [controllerPath] [unitTestPath]
php easyswoole generation init
";
    }


    function generationBaseController()
    {
        $generation = new \EasySwoole\CodeGeneration\InitBaseClass\Controller\ControllerGeneration();
        return $generation->generate();
    }

    function generationBaseUnitTest()
    {
        $generation = new \EasySwoole\CodeGeneration\InitBaseClass\UnitTest\UnitTestGeneration();
        return $generation->generate();
    }

    function generationBaseModel()
    {
        $generation = new \EasySwoole\CodeGeneration\InitBaseClass\Model\ModelGeneration();
        return $generation->generate();
    }

}
