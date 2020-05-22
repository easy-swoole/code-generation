<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 2020-05-20
 * Time: 16:05
 */

namespace EasySwoole\CodeGeneration\ControllerGeneration;

use EasySwoole\CodeGeneration\ModelGeneration\ModelGeneration;
use EasySwoole\CodeGeneration\ModelGeneration\ModelConfig;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\ORM\Utility\Schema\Table;

class ControllerConfig extends ModelConfig
{
    protected $authSessionName;//额外需要的授权session名称
    protected $modelClass;//model的类名
    protected $fileSuffix='';//文件后缀

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
