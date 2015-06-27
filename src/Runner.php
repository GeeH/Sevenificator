<?php

namespace GeeH\Sevenificator;

use GeeH\Sevenificator\Entity\ClassEntity;

class Runner
{

    public function replaceFunctionsInFile(ClassEntity $classEntity, string $path) : int
    {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException("Cannot open path `$path`");
        }

        $filename = $path . $classEntity->getFileName();
        $this->makeDirectories($filename);

        $file = file_get_contents($classEntity->getFileName());

        $replaced = 0;

        foreach ($classEntity->getNewFunctions() as $functionName => $function) {
            $regex = '#\s+(public|private|protected).*function\s' . $functionName . '\(.*\)\n#';

            $function = PHP_EOL . $function;
            $file     = preg_replace($regex, $function, $file, -1, $count);
            if ($count > 0) {
                $replaced += $count;
            }
        }

        file_put_contents($filename, $file);

        return $replaced;
    }

    private function makeDirectories(string $filename) : bool
    {
        $fileDetails = pathinfo($filename);
        $directory = $fileDetails['dirname'];
        if(file_exists($directory)) {
            return false;
        }
        return mkdir(dirname($filename . '/'), 0777, true);

    }
}