<?php
/**
 * Created by Gary Hockin.
 * Date: 22/06/2015
 * @GeeH
 */

namespace GeeH\Sevenificator\Entity;


use phpDocumentor\Reflection\DocBlock;

class ReflectionEntity
{
    /**
     * @var \ReflectionClass
     */
    private $reflectionClass;
    /**
     * @var DocBlock[]
     */
    private $reflectionDocBlock = [];
    /**
     * @var \string[]
     */
    private $functionDefinition = [];
    /**
     * @var \SplFileObject
     */
    private $scriptFile;


    public function __construct(\ReflectionClass $reflectionClass)
    {
        $this->reflectionClass = $reflectionClass;
    }

    /**
     * @return DocBlock
     */
    public function getReflectionDocBlock(\string $functionName) : DocBlock
    {
        if (!isset($this->reflectionDocBlock[$functionName])) {
            $reflectionMethod = $this->getReflectionClass()->getMethod($functionName);
            $reflectionDocBlock = new DocBlock($reflectionMethod);

            $this->setReflectionDocBlock($functionName, $reflectionDocBlock);
        }
        return $this->reflectionDocBlock[$functionName];
    }

    /**
     * @param DocBlock $reflectionDocBlock
     */
    public function setReflectionDocBlock(\string $name, DocBlock $reflectionDocBlock)
    {
        $this->reflectionDocBlock[$name] = $reflectionDocBlock;
    }

    /**
     * @return \string
     */
    public function getFunctionDefinition(\string $functionName) : \string
    {
        if (!isset($this->functionDefinition[$functionName])) {
            $file = $this->getScriptFile();
            $file->seek($this->getReflectionClass()->getMethod($functionName)->getStartLine() - 1);
            $this->setFunctionDefinition($functionName, $file->current());
        }

        return $this->functionDefinition[$functionName];
    }

    /**
     * @param array $functionDefinition
     */
    public function setFunctionDefinition(\string $name, \string $functionDefinition)
    {
        $this->functionDefinition[$name] = $functionDefinition;
    }


    /**
     * @return \SplFileObject
     */
    public function getScriptFile() : \SplFileObject
    {
        if (!($this->scriptFile instanceof \SplFileObject)) {
            $this->setScriptFile(new \SplFileObject($this->getReflectionClass()->getFileName()));
        }
        return $this->scriptFile;
    }

    /**
     * @param \SplFileObject $scriptFile
     */
    public function setScriptFile(\SplFileObject $scriptFile)
    {
        $this->scriptFile = $scriptFile;
    }

    /**
     * @return \ReflectionClass
     */
    public function getReflectionClass() : \ReflectionClass
    {
        return $this->reflectionClass;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     */
    public function setReflectionClass(\ReflectionClass $reflectionClass)
    {
        $this->reflectionClass = $reflectionClass;
    }

}