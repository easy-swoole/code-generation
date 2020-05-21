<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 2020-05-21
 * Time: 10:13
 */

namespace EasySwoole\CodeGeneration\InitBaseClass\Controller;


use EasySwoole\CodeGeneration\ClassGeneration\ClassGeneration;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Trigger;
use EasySwoole\Http\Message\Status;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Exception\Annotation\ParamValidateError;

class ControllerGeneration extends ClassGeneration
{
    /**
     * @var $config ControllerConfig
     */
    protected $config;
    public function __construct(?ControllerConfig $config=null)
    {
        if (empty($config)){
            $config = new ControllerConfig('Base',"App\\HttpController");
            $config->setExtendClass(AnnotationController::class);
        }
        parent::__construct($config);
    }

    function addClassData()
    {
        $this->addIndexMethod();
        $this->addGetClientIpMethod();
        $this->addOnExceptionMethod();
    }

    protected function addIndexMethod()
    {
        $phpClass = $this->phpClass;
        $phpClass->addMethod('index')
            ->addBody(<<<BODY
             \$this->actionNotFound('index');
BODY
            );
    }

    protected function addGetClientIpMethod()
    {
        $this->phpNamespace->addUse(ServerManager::class);

        $phpClass = $this->phpClass;
        $method = $phpClass->addMethod('clientRealIP');
        $method->addParameter('headerName')->setDefaultValue("x-real-ip");
        $method->setBody(<<<BODY
\$server = ServerManager::getInstance()->getSwooleServer();
\$client = \$server->getClientInfo(\$this->request()->getSwooleRequest()->fd);
\$clientAddress = \$client['remote_ip'];
\$xri = \$this->request()->getHeader(\$headerName);
\$xff = \$this->request()->getHeader('x-forwarded-for');
if (\$clientAddress === '127.0.0.1') {
    if (!empty(\$xri)) {  // 如果有xri 则判定为前端有NGINX等代理
        \$clientAddress = \$xri[0];
    } elseif (!empty(\$xff)) {  // 如果不存在xri 则继续判断xff
        \$list = explode(',', \$xff[0]);
        if (isset(\$list[0])) \$clientAddress = \$list[0];
    }
}
return \$clientAddress;
BODY
        );

    }

    function addOnExceptionMethod()
    {
        $this->phpNamespace->addUse(ParamValidateError::class);
        $this->phpNamespace->addUse(Status::class);
        $this->phpNamespace->addUse(Trigger::class);

        $phpClass = $this->phpClass;
        $method = $phpClass->addMethod('onException');
        $method->addParameter('throwable')->setType(\Throwable::class);
        $method->setReturnType('void');
        $method->setBody(<<<BODY
if (\$throwable instanceof ParamValidateError) {
    \$this->writeJson(Status::CODE_BAD_REQUEST,[], \$throwable->getValidate()->getError()->__toString());
}  else {
    Trigger::getInstance()->throwable(\$throwable);
    \$this->writeJson(Status::CODE_INTERNAL_SERVER_ERROR, null, \$throwable->getMessage());
}
BODY
        );

    }
}
