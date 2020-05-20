<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 2020-05-20
 * Time: 10:18
 */

namespace CodeGeneration\CodeGeneration;
class Config
{
    protected $extendClass;//继承的基类
    protected $directory;//生成的目录
    protected $namespace;//生成的命名空间
    protected $className;

    public function __construct($className, $nameSpace = "\\App")
    {
        $this->setClassName($className);
        $this->setNamespace($nameSpace);
    }


    /**
     * @param mixed $namespace
     */
    public function setNamespace($namespace): void
    {
        $this->namespace = $namespace;
        //设置下基础目录
        $pathArr = explode('\\', $namespace);
        $app = array_shift($pathArr);
        $this->setDirectory(EASYSWOOLE_ROOT . '/' . \CodeGeneration\Unity\Unity::getNamespacePath($app) . implode('/', $pathArr));
    }

    /**
     * @return mixed
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return mixed
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @param mixed $directory
     */
    public function setDirectory($directory): void
    {
        $this->directory = $directory;
    }


    /**
     * @return mixed
     */
    public function getExtendClass()
    {
        return $this->extendClass;
    }

    /**
     * @param mixed $extendClass
     */
    public function setExtendClass($extendClass): void
    {
        if ($extendClass=='App\HttpController\Api\User'){
            throw  new \Exception('11');
        }
        $this->extendClass = $extendClass;
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param mixed $className
     */
    public function setClassName($className): void
    {
        $this->className = $className;
    }
}
