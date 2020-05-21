<?php
/**
 * Created by PhpStorm.
 * User: tioncico
 * Date: 2020-05-20
 * Time: 10:38
 */

namespace EasySwoole\CodeGeneration\ClassGeneration;


abstract class MethodAbstract
{
    /**
     * @var ClassGeneration
     */
    protected $classGeneration;
    /**
     * @var \Nette\PhpGenerator\Method
     */
    protected $method;

    function __construct(ClassGeneration $classGeneration)
    {
        $this->classGeneration = $classGeneration;
        $method = $classGeneration->getPhpClass()->addMethod($this->getMethodName());
        $this->method = $method;
    }

    function run()
    {
        $this->addComment();
        $this->addMethodBody();
    }

    function addComment()
    {
        return;
    }

    abstract function addMethodBody();

    abstract function getMethodName(): string;
}
