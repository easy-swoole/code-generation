<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 2020-05-21
 * Time: 09:39
 */

namespace EasySwoole\CodeGeneration\UnitTest\Method;


use EasySwoole\CodeGeneration\Unity\Unity;

class Add extends UnitTestMethod
{
    protected $methodName = 'testAdd';
    protected $actionName = 'add';
    function addMethodBody()
    {
        $method = $this->method;
        $body = <<<BODY
\$data = [];

BODY;
        $body .= $this->getTableTestData('data');
        $modelName = Unity::getModelName($this->classGeneration->getConfig()->getModelClass());

        $body .= <<<BODY
\$response = \$this->request('{$this->actionName}',\$data);
\$model = new {$modelName}();
\$model->destroy(\$response->result->{$this->classGeneration->getConfig()->getTable()->getPkFiledName()});
//var_dump(json_encode(\$response,JSON_UNESCAPED_UNICODE));
BODY;
        $method->setBody($body);

    }
}
