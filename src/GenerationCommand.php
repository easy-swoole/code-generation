<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 2020-05-20
 * Time: 16:51
 */

namespace EasySwoole\CodeGeneration;


use EasySwoole\Command\AbstractInterface\CommandHelpInterface;
use EasySwoole\Command\AbstractInterface\CommandInterface;
use EasySwoole\Command\Color;
use EasySwoole\Command\CommandManager;
use EasySwoole\Command\Result;
use EasySwoole\Component\Di;
use EasySwoole\Component\Timer;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\Utility\ArrayToTextTable;
use Swoole\Coroutine\Scheduler;

class GenerationCommand implements CommandInterface
{
    public function commandName(): string
    {
        return "generation";
    }

    public function desc(): string
    {
        return 'Code auto generation tool';
    }


    public function help(CommandHelpInterface $commandHelp): CommandHelpInterface
    {
        $commandHelp->addAction('init', 'initialization');
        $commandHelp->addAction('all', 'specify build');
        $commandHelp->addActionOpt('--tableName', 'specify table name');
        $commandHelp->addActionOpt('--modelPath', 'specify model path');
        $commandHelp->addActionOpt('--controllerPath', 'specify controller path');
        $commandHelp->addActionOpt('--unitTestPath', 'specify unit-test path');
        return $commandHelp;
    }

    public function exec(): ?string
    {
        $action = CommandManager::getInstance()->getArg(0);
        $result = new Result();
        $run = new Scheduler();
        $run->add(function () use (&$result, $action) {
            switch ($action) {
                case 'init':
                    $result = $this->init();
                    break;
                case 'all':
                    $result = $this->all();
                    break;
                default:
                    $result = CommandManager::getInstance()->displayCommandHelp($this->commandName());
                    break;
            }
            Timer::getInstance()->clearAll();
        });
        $run->start();
        return $result . PHP_EOL;
    }


    protected function init()
    {
        $table = [];
        $table[0] = ['className' => 'Model', 'filePath' => $this->generationBaseModel()];
        $table[1] = ['className' => 'Controller', 'filePath' => $this->generationBaseController()];
        $table[2] = ['className' => 'UnitTest', 'filePath' => $this->generationBaseUnitTest()];

        return new ArrayToTextTable($table);
    }

    protected function all()
    {
        $tableName = CommandManager::getInstance()->getOpt('tableName');
        if (empty($tableName)) {
            return Color::error('table not empty');
        }

        $modelPath = CommandManager::getInstance()->getOpt('modelPath');
        $controllerPath = CommandManager::getInstance()->getOpt('controllerPath');
        $unitTestPath = CommandManager::getInstance()->getOpt('unitTestPath');
        $connection = $this->getConnection();
        $codeGeneration = new CodeGeneration($tableName, $connection);
        $this->trySetDiGenerationPath($codeGeneration);

        $table = [];
        if ($modelPath) {
            $filePath = $codeGeneration->generationModel($modelPath);
            $table[] = ['className' => 'Model', "filePath" => $filePath];
        } else {
            return Color::error('Model path must be specified');
        }

        if ($controllerPath) {
            $filePath = $codeGeneration->generationController($controllerPath);
            $table[] = ['className' => 'Controller', "filePath" => $filePath];
        }
        if ($unitTestPath) {
            $filePath = $codeGeneration->generationUnitTest($unitTestPath);
            $table[] = ['className' => 'UnitTest', "filePath" => $filePath];
        }

        return new ArrayToTextTable($table);
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


    protected function generationBaseController()
    {
        $generation = new \EasySwoole\CodeGeneration\InitBaseClass\Controller\ControllerGeneration();
        return $generation->generate();
    }

    protected function generationBaseUnitTest()
    {
        $generation = new \EasySwoole\CodeGeneration\InitBaseClass\UnitTest\UnitTestGeneration();
        return $generation->generate();
    }

    protected function generationBaseModel()
    {
        $generation = new \EasySwoole\CodeGeneration\InitBaseClass\Model\ModelGeneration();
        return $generation->generate();
    }

}
