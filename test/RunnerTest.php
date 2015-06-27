<?php
/**
 * Created by Gary Hockin.
 * Date: 26/06/2015
 * @GeeH
 */

namespace GeeH\SevenificatorTest;


use Asgrim\Reflector;
use GeeH\Sevenificator\Handler\FileHandler;
use GeeH\Sevenificator\Runner;

class RunnerTest extends \PHPUnit_Framework_TestCase
{

    public function testReplaceFunctionsInFile()
    {
        global $loader;
        $filename = 'test/Asset/SinglePublicMethodClass.php';
        $reflector = new Reflector($loader);
        $fileHandler = new FileHandler($reflector);

        $classes = $fileHandler->handleFile($filename);

        $runner = new Runner();
        $result = $runner->replaceFunctionsInFile($classes[0], 'tmp/');

        $this->assertEquals(1, $result);
    }

    public function testReplaceFunctionsInaRealFile()
    {
        global $loader;
        $filename = 'vendor/zendframework/zend-eventmanager/src/EventManager.php';
        $reflector = new Reflector($loader);
        $fileHandler = new FileHandler($reflector);

        $classes = $fileHandler->handleFile($filename);

        $runner = new Runner();
        $result = $runner->replaceFunctionsInFile($classes[0], 'tmp/');

        $this->assertEquals(21, $result);
    }
}
