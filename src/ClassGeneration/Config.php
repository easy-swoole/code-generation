<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 2020-05-20
 * Time: 10:18
 */

namespace EasySwoole\CodeGeneration\ClassGeneration;

use EasySwoole\CodeGeneration\Unity\Unity;

class Config
{
    protected $extendClass;//继承的基类
    protected $directory;//生成的目录
    protected $namespace;//生成的命名空间
    protected $className;
    protected $rootPath;//项目根目录

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
        if (empty($this->directory) && !empty($this->getNamespace())) {
            //设置下基础目录
            $pathArr = explode('\\', $this->getNamespace());
            $app = array_shift($pathArr);

            $this->setDirectory(rtrim($this->getRootPath() . '/' . Unity::getNamespacePath($this->getRootPath(), $app) . implode('/', $pathArr), '/'));
        }
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

    /**
     * @return mixed
     */
    public function getRootPath()
    {
        if (empty($this->rootPath)) {
            $this->rootPath = getcwd();
        }
        return $this->rootPath;
    }

    /**
     * @param mixed $rootPath
     */
    public function setRootPath($rootPath): void
    {
        $this->rootPath = $rootPath;
    }
}
