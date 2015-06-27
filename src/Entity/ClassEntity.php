<?php
/**
 * Created by Gary Hockin.
 * Date: 26/06/2015
 * @GeeH
 */

namespace GeeH\Sevenificator\Entity;


use Asgrim\ReflectionClass;

class ClassEntity
{
    private $fileName;
    private $className;
    private $newFunctions;
    private $reflectionClass;


    public function __construct(ReflectionClass $class, array $newFunctions)
    {
        $this->reflectionClass = $class;
        $this->fileName = $class->getFilename();
        $this->className = $class->getName();
        $this->newFunctions = $newFunctions;
    }

    public function getFileName() : string
    {
        return $this->fileName;
    }

    public function getClassName() : string
    {
        return $this->className;
    }

    public function getNewFunctions() : array
    {
        return $this->newFunctions;
    }

    public function getReflectionClass() : ReflectionClass
    {
        return $this->reflectionClass;
    }

}