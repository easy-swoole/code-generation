<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 2020-05-21
 * Time: 10:13
 */

namespace EasySwoole\CodeGeneration\InitBaseClass\Model;


use EasySwoole\CodeGeneration\ClassGeneration\ClassGeneration;
use EasySwoole\CodeGeneration\InitBaseClass\Model\ModelConfig;
use EasySwoole\ORM\AbstractModel;
use EasySwoole\ORM\DbManager;

class ModelGeneration extends ClassGeneration
{
    /**
     * @var $config ModelConfig
     */
    protected $config;
    public function __construct(?ModelConfig $config=null)
    {
        if (empty($config)){
            $config = new ModelConfig("BaseModel","App\\Model");
            $config->setExtendClass(AbstractModel::class);
        }
        parent::__construct($config);
    }

    function addClassData()
    {
        $this->phpNamespace->addUse(DbManager::class);
        $method = $this->phpClass->addMethod('transaction');
        $method->setStatic();
        $method->addParameter('callable')->setType('callable');
        $method->setBody(<<<BODY
try {
    DbManager::getInstance()->startTransaction();
    \$result = \$callable();
    DbManager::getInstance()->commit();
    return \$result;
} catch (\Throwable \$throwable) {
    DbManager::getInstance()->rollback();
    throw \$throwable;;
}
BODY
        );
    }
}
