<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 2020-05-21
 * Time: 09:39
 */

namespace EasySwoole\CodeGeneration\UnitTest\Method;

use EasySwoole\CodeGeneration\ClassGeneration\MethodAbstract;
use EasySwoole\CodeGeneration\UnitTest\UnitTestGeneration;
use EasySwoole\CodeGeneration\Unity\Unity;
use EasySwoole\ORM\Utility\Schema\Column;
use EasySwoole\Utility\Random;

abstract class UnitTestMethod extends MethodAbstract
{
    /**
     * @var UnitTestGeneration
     */
    protected $classGeneration;

    protected $methodName;
    protected $actionName;

    function getMethodName(): string
    {
        return $this->methodName;
    }


    protected function getTableTestData($variableName = 'data')
    {
        $data = '';

        Unity::chunkTableColumn($this->classGeneration->getConfig()->getTable(), function (Column $column, string $columnName) use (&$data, $variableName) {
            if ($columnName == $this->classGeneration->getConfig()->getTable()->getPkFiledName()) {
                return false;
            }
            $value = $this->randColumnTypeValue($column);
            $data .= "\${$variableName}['{$columnName}'] = '{$value}';\n";
        });
        return $data;
    }

    protected function randColumnTypeValue(Column $column)
    {
        $columnType = Unity::convertDbTypeToDocType($column->getColumnType());
        $value = null;
        switch ($columnType) {
            case "int":
                if ($column->getColumnLimit() <= 3) {
                    $value = mt_rand(0, 3);
                } else {
                    $value = mt_rand(10000, 99999);
                }
                break;
            case "float":
                if ($column->getColumnLimit() <= 3) {
                    $value = mt_rand(10, 30) / 10;
                } else {
                    $value = mt_rand(100000, 999999) / 10;
                }
                break;
            case "string":
                $value = '测试文本' . Random::character(6);
                break;
            case "mixed":
                $value = null;
                break;
        }
        return $value;
    }
}
