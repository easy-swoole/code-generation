<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 2020-05-20
 * Time: 16:41
 */

namespace EasySwoole\CodeGeneration;


use EasySwoole\CodeGeneration\ControllerGeneration\ControllerConfig;
use EasySwoole\CodeGeneration\ControllerGeneration\ControllerGeneration;
use EasySwoole\CodeGeneration\ModelGeneration\ModelGeneration;
use EasySwoole\CodeGeneration\ModelGeneration\ModelConfig;
use EasySwoole\CodeGeneration\UnitTest\UnitTestConfig;
use EasySwoole\CodeGeneration\UnitTest\UnitTestGeneration;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\ORM\Db\Connection;
use PHPUnit\Framework\TestCase;

class CodeGeneration
{
    protected $schemaInfo;
    protected $modelGeneration;
    protected $controllerGeneration;

    function __construct(string $tableName, Connection $connection)
    {
        $tableObjectGeneration = new \EasySwoole\ORM\Utility\TableObjectGeneration($connection, $tableName);
        $schemaInfo = $tableObjectGeneration->generationTable();
        $this->schemaInfo = $schemaInfo;
    }

    function getModelGeneration($path, $tablePre = '', $extendClass = \EasySwoole\ORM\AbstractModel::class): ModelGeneration
    {
        $modelConfig = new ModelConfig($this->schemaInfo, $tablePre, "App\\Model{$path}", $extendClass);
        $modelGeneration = new ModelGeneration($modelConfig);
        $this->modelGeneration = $modelGeneration;
        return $modelGeneration;
    }

    function getControllerGeneration(ModelGeneration $modelGeneration, $path, $tablePre = '', $extendClass = AnnotationController::class): ControllerGeneration
    {
        $controllerConfig = new ControllerConfig($modelGeneration->getConfig()->getNamespace() . '\\' . $modelGeneration->getClassName(), $this->schemaInfo, $tablePre, "App\\HttpController{$path}", $extendClass);
        $controllerGeneration = new ControllerGeneration($controllerConfig);
        $this->controllerGeneration = $controllerGeneration;
        return $controllerGeneration;
    }

    function getUnitTestGeneration(ModelGeneration $modelGeneration, ControllerGeneration $controllerGeneration, $path, $tablePre = '', $extendClass = TestCase::class): UnitTestGeneration
    {
        $controllerConfig = new UnitTestConfig($modelGeneration->getConfig()->getNamespace() . '\\' . $modelGeneration->getClassName(), $controllerGeneration->getConfig()->getNamespace() . '\\' . $controllerGeneration->getClassName(), $this->schemaInfo, $tablePre, "UnitTest{$path}", $extendClass);
        $unitTestGeneration = new UnitTestGeneration($controllerConfig);
        return $unitTestGeneration;
    }

    function generationModel($path, $tablePre = '', $extendClass = \EasySwoole\ORM\AbstractModel::class)
    {
        $modelGeneration = $this->getModelGeneration($path, $tablePre, $extendClass);
        $result = $modelGeneration->generate();
        return $result;
    }

    function generationController($path, ?ModelGeneration $modelGeneration = null, $tablePre = '', $extendClass = AnnotationController::class)
    {
        $modelGeneration = $modelGeneration ?? $this->modelGeneration;
        $controllerGeneration = $this->getControllerGeneration($modelGeneration, $path, $tablePre, $extendClass);
        $result = $controllerGeneration->generate();
        return $result;
    }

    function generationUnitTest($path, ?ModelGeneration $modelGeneration = null, ?ControllerGeneration $controllerGeneration = null, $tablePre = '', $extendClass = TestCase::class)
    {
        $modelGeneration = $modelGeneration ?? $this->modelGeneration;
        $controllerGeneration = $controllerGeneration ?? $this->controllerGeneration;
        $controllerGeneration = $this->getUnitTestGeneration($modelGeneration, $controllerGeneration, $path, $tablePre, $extendClass);
        $result = $controllerGeneration->generate();
        return $result;
    }
}
