<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 2020-05-20
 * Time: 10:49
 */

namespace EasySwoole\CodeGeneration\UnitTest;

use EasySwoole\CodeGeneration\ClassGeneration\ClassGeneration;
use EasySwoole\CodeGeneration\UnitTest\Method\Add;
use EasySwoole\CodeGeneration\UnitTest\Method\Del;
use EasySwoole\CodeGeneration\UnitTest\Method\GetList;
use EasySwoole\CodeGeneration\UnitTest\Method\GetOne;
use EasySwoole\CodeGeneration\UnitTest\Method\Update;
use EasySwoole\CodeGeneration\Unity\Unity;
use Nette\PhpGenerator\ClassType;

class UnitTestGeneration extends ClassGeneration
{
    /**
     * @var $config UnitTestConfig
     */
    protected $config;

    function addClassData()
    {
        $this->phpClass->addProperty('modelName', $this->getApiUrl());
        $this->phpNamespace->addUse($this->config->getModelClass());
        $this->addClassBaseContent();
        $this->addGenerationMethod(new Add($this));
        $this->addGenerationMethod(new GetOne($this));
        $this->addGenerationMethod(new Update($this));
        $this->addGenerationMethod(new GetList($this));
        $this->addGenerationMethod(new Del($this));
    }

    protected function getApiUrl()
    {
        $baseNamespace = $this->config->getControllerClass();
        $modelName = str_replace(['App\\HttpController', '\\'], ['', '/'], $baseNamespace);
        return $modelName;
    }

    /**
     * 新增基础类内容
     * addClassBaseContent
     * @author Tioncico
     * Time: 21:38
     */
    protected function addClassBaseContent(): ClassType
    {
        $table = $this->config->getTable();
        $phpClass = $this->phpClass;
        //配置表名属性
        $phpClass->addProperty('tableName', $table->getTable())
            ->setVisibility('protected');
        foreach ($table->getColumns() as $column) {
            $name = $column->getColumnName();
            $comment = $column->getColumnComment();
            $columnType = Unity::convertDbTypeToDocType($column->getColumnType());
            $phpClass->addComment("@property {$columnType} \${$name} // {$comment}");
        }
        return $phpClass;
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        $className = $this->config->getRealTableName() . $this->config->getFileSuffix();
        return $className;
    }

}
