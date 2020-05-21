# code-generation
easyswoole代码生成组件,可使用命令行,代码一键生成业务通用代码,支持代码如下:
- 一键生成项目初始化 baseController,baseModel,baseUnitTest.
- 一键生成 表Model ,自带属性注释
- 一键生成 表 curd控制器,自带5个curd方法
- 一键生成 控制器单元测试用例,测试5个curd方法


## 安装
```bash
composer require easyswoole/code-generation
```

## 使用
```php
<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 2020-05-20
 * Time: 10:26
 */
include "./vendor/autoload.php";
\EasySwoole\EasySwoole\Core::getInstance()->initialize()->globalInitialize();

go(function () {
    //生成基础类

    $generation = new \EasySwoole\CodeGeneration\InitBaseClass\Controller\ControllerGeneration();
    $generation->generate();
    $generation = new \EasySwoole\CodeGeneration\InitBaseClass\UnitTest\UnitTestGeneration();
    $generation->generate();
    $generation = new \EasySwoole\CodeGeneration\InitBaseClass\Model\ModelGeneration();
    $generation->generate();
    

    $mysqlConfig = new \EasySwoole\ORM\Db\Config(\EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL'));
    //获取连接
    $connection = new \EasySwoole\ORM\Db\Connection($mysqlConfig);
    $tableName = 'user_list';

    $codeGeneration = new EasySwoole\CodeGeneration\CodeGeneration($tableName, $connection);
    //生成model
    $codeGeneration->generationModel("\\User");
    //生成controller
    $codeGeneration->generationController("\\Api\\User", null);
    //生成unitTest
    $codeGeneration->generationUnitTest("\\User", null);
});

```
::: warning
`EasySwoole\CodeGeneration\CodeGeneration` 方法可自行查看,代码很简单.  
::: 


## 命令行使用.
由于命令行特性,命令行功能支持并不完善,如果需要体验全部功能,请使用 `EasySwoole\CodeGeneration\CodeGeneration` 生成,或参考`EasySwoole\CodeGeneration\CodeGeneration`代码生成.
### 注册命令
在`bootstrap事件`注册command:
```php
<?php
\EasySwoole\EasySwoole\Command\CommandContainer::getInstance()->set(new \EasySwoole\CodeGeneration\GenerationCommand());
```
即可使用命令生成.  
```bash
php easyswoole generation all user_list \\User \\Api\\\User \\User
php easyswoole generation init
```


## 独立使用
```php
<?php
$path='';
//获取表结构
$tableObjectGeneration = new \EasySwoole\ORM\Utility\TableObjectGeneration($connection, $tableName);
$schemaInfo = $tableObjectGeneration->generationTable();


$modelConfig = new \EasySwoole\CodeGeneration\ModelGeneration\ModelConfig($schemaInfo, '', "App\\Model{$path}", \EasySwoole\ORM\AbstractModel::class);
$modelGeneration = new \EasySwoole\CodeGeneration\ModelGeneration\ModelGeneration($modelConfig);
$modelGeneration->generate();

$controllerConfig = new \EasySwoole\CodeGeneration\ControllerGeneration\ControllerConfig($modelGeneration->getConfig()->getNamespace() . '\\' . $modelGeneration->getClassName(), $schemaInfo, '', "App\\HttpController{$path}", \EasySwoole\HttpAnnotation\AnnotationController::class);
$controllerGeneration = new \EasySwoole\CodeGeneration\ControllerGeneration\ControllerGeneration($controllerConfig);
$controllerGeneration->generate();


$controllerConfig = new \EasySwoole\CodeGeneration\UnitTest\UnitTestConfig($modelGeneration->getConfig()->getNamespace() . '\\' . $modelGeneration->getClassName(), $controllerGeneration->getConfig()->getNamespace() . '\\' . $controllerGeneration->getClassName(), $this->schemaInfo, $tablePre, "UnitTest{$path}", \PHPUnit\Framework\TestCase::class);
$unitTestGeneration = new \EasySwoole\CodeGeneration\UnitTest\UnitTestGeneration($controllerConfig);
$unitTestGeneration->generate();

```

## 注意

`控制器生成器`依赖于`注解组件`,依赖原先生成的`Model生成器`.  
`单元测试生成器` 依赖于`phpunit`,依赖原先生成的`控制器生成器`,依赖`UnitTestGeneration`,依赖`curl组件`
