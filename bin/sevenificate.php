<?php

chdir(__DIR__ . '/../');

try {

    $filename  = $argv[1];
    $directory = isset($argv[2]) ? $argv[2] : 'tmp/';

    if (!file_exists($filename)) {
        throw new \Exception('Cannot find file at `' . $filename . '`');
    }

    if (!file_exists($directory)) {
        throw new \Exception('Cannot find tmp folder at `' . $directory . '`');
    }

    $loader = require_once('vendor/autoload.php');
    if (!$loader) {
        throw new \Exception('Could not load the autoloader - have you done a composer install?');
    }

    $reflector = new \Asgrim\Reflector($loader);
    $runner = new \GeeH\Sevenificator\Runner();
    $fileHandler = new \GeeH\Sevenificator\Handler\FileHandler($reflector);
    $classes = $fileHandler->handleFile($filename);

    $result = 0;
    foreach($classes as $class) {
        $result += $runner->replaceFunctionsInFile($class, $directory);
    }

    echo 'SUCCESS: Replaced ' . $result . ' functions...' . PHP_EOL;

    exit(0);

} catch (\Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}

