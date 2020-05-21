<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 2020-05-21
 * Time: 09:39
 */

namespace EasySwoole\CodeGeneration\UnitTest\Method;


use EasySwoole\CodeGeneration\Unity\Unity;

class GetList extends UnitTestMethod
{
    protected $methodName = 'testGetList';
    protected $actionName = 'getList';
    function addMethodBody()
    {
        $method = $this->method;
        $modelName = Unity::getModelName($this->classGeneration->getConfig()->getModelClass());
        $body = <<<BODY
\$model = new {$modelName}();
\$data = [];
\$response = \$this->request('{$this->actionName}',\$data);

//var_dump(json_encode(\$response,JSON_UNESCAPED_UNICODE));

BODY;
        $method->setBody($body);
    }
}
