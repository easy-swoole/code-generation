<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 2020-05-20
 * Time: 16:05
 */

namespace EasySwoole\CodeGeneration\ControllerGeneration;

use EasySwoole\CodeGeneration\ModelGeneration\ModelClassGeneration;
use EasySwoole\CodeGeneration\ModelGeneration\ModelConfig;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\ORM\Utility\Schema\Table;

class ControllerConfig extends ModelConfig
{
    protected $authName;//额外需要的授权用户分组
    protected $authSessionName;//额外需要的授权session名称
    protected $modelClass;//model的类名

    protected $table;//表数据DDL对象
    protected $ignoreString = [
        'list',
        'log'
    ];//文件名生成时,忽略的字符串(list,log等)
    protected $realTableName;//表(生成的文件)真实名称

    public function __construct(string $modelClass, Table $schemaInfo, $tablePre = '', $nameSpace = "App\\HttpController", $extendClass = AnnotationController::class)
    {
        $this->setModelClass($modelClass);
        $this->setTable($schemaInfo);
        $this->setTablePre($tablePre);
        $this->setNamespace($nameSpace);
        $this->setExtendClass($extendClass);
    }

    /**
     * @return mixed
     */
    public function getModelClass()
    {
        return $this->modelClass;
    }

    /**
     * @param mixed $modelClass
     */
    public function setModelClass($modelClass): void
    {
        $this->modelClass = $modelClass;
    }

    /**
     * @return mixed
     */
    public function getAuthName()
    {
        return $this->authName;
    }

    /**
     * @param mixed $authName
     */
    public function setAuthName($authName): void
    {
        $this->authName = $authName;
    }

    /**
     * @return mixed
     */
    public function getAuthSessionName()
    {
        return $this->authSessionName;
    }

    /**
     * @param mixed $authSessionName
     */
    public function setAuthSessionName($authSessionName): void
    {
        $this->authSessionName = $authSessionName;
    }

}
