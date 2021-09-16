<?php


namespace EasySwoole\CodeGeneration\ModelGeneration\Method;


abstract class MethodAbstract  extends \EasySwoole\CodeGeneration\ClassGeneration\MethodAbstract
{

    protected function chunkTableColumn(callable $callback)
    {
        $table = $this->classGeneration->getConfig()->getTable();
        foreach ($table->getColumns() as $column) {
            $columnName = $column->getColumnName();
            $result = $callback($column, $columnName);
            if ($result ===true){
                break;
            }
        }
    }


}
