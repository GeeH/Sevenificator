<?php
/**
 * Created by Gary Hockin.
 * Date: 17/06/2015
 * @GeeH
 */
declare(strict_types = 1);

namespace GeeH\SevenificatorTest;


use Doctrine\Common\Annotations\PhpParser;
use GeeH\Sevenificator\Sevenificator;
use GeeH\SevenificatorTest\Asset\MultiplePublicMethodClass;
use GeeH\SevenificatorTest\Asset\SinglePublicMethodClass;
use Monolog\Logger;
use phpDocumentor\Reflection\DocBlock;
use Symfony\Component\Console\Command\Command;
use Zend\EventManager\EventManager;

class SevenificatorTest extends \PHPUnit_Framework_TestCase
{

    public function testGetPHPFunctionDeclarationWithSimpleClass()
    {
        $reflectionClass = new \ReflectionClass(SinglePublicMethodClass::class);
        $sevenificator   = new Sevenificator($reflectionClass);
        $result          = '    public function aSingleMethod(array $array, string $string, int $int) : bool' . PHP_EOL;

        $this->assertEquals($sevenificator->getNewFunctionDeclaration('aSingleMethod'), $result);
    }

    public function testGetPHPFunctionDeclarationWithMultipleClass()
    {
        $reflectionClass = new \ReflectionClass(MultiplePublicMethodClass::class);
        $sevenificator   = new Sevenificator($reflectionClass);

        $result = '    public function oneMethod(array $array, string $string, int $int) : bool' . PHP_EOL;
        $this->assertEquals($sevenificator->getNewFunctionDeclaration('oneMethod'), $result);

        $result = '    public function twoMethod(MultiplePublicMethodClass $self, float $float, bool $bool) : string' . PHP_EOL;
        $this->assertEquals($sevenificator->getNewFunctionDeclaration('twoMethod'), $result);

        $result = '    public function threeMethod($one, int $two, $three, array $four, $five) : \\stdClass' . PHP_EOL;
        $this->assertEquals($sevenificator->getNewFunctionDeclaration('threeMethod'), $result);

        $result = '    public function fourMethod(AuthorTag $tag) : AuthorTag' . PHP_EOL;
        $this->assertEquals($sevenificator->getNewFunctionDeclaration('fourMethod'), $result);

        $result = '    public function fiveMethod(string $one = \'Orange Portal\', string $two = \'Blue Portal\', string $three = null) : bool' . PHP_EOL;
        $this->assertEquals($sevenificator->getNewFunctionDeclaration('fiveMethod'), $result);
    }

    public function testARealClass()
    {
        $reflectionClass = new \ReflectionClass(PhpParser::class);
        $sevenificator   = new Sevenificator($reflectionClass);

        $result = '    public function parseClass(\ReflectionClass $class) : array' . PHP_EOL;
        $this->assertEquals($sevenificator->getNewFunctionDeclaration('parseClass'), $result);

        $result = '    private function getFileContent(string $filename, int $lineNumber) : string' . PHP_EOL;
        $this->assertEquals($sevenificator->getNewFunctionDeclaration('getFileContent'), $result);
    }

    public function getMonologClassData()
    {
        return [
            [
                '__construct',
                '    public function __construct(string $name, array $handlers = array(), array $processors = array())'
            ],
            [
                'getName',
                '    public function getName() : string'
            ],
            [
                'pushHandler',
                '    public function pushHandler(HandlerInterface $handler) : self'
            ],
            [
                'popHandler',
                '    public function popHandler() : HandlerInterface'
            ],
            [
                'getHandlers',
                '    public function getHandlers() : array'
            ],
            [
                'pushProcessor',
                '    public function pushProcessor(callable $callback) : self'
            ],
            [
                'popProcessor',
                '    public function popProcessor() : callable'
            ],
            [
                'getProcessors',
                '    public function getProcessors() : array'
            ],
            [
                'addRecord',
                '    public function addRecord(int $level, string $message, array $context = array()) : bool'
            ],
            [
                'addDebug',
                '    public function addDebug(string $message, array $context = array()) : bool'
            ],
            [
                'getLevels',
                '    public static function getLevels() : array'
            ],
            [
                'getLevelName',
                '    public static function getLevelName(int $level) : string'
            ],
            [
                'toMonologLevel',
                '    public static function toMonologLevel($level) : int'
            ],
            [
                'isHandling',
                '    public function isHandling(int $level) : bool'
            ],
            [
                'log',
                '    public function log($level, string $message, array $context = array()) : bool'
            ],
            [
                'debug',
                '    public function debug(string $message, array $context = array()) : bool'
            ],
            [
                'setTimeZone',
                '    public static function setTimezone(\DateTimeZone $tz)'
            ],
        ];
    }

    /**
     * @dataProvider getMonologClassData
     */
    public function testMonologClass($function, $result)
    {
        $reflectionClass = new \ReflectionClass(Logger::class);
        $sevenificator   = new Sevenificator($reflectionClass);
        $result          = $result . PHP_EOL;
        $this->assertEquals($sevenificator->getNewFunctionDeclaration($function), $result);
    }

