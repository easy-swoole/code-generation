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
use EasySwoole\CodeGeneration\ModelGeneration\ModelClassGeneration;
use EasySwoole\CodeGeneration\ModelGeneration\ModelConfig;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\ORM\Db\Connection;

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

    function getModelGeneration($path, $tablePre = '', $extendClass = \EasySwoole\ORM\AbstractModel::class): ModelClassGeneration
    {
        $modelConfig = new ModelConfig($this->schemaInfo, $tablePre, "App\\Model{$path}", $extendClass);
        $modelGeneration = new ModelClassGeneration($modelConfig);
        $this->modelGeneration = $modelGeneration;
        return $modelGeneration;
    }

    function getControllerGeneration(ModelClassGeneration $modelGeneration, $path, $tablePre = '', $extendClass = AnnotationController::class): ControllerGeneration
    {
        $controllerConfig = new ControllerConfig($modelGeneration, $this->schemaInfo, $tablePre, "App\\HttpController{$path}", $extendClass);
        $controllerGeneration = new ControllerGeneration($controllerConfig);
        return $controllerGeneration;
    }

    function generationModel($path, $tablePre = '', $extendClass = \EasySwoole\ORM\AbstractModel::class)
    {
        $modelGeneration = $this->getModelGeneration($path, $tablePre, $extendClass);
        $result = $modelGeneration->generate();
        return $result;
    }

    function generationController($path, ?ModelClassGeneration $modelGeneration = null, $tablePre = '', $extendClass = AnnotationController::class)
    {
        $modelGeneration = $modelGeneration ?? $this->modelGeneration;
        $controllerGeneration = $this->getControllerGeneration($modelGeneration, $path, $tablePre,  $extendClass);
        $result = $controllerGeneration->generate();
        return $result;
    }
}
