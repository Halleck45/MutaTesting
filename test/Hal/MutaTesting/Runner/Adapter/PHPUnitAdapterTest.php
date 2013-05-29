<?php

namespace Test\Hal\MutaTesting\Runner\Adapter;

use Hal\MutaTesting\Runner\Adapter\PHPUnitAdapter;

require_once __DIR__ . '/../../../../../vendor/autoload.php';

/**
 * @group adapter
 */
class PHPUnitAdapterTest extends \PHPUnit_Framework_TestCase
{

    private $directory;
    private $binary;

    public function setUp()
    {

        $this->binary = __DIR__ . '/../../../../resources/adapter/binary/phpunit.phar';
        $this->directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'unit' . DIRECTORY_SEPARATOR;
        if (file_exists($this->directory)) {
            foreach (glob($this->directory . '*') as $file) {
                unlink($file);
            }
            rmdir($this->directory);
        }
        mkdir($this->directory);
    }

    public function tearDown()
    {
        foreach (glob($this->directory . '*') as $file) {
            unlink($file);
        }
        rmdir($this->directory);
    }

    public function testICanRunPhpUnit()
    {
        $runner = new PHPUnitAdapter($this->binary, $this->directory);
        $output = $runner->run();
        $this->assertContains('Sebastian Bergmann', $output, 'I can run PHPUnit');
    }

    public function testICanGetTestSuites()
    {

        $filename = $this->directory . 'ExampleTest.php';
        $content = '<?php
class ExampleTest extends PHPUnit_Framework_TestCase {
    public function testEx1() {
        $this->assertEquals(1,1);
    }
}
';
        file_put_contents($filename, $content);

        $runner = new PHPUnitAdapter($this->binary, $this->directory);


        $logFile = tempnam(sys_get_temp_dir(), 'unit-test');
        $runner->run(null, array(), $logFile);
        $collection = $runner->getSuiteResult($logFile);
        $this->assertInstanceOf('\Hal\MutaTesting\Test\UnitCollectionInterface', $collection);
        $this->assertEquals(1, sizeof($collection->all()));
    }

    
    
    public function testICanGetTestedFilesFromUnitTest()
    {
        $filename = $this->directory . 'ExampleTest.php';
        $testContent = '<?php
require_once "src.php";
class ExampleTest extends PHPUnit_Framework_TestCase {
    public function testEx1() {
        $a = new A;
        $this->assertEquals(true, $a->foo() );
    }
}
';
        $srcContent = '<?php
class A {
    public function foo() { return true; }
}
';
        file_put_contents($filename, $testContent);
        file_put_contents($this->directory . 'src.php', $srcContent);
        $runner = new PHPUnitAdapter($this->binary, $this->directory);


        // @todo mock
        $unit = new \Hal\MutaTesting\Test\Unit;
        $unit->setFile($filename);
        $runner->parseTestedFiles($unit);
        
        
        $testedFiles = $unit->getTestedFiles();
        $expected = array($this->directory . 'src.php');
        $this->assertEquals($expected, $testedFiles);
    }

}