    public function getZendEventManagerClassData()
    {
        return [
            [
                '__construct',
                '    public function __construct($identifiers = null)'
            ],
            [
                'setEventClass',
                '    public function setEventClass(string $class) : EventManager'
            ],
            [
                'setSharedManager',
                '    public function setSharedManager(SharedEventManagerInterface $sharedEventManager) : EventManager'
            ],
            [
                'unsetSharedManager',
                '    public function unsetSharedManager()'
            ],
            [
                'getSharedManager',
                '    public function getSharedManager()'
            ],
            [
                'getIdentifiers',
                '    public function getIdentifiers() : array'
            ],
            [
                'setIdentifiers',
                '    public function setIdentifiers($identifiers) : EventManager',
            ],
            [
                'addIdentifiers',
                '    public function addIdentifiers($identifiers) : EventManager',
            ],
            [
                'trigger',
                '    public function trigger($event, $target = null, $argv = [], $callback = null) : ResponseCollection'
            ],
            [
                'triggerUntil',
                '    public function triggerUntil($event, $target, $argv = null, callable $callback = null) : ResponseCollection',
            ],
            [
                'attach',
                '    public function attach($event, $callback = null, int $priority = 1)',
            ],
            [
                'attachAggregate',
                '    public function attachAggregate(ListenerAggregateInterface $aggregate, int $priority = 1)'
            ],
            [
                'detach',
                '    public function detach($listener) : bool'
            ],
            [
                'detachAggregate',
                '    public function detachAggregate(ListenerAggregateInterface $aggregate)'
            ],
            [
                'getEvents',
                '    public function getEvents() : array'
            ],
            [
                'getListeners',
                '    public function getListeners(string $event) : PriorityQueue',
            ],
            [
                'clearListeners',
                '    public function clearListeners(string $event)'
            ],
            [
                'prepareArgs',
                '    public function prepareArgs(array $args) : ArrayObject'
            ],
            [
                'triggerListeners',
                '    protected function triggerListeners(string $event, EventInterface $e, $callback = null) : ResponseCollection'
            ],
            [
                'getSharedListeners',
                '    protected function getSharedListeners(string $event) : array'
            ],
            [
                'insertListeners',
                '    protected function insertListeners(PriorityQueue $masterListeners, $listeners)'
            ],


        ];
    }

    /**
     * @dataProvider getZendEventManagerClassData
     */
    public function testZendEventManager($function, $result)
    {
        $reflectionClass = new \ReflectionClass(EventManager::class);
        $sevenificator   = new Sevenificator($reflectionClass);
        $result          = $result . PHP_EOL;
        $this->assertEquals($sevenificator->getNewFunctionDeclaration($function), $result);
    }

    public function getSymphonyCommandClassData()
    {
        return [
            [
                '__construct',
                '    public function __construct($name = null)'
            ],
            [
                'ignoreValidationErrors',
                '    public function ignoreValidationErrors()'
            ],
            [
                'setApplication',
                '    public function setApplication(Application $application = null)'
            ],
            [
                'setHelperSet',
                '    public function setHelperSet(HelperSet $helperSet)'
            ],
            [
                'getHelperSet',
                '    public function getHelperSet() : HelperSet'
            ],
            [
                'getApplication',
                '    public function getApplication() : Application',
            ],
            [
                'isEnabled',
                '    public function isEnabled() : bool'
            ],
            [
                'execute',
                '    protected function execute(InputInterface $input, OutputInterface $output)'
            ],
            [
                'interact',
                '    protected function interact(InputInterface $input, OutputInterface $output)'
            ],
            [
                'initialize',
                '    protected function initialize(InputInterface $input, OutputInterface $output)'
            ],
            [
                'run',
                '    public function run(InputInterface $input, OutputInterface $output) : int'
            ],
            [
                'setCode',
                '    public function setCode(callable $code) : Command'
            ],
            [
                'mergeApplicationDefinition',
                '    public function mergeApplicationDefinition(bool $mergeArgs = true)'
            ],
            [
                'setDefinition',
                '    public function setDefinition($definition) : Command'
            ],
            [
                'getDefinition',
                '    public function getDefinition() : InputDefinition'
            ],
            [
                'getNativeDefinition',
                '    public function getNativeDefinition() : InputDefinition',
            ],
            [
                'addArgument',
                '    public function addArgument(string $name, int $mode = null, string $description = \'\', $default = null) : Command'
            ],
            [
                'addOption',
                '    public function addOption(string $name, string $shortcut = null, int $mode = null, string $description = \'\', $default = null) : Command'
            ],
            [
                'setName',
                '    public function setName(string $name) : Command'
            ],
            [
                'setProcessTitle',
                '    public function setProcessTitle(string $title) : Command'
            ],
            [
                'getName',
                '    public function getName() : string'
            ],
            [
                'setDescription',
                '    public function setDescription(string $description) : Command'
            ],
            [
                'getDescription',
                '    public function getDescription() : string'
            ],
            [
                'setHelp',
                '    public function setHelp(string $help) : Command'
            ],
            [
                'getHelp',
                '    public function getHelp() : string'
            ],
            [
                'getProcessedHelp',
                '    public function getProcessedHelp() : string',
            ],
            [
                'setAliases',
                '    public function setAliases(array $aliases) : Command'
            ],
            [
                'getAliases',
                '    public function getAliases() : array'
            ],
            [
                'getSynopsis',
                '    public function getSynopsis(bool $short = false) : string'
            ],
            [
                'addUsage',
                '    public function addUsage(string $usage)'
            ],
            [
                'getUsages',
                '    public function getUsages() : array'
            ],
            [
                'getHelper',
                '    public function getHelper(string $name)'
            ],
            [
                'asText',
                '    public function asText() : string'
            ],
            [
                'asXml',
                '    public function asXml(bool $asDom = false)'
            ],
            [
                'validateName',
                '    private function validateName(string $name)'
            ],
        ];
    }

    /**
     * @dataProvider getSymphonyCommandClassData
     */
    public function testSymphonyCommand($function, $result)
    {
        $reflectionClass = new \ReflectionClass(Command::class);
        $sevenificator   = new Sevenificator($reflectionClass);
        $result          = $result . PHP_EOL;
        $this->assertEquals($sevenificator->getNewFunctionDeclaration($function), $result);
    }

}
