<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 2020-05-20
 * Time: 16:51
 */

namespace CodeGeneration;


use EasySwoole\Component\Timer;
use EasySwoole\EasySwoole\Command\CommandInterface;
use EasySwoole\EasySwoole\Command\Utility;
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
                case 'model':
                    $result = $this->model($args);
                    break;
                case 'controller':
                    $result = $this->controller($args);
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

    function model($args)
    {
        $mysqlConfig = new \EasySwoole\ORM\Db\Config(\EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL'));
        $connection = new \EasySwoole\ORM\Db\Connection($mysqlConfig);
        $tableName = array_shift($args);
        $codeGeneration = new CodeGeneration($tableName, $connection);

        $modelPath = array_shift($args);
        $tablePre = array_shift($args);
        if (empty($tablePre)) {
            $tablePre = '';
        }
        $result = $codeGeneration->generationModel($modelPath, $tablePre);
        if ($result) {
            return "generation model success:{$result}";
        } else {
            return "generation model fail\n";
        }
    }

    function controller($args)
    {
        $mysqlConfig = new \EasySwoole\ORM\Db\Config(\EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL'));
        $connection = new \EasySwoole\ORM\Db\Connection($mysqlConfig);
        $tableName = array_shift($args);
        $codeGeneration = new CodeGeneration($tableName, $connection);

        $modelPath = array_shift($args);
        $controllerPath = array_shift($args);
        $tablePre = array_shift($args);
        if (empty($tablePre)) {
            $tablePre = '';
        }
        $modelGeneration = $codeGeneration->getModelGeneration($modelPath, $tablePre);
        $result = $codeGeneration->generationController($controllerPath, $modelGeneration, $tablePre);
        if ($result) {
            return "generation controller success:{$result}";
        } else {
            return "generation controller fail";
        }
    }

    function all($args)
    {
        $mysqlConfig = new \EasySwoole\ORM\Db\Config(\EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL'));
        $connection = new \EasySwoole\ORM\Db\Connection($mysqlConfig);
        $tableName = array_shift($args);
        $codeGeneration = new CodeGeneration($tableName, $connection);

        $modelPath = array_shift($args);
        $controllerPath = array_shift($args);
        $tablePre = array_shift($args);
        if (empty($tablePre)) {
            $tablePre = '';
        }
        $str = "";
        $result = $codeGeneration->generationModel($modelPath, $tablePre);
        if ($result) {
            $str .= "generation model success:{$result}\n";
        } else {
            $str .= "generation model fail\n";
        }
        $modelGeneration = $codeGeneration->getModelGeneration($modelPath, $tablePre);
        $result = $codeGeneration->generationController($controllerPath, $modelGeneration, $tablePre);
        if ($result) {
            $str .= "generation controller success:{$result}\n";
        } else {
            $str .= "generation controller fail";
        }
        return $str;
    }

    public function help(array $args): ?string
    {
        //输出logo
        $logo = Utility::easySwooleLog();
        return $logo . "
php easyswoole generation model tableName modelPath [tablePre]
php easyswoole generation controller tableName modelPath controllerPath [tablePre]
php easyswoole generation all tableName modelPath controllerPath [tablePre]
";
    }


}
