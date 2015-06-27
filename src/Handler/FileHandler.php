<?php
/**
 * Created by Gary Hockin.
 * Date: 26/06/2015
 * @GeeH
 */

namespace GeeH\Sevenificator\Handler;


use Asgrim\ReflectionClass;
use Asgrim\Reflector;
use GeeH\Sevenificator\Entity\ClassEntity;
use GeeH\Sevenificator\Sevenificator;

class FileHandler
{
    /**
     * @var Reflector
     */
    private $reflector;
    /**
     * @var null|Sevenificator
     */
    private $sevenificator;


    public function __construct(Reflector $reflector, Sevenificator $sevenificator = null)
    {
        $this->reflector     = $reflector;
        $this->sevenificator = $sevenificator;
    }

    public function handleFile($filename) : array
    {
        $classes = $this->reflector->getClassesFromFile($filename);
        if(empty($classes)) {
            return [];
        }

        foreach($classes as $class) {
            $return[] = $this->getNewFunctionsForClass($class);
        }

        return $return;
    }


    private function getNewFunctionsForClass(ReflectionClass $class) : ClassEntity
    {
        $functions = [];
        $reflectionClass = new \ReflectionClass($class->getName());
        $this->sevenificator = new Sevenificator($reflectionClass);
        foreach($class->getMethods() as $method) {
            $functions[$method->getName()] = $this->sevenificator->getNewFunctionDeclaration($method->getName());
        }
        $classEntity = new ClassEntity($class, $functions);
        return $classEntity;
    }


}