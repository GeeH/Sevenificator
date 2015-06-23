<?php
/**
 * Created by Gary Hockin.
 * Date: 20/06/2015
 * @GeeH
 */
declare(strict_types = 1);

namespace GeeH\Sevenificator;


use GeeH\Sevenificator\Entity\ReflectionEntity;
use Monolog\Logger;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag;


class Sevenificator
{

    /**
     * @var ReflectionEntity
     */
    private $reflectionEntity;


    /**
     * @param \ReflectionClass $reflectionClass
     */
    public function __construct(\ReflectionClass $reflectionClass)
    {
        $this->reflectionEntity = new ReflectionEntity($reflectionClass);
    }

    public function getNewFunctionDeclaration(string $functionName) : string
    {
        $docBlock           = $this->getReflectionEntity()->getReflectionDocBlock($functionName);
        $functionDefinition = $this->getReflectionEntity()->getFunctionDefinition($functionName);

        // add the strict type hinting if the parameter exists in the docblock
        /* @var Tag\ParamTag $parameter */
        foreach ($docBlock->getTagsByName('param') as $parameter) {
            $type = $parameter->getType();
            $type = $this->cleanType($type);
            $type = $this->getNamespacedType($type);

            $functionDefinition = $this->addStrictType($parameter->getVariableName(), $type, $functionDefinition);
        }

        // we have no return tag, there's nothing we can add as RTH
        $returnTag = $docBlock->getTagsByName('return');
        if (empty($returnTag)) {
            return $functionDefinition;
        }

        // we if we have multiple return tags, use the first one and ignore the rest
        $returnTag = $returnTag[0];
        // add the return type hint
        $functionDefinition = $this->addReturnType($returnTag->getType(), $functionDefinition);

        return $functionDefinition;
    }

    /**
     * @return ReflectionEntity
     */
    public function getReflectionEntity() : ReflectionEntity
    {
        return $this->reflectionEntity;
    }

    /**
     * @param ReflectionEntity $reflectionEntity
     */
    public function setReflectionEntity(ReflectionEntity $reflectionEntity)
    {
        $this->reflectionEntity = $reflectionEntity;
    }

    public function log(int $level, string $message)
    {
//        echo $level . ': ' . $message . PHP_EOL;
    }

    private function addStrictType(string $name, string $type, string $functionDefinition) : string
    {

        // something is empty, we couldn't parse properly, log this for human inspection
        if (empty($name) || empty($type) || empty($functionDefinition)) {
            $this->log(Logger::WARNING, 'Could not parse parameter type hint for `' . trim($functionDefinition) . '`'
                . ' (got name=' . $name . ', type=' . $type . ')');
            return $functionDefinition;
        }

        // yeah, um, we can't really add strict type hints for non-strict type declaration, log for human eyes
        if ($this->isMixed($type)) {
            $this->log(Logger::WARNING, 'Non-strict type for `' . trim($functionDefinition) . '`'
                . ' (got name=' . $name . ', type=' . $type . ')');
            return $functionDefinition;
        }

        // does this already have a type definition (it may not be a scalar type or may have been fixed by hand)
        $existsRegex = '#' . addslashes($type) . '\s\\' . $name . '.*?(,|\))#';
        if (preg_match($existsRegex, $functionDefinition)) {
            return $functionDefinition;
        }

        $regex              = '#\\' . $name . '.*?(,|\))#';
        $functionDefinition = preg_replace_callback($regex, function ($match) use ($type) {
            return $type . ' ' . $match[0];
        }, $functionDefinition);

        return $functionDefinition;
    }

    private function addReturnType(string $type, string $functionDefinition) : string
    {
        $originalType = $type;
        $type = $this->cleanType($type);
        $type = $this->getNamespacedType($type);

        if (empty($type)) {
            $this->log(Logger::WARNING, "`$originalType` type detected - ignoring!");
            return $functionDefinition;
        }

        // yeah, um, we can't really add return type hints for non-strict type declaration, log for human eyes
        if ($this->isMixed($type)) {
            $this->log(Logger::WARNING, 'Non-strict return type for `' . trim($functionDefinition) . '`'
                . ' (got type=' . $type . ')');
            return $functionDefinition;
        }

        $regex              = '#\)\s$#';
        $replace            = ') : ' . $type . PHP_EOL;
        $functionDefinition = preg_replace($regex, $replace, $functionDefinition);
        return $functionDefinition;
    }

    private function getNamespacedType(string $type) : string
    {

        if (strpos($type, '\\') !== 0) {
            return $type;
        }

        $file = $this->getReflectionEntity()->getScriptFile();
        $file->rewind();

        // type exists in namespace function or class definition, remove the slash
        while (!preg_match('#^(final\s)?class\s.+\s#', $file->current())) {
            $file->next();
            $regex = '#^(use|namespace|(final\s)?class)\s.*?' . addslashes(ltrim($type, '\\')) . '#';
            preg_match($regex, $file->current(), $matches);
            if (!empty($matches)) {
                return ltrim($type, '\\');
            }
        }

        // type exists in this namespace as the FQCN can be autoloaded
        $fqcn = $this->getReflectionEntity()->getReflectionClass()->getNamespaceName() . $type;
        if (class_exists($fqcn) || interface_exists($fqcn) || trait_exists($fqcn)) {
            return ltrim($type, '\\');
        }

        return $type;
    }

    private function cleanType($type)
    {

        if ($type === 'integer') {
            return 'int';
        }

        if ($type === 'double') {
            return 'float';
        }

        if ($type === 'void') {
            return '';
        }

        if (strtolower($type) === 'boolean') {
            return 'bool';
        }

        if (preg_match('#(\$)?this#', $type)) {
            return 'self';
        }

        if (preg_match('#\[\]$#', $type)) {
            return 'array';
        }


        return $type;

    }

    /**
     * Is this a mixed type (it's equal to `mixed` or has a `|` in it
     */
    private function isMixed(string $type) : bool
    {
        return $type === 'mixed' || preg_match('#.+\|.+#', $type);
    }


}