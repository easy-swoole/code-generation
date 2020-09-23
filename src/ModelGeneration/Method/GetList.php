<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 2020-05-20
 * Time: 11:03
 */

namespace EasySwoole\CodeGeneration\ModelGeneration\Method;




use EasySwoole\CodeGeneration\ClassGeneration\MethodAbstract;

class GetList extends MethodAbstract
{
    function addMethodBody()
    {
        $method = $this->method;

        //配置返回类型
        $method->setReturnType('array');

        //配置方法参数
        $method->addParameter('page', 1)
            ->setTypeHint('int');
        $method->addParameter('pageSize', 10)
            ->setTypeHint('int');
        $method->addParameter('field', '*')->setTypeHint('string');

        $methodBody = '';
        $methodBody .= <<<Body
        
\$list = \$this
    ->withTotalCount()
	->order(\$this->schemaInfo()->getPkFiledName(), 'DESC')
    ->field(\$field)
    ->page(\$page, \$pageSize)
    ->all();
\$total = \$this->lastQueryResult()->getTotalCount();
\$data = [
    'page'=>\$page,
    'pageSize'=>\$pageSize,
    'list'=>\$list,
    'total'=>\$total,
    'pageCount'=>ceil(\$total / \$pageSize)
];
return \$data;
Body;
        //配置方法内容
        $method->setBody($methodBody);
    }

    function getMethodName(): string
    {
        return "getList";
    }
}
