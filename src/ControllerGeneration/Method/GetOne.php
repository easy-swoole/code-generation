<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 20-4-25
 * Time: 上午11:15
 */

namespace EasySwoole\CodeGeneration\ControllerGeneration\Method;


use EasySwoole\ORM\Utility\Schema\Column;

class GetOne extends MethodAbstract
{

    protected $methodName = 'getOne';
    protected $methodDescription = '获取一条数据';
    protected $responseParam = [
        'code'   => '状态码',
        'result' => 'api请求结果',
        'msg'    => 'api提示信息',
    ];
    protected $authParam = 'userSession';
    protected $methodAllow = "GET,POST";
    protected $responseSuccessText = '{"code":200,"result":[],"msg":"获取成功"}';
    protected $responseFailText = '{"code":400,"result":[],"msg":"获取失败"}';


    function addMethodBody()
    {
        $method = $this->method;
        $table = $this->controllerConfig->getTable();
        $modelName = $this->getModelName();
        $responseParamComment = [];
        $this->chunkTableColumn(function (Column $column, string $columnName) use ($table,&$methodBody,&$responseParamComment) {
            $paramValue = $this->newColumnParam($column);
            $responseParamName = "result.{$column->getColumnName()}";
            $responseParamComment[] = "@ApiSuccessParam(name=\"{$responseParamName}\",description=\"{$column->getColumnComment()}\")";
            if ($columnName != $table->getPkFiledName()) {
                return false;
            }
            $paramValue->required= '';
            $this->addColumnComment($paramValue);
            return false;
        });
        $this->addResponseParamComment($responseParamComment);

        $methodBody = <<<Body
\$param = ContextManager::getInstance()->get('param');
\$model = new {$modelName}();
\$info = \$model->get(['{$table->getPkFiledName()}' => \$param['{$table->getPkFiledName()}']]);
if (\$info) {
    \$this->writeJson(Status::CODE_OK, \$info, "获取数据成功.");
} else {
    \$this->writeJson(Status::CODE_BAD_REQUEST, [], '数据不存在');
}
Body;
        $method->setBody($methodBody);

    }

    function addResponseParamComment($responseParamArr){
        foreach ($responseParamArr as  $value){
            $this->method->addComment($value);
        }
    }



}
