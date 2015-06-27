<?php
use Asgrim\Reflector;
use GeeH\Sevenificator\Entity\ClassEntity;
use GeeH\Sevenificator\Handler\FileHandler;

/**
 * Created by Gary Hockin.
 * Date: 26/06/2015
 * @GeeH
 */
class FileHandlerTest extends PHPUnit_Framework_TestCase
{
    public function testGetNewFunctionsForSimpleClass()
    {
        global $loader;
        $reflector   = new Reflector($loader);
        $fileHandler = new FileHandler($reflector);

        $filename = 'test/Asset/SinglePublicMethodClass.php';

        /** @var ClassEntity[] $classes */
        $classes = $fileHandler->handleFile($filename);

        $this->assertInternalType('array', $classes);
        $this->assertCount(1, $classes);
        $this->assertEquals('GeeH\SevenificatorTest\Asset\SinglePublicMethodClass', $classes[0]->getClassName());
        $this->assertEquals($filename, $classes[0]->getFileName());
        $this->assertCount(1, $classes[0]->getNewFunctions());

        $this->assertEquals(
            '    public function aSingleMethod(array $array, string $string, int $int) : bool' . PHP_EOL,
            $classes[0]->getNewFunctions()['aSingleMethod']
            );
    }

    public function testGetNewFunctionsForRealClass()
    {
        global $loader;
        $reflector   = new Reflector($loader);
        $fileHandler = new FileHandler($reflector);

        $filename = 'vendor/zendframework/zend-eventmanager/src/EventManager.php';

        /** @var ClassEntity[] $classes */
        $classes = $fileHandler->handleFile($filename);

        $this->assertInternalType('array', $classes);
        $this->assertCount(1, $classes);

        $class = $classes[0];

        $this->assertEquals('Zend\EventManager\EventManager', $class->getClassName());
        $this->assertEquals($filename, $class->getFileName());
        $this->assertCount(21, $class->getNewFunctions());

    }

}
