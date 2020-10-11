<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 20-4-25
 * Time: 上午11:15
 */

namespace EasySwoole\CodeGeneration\ControllerGeneration\Method;


use EasySwoole\ORM\Utility\Schema\Column;

class GetList extends MethodAbstract
{

    protected $methodName = 'getList';
    protected $methodDescription = '获取数据列表';
    protected $responseParam = [
        'code' => '状态码',
        'result' => 'api请求结果',
        'msg' => 'api提示信息',
    ];
    protected $authParam = 'userSession';
    protected $methodAllow = "GET,POST";
    protected $responseSuccessText = '{"code":200,"result":[],"msg":"获取成功"}';
    protected $responseFailText = '{"code":400,"result":[],"msg":"获取失败"}';


    function addMethodBody()
    {
        $method = $this->method;
        $modelName = $this->getModelName();

        //新增page参数注解
        $method->addComment("@Param(name=\"page\", from={GET,POST}, alias=\"页数\", optional=\"\")");
        $method->addComment("@Param(name=\"pageSize\", from={GET,POST}, alias=\"每页总数\", optional=\"\")");

        $responseParamComment = [];
        $this->chunkTableColumn(function (Column $column, string $columnName) use (&$responseParamComment) {
            $responseParamName = "result[].{$columnName}";
            $responseParamComment[] = "@ApiSuccessParam(name=\"{$responseParamName}\",description=\"{$column->getColumnComment()}\")";
            return false;
        });
        $this->addResponseParamComment($responseParamComment);

        $methodBody = <<<Body
\$param = ContextManager::getInstance()->get('param');
\$page = (int)(\$param['page'] ?? 1);
\$pageSize = (int)(\$param['pageSize'] ?? 20);
\$model = new {$modelName}();
\$data = \$model->getList(\$page, \$pageSize);
\$this->writeJson(Status::CODE_OK, \$data, '获取列表成功');
Body;
        $method->setBody($methodBody);
    }


    function addResponseParamComment($responseParamArr){
        foreach ($responseParamArr as  $value){
            $this->method->addComment($value);
        }
    }

}
