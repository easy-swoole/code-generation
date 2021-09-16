<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 2020-05-20
 * Time: 11:03
 */

namespace EasySwoole\CodeGeneration\ModelGeneration\Method;


use App\Model\Package\PackageAdvertConfigModel;
use EasySwoole\CodeGeneration\Utility\Utility;
use EasySwoole\ORM\Utility\Schema\Column;

class AddData extends MethodAbstract
{
    function addMethodBody()
    {
        $method = $this->method;

        //配置返回类型
        $method->setReturnType("self");

        $addBodyStr = '';
        $this->chunkTableColumn(function (Column $column, string $columnName) use (&$addBodyStr, $method) {
            if ($column->getIsAutoIncrement()) {
                return false;
            }
            $columnType = Utility::convertDbTypeToDocType($column->getColumnType());
            //配置方法参数
            $method->addParameter($columnName)->setType($columnType);
            $addBodyStr .= "    '{$columnName}'=>\${$columnName},\n";
            return false;
        });

        $methodBody = '';
        $methodBody .= <<<Body
 
\$data = [
$addBodyStr];
\$model = new self(\$data);
\$model->save();
return \$model;
Body;
        //配置方法内容
        $method->setBody($methodBody);
    }

    function getMethodName(): string
    {
        return "addData";
    }
}
