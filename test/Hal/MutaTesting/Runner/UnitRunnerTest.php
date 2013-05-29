<?php

namespace Test\Hal\MutaTesting\Runner;

use Hal\MutaTesting\Runner\UnitRunner;

require_once __DIR__ . '/../../../../vendor/autoload.php';

class UnitRunnerTest extends \PHPUnit_Framework_TestCase
{

    public function testRunnerUsesAdapterToRunTests()
    {
        $adapter = $this->getMock('\Hal\MutaTesting\Runner\Adapter\AdapterInterface');
        $adapter->expects($this->once())
                ->method('run')
        ;

        $runner = new UnitRunner($adapter);
        $runner->run(null);
    }

    public function testICanGetTestsSuite()
    {
        $adapter = $this->getMock('\Hal\MutaTesting\Runner\Adapter\AdapterInterface');
        $adapter->expects($this->once())
                ->method('getSuiteResult')
        ;

        $runner = new UnitRunner($adapter);
        $runner->getSuiteResult(null);
    }

    public function testICanObtainTestedFilesFromTest()
    {
        $adapter = $this->getMock('\Hal\MutaTesting\Runner\Adapter\AdapterInterface');
        $test = $this->getMock('\Hal\MutaTesting\Test\UnitInterface');
        $adapter->expects($this->once())
                ->method('parseTestedFiles')
        ;

        $runner = new UnitRunner($adapter);
        $runner->parseTestedFiles($test);
    }

}
