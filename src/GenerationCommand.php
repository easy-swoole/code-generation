<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 2020-05-20
 * Time: 16:51
 */

namespace EasySwoole\CodeGeneration;


use EasySwoole\Command\AbstractInterface\ResultInterface;
use EasySwoole\Command\Result;
use EasySwoole\Component\Di;
use EasySwoole\Component\Timer;
use EasySwoole\EasySwoole\Command\CommandInterface;
use EasySwoole\EasySwoole\Command\Utility;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\Utility\ArrayToTextTable;
use Swoole\Coroutine\Scheduler;

class GenerationCommand implements CommandInterface
{
    public function commandName(): string
    {
        return "generation";
    }

    public function exec($args): ResultInterface
    {
        $result = new Result();
        $run = new Scheduler();
        $run->add(function () use (&$result, $args) {
            $action = array_shift($args);
            switch ($action) {
                case 'init':
                    $msg = $this->init($args);
                    break;
                case 'all':
                    $msg = $this->all($args);
                    break;
                default:
                    $msg = $this->help($args);
                    break;
            }
            $result->setMsg($msg);
            Timer::getInstance()->clearAll();
        });
        $run->start();
        return $result;
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
        $tableName = array_shift($args);
        if (empty($tableName)) {
            return "table not empty";
        }
        $modelPath = array_shift($args);
        $controllerPath = array_shift($args);
        $unitTestPath = array_shift($args);
        $connection = $this->getConnection();
        $codeGeneration = new CodeGeneration($tableName, $connection);
        $this->trySetDiGenerationPath($codeGeneration);
        $result = [];
        if ($modelPath) {
            $filePath = $codeGeneration->generationModel($modelPath);
            $result[] = ['className' => 'Model', "filePath" => $filePath];
        }
        if ($controllerPath) {
            $filePath = $codeGeneration->generationController($controllerPath);
            $result[] = ['className' => 'controller', "filePath" => $filePath];
        }
        if ($unitTestPath) {
            $filePath = $codeGeneration->generationUnitTest($unitTestPath);
            $result[] = ['className' => 'UnitTest', "filePath" => $filePath];
        }
        return new ArrayToTextTable($result);
    }

    public function help($args): ResultInterface
    {
        //输出logo
        $logo = Utility::easySwooleLog();
        $result = new Result();
        $msg = $logo . "
php easyswoole generation all tableName modelPath [controllerPath] [unitTestPath]
php easyswoole generation init
";
        $result->setMsg($msg);
        return $result;
    }

    protected function getConnection(): Connection
    {
        $connection = Di::getInstance()->get('CodeGeneration.connection');
        if ($connection instanceof Connection) {
            return $connection;
        } elseif (is_array($connection)) {
            $mysqlConfig = new \EasySwoole\ORM\Db\Config($connection);
            $connection = new Connection($mysqlConfig);
            return $connection;
        } elseif ($connection instanceof \EasySwoole\ORM\Db\Config) {
            $connection = new Connection($connection);
            return $connection;
        }
    }

    protected function trySetDiGenerationPath(CodeGeneration $codeGeneration)
    {
        $modelBaseNameSpace = Di::getInstance()->get('CodeGeneration.modelBaseNameSpace');
        $controllerBaseNameSpace = Di::getInstance()->get('CodeGeneration.controllerBaseNameSpace');
        $unitTestBaseNameSpace = Di::getInstance()->get('CodeGeneration.unitTestBaseNameSpace');
        $rootPath = Di::getInstance()->get('CodeGeneration.rootPath');
        if ($modelBaseNameSpace) {
            $codeGeneration->setModelBaseNameSpace($modelBaseNameSpace);
        }
        if ($controllerBaseNameSpace) {
            $codeGeneration->setControllerBaseNameSpace($controllerBaseNameSpace);
        }
        if ($unitTestBaseNameSpace) {
            $codeGeneration->setUnitTestBaseNameSpace($unitTestBaseNameSpace);
        }
        if ($unitTestBaseNameSpace) {
            $codeGeneration->setRootPath($rootPath);
        }
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
