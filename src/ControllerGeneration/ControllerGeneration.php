<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 2020-05-20
 * Time: 16:08
 */

namespace EasySwoole\CodeGeneration\ControllerGeneration;


use EasySwoole\CodeGeneration\ClassGeneration\ClassGeneration;
use EasySwoole\CodeGeneration\ClassGeneration\MethodAbstract;
use EasySwoole\CodeGeneration\ControllerGeneration\Method\Add;
use EasySwoole\CodeGeneration\ControllerGeneration\Method\Delete;
use EasySwoole\CodeGeneration\ControllerGeneration\Method\GetList;
use EasySwoole\CodeGeneration\ControllerGeneration\Method\GetOne;
use EasySwoole\CodeGeneration\ControllerGeneration\Method\Update;
use EasySwoole\Http\Message\Status;
use EasySwoole\HttpAnnotation\AnnotationTag\DocTag\Api;
use EasySwoole\HttpAnnotation\AnnotationTag\DocTag\ApiFail;
use EasySwoole\HttpAnnotation\AnnotationTag\DocTag\ApiRequestExample;
use EasySwoole\HttpAnnotation\AnnotationTag\DocTag\ApiSuccess;
use EasySwoole\HttpAnnotation\AnnotationTag\DocTag\ResponseParam;
use EasySwoole\HttpAnnotation\AnnotationTag\Method;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;
use EasySwoole\Validate\Validate;
use Nette\PhpGenerator\PhpNamespace;

class ControllerGeneration extends ClassGeneration
{
    /**
     * @var $config ControllerConfig
     */
    protected $config;


    function addClassData()
    {
        $this->addUse($this->phpNamespace);
        $this->addGenerationMethod(new Add($this));
        $this->addGenerationMethod(new Update($this));
        $this->addGenerationMethod(new GetOne($this));
        $this->addGenerationMethod(new GetList($this));
        $this->addGenerationMethod(new Delete($this));
    }

    function getClassName()
    {
        return $this->config->getRealTableName();
    }


    protected function addUse(PhpNamespace $phpNamespace)
    {
        $phpNamespace->addUse($this->config->getModelClass());
        $phpNamespace->addUse(Status::class);
        $phpNamespace->addUse(Validate::class);
        $phpNamespace->addUse(Validate::class);
        $phpNamespace->addUse($this->config->getExtendClass());
        //引入新版注解,以及文档生成
        $phpNamespace->addUse(ApiFail::class);
        $phpNamespace->addUse(ApiRequestExample::class);
        $phpNamespace->addUse(ApiSuccess::class);
        $phpNamespace->addUse(Method::class);
        $phpNamespace->addUse(Param::class);
        $phpNamespace->addUse(Api::class);
        $phpNamespace->addUse(ResponseParam::class);
    }

    function addGenerationMethod(MethodAbstract $abstract){
        $this->methodGenerationList[$abstract->getMethodName()] = $abstract;
    }
}
