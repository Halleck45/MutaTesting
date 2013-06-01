<?php

namespace Test\Hal\MutaTesting\Runner\Adapter;

use Hal\MutaTesting\Runner\Adapter\AtoumAdapter;

require_once __DIR__ . '/../../../../../vendor/autoload.php';

/**
 * @group adapter
 * @group atoum
 */
class AtoumAdapterTest extends \PHPUnit_Framework_TestCase
{

    private $directory;
    private $binary;

    public function setUp()
    {

        $this->binary = 'php ' . __DIR__ . '/../../../../resources/adapter/binary/mageekguy.atoum.phar';
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

    public function testICanRunAtoum()
    {
        $runner = new AtoumAdapter($this->binary, null);
        $output = $runner->run();
        $this->assertContains('Running duration', $output, 'I can run atoum');
    }

    public function testICanGetTestSuites()
    {

        $filename = $this->directory . 'ExampleTest.php';
        $content = '<?php
            namespace vendor\project\tests\units;
            require_once "ExampleSrc.php";
            use \mageekguy\atoum;
            use \vendor\project;
            class helloWorld extends atoum\test {
                public function testSay() {
                    $helloWorld = new project\helloWorld();
                    $this->string($helloWorld->say())->isEqualTo("Hello World!")
                    ;
                }
            }
        ';
        file_put_contents($filename, $content);

        $filename = $this->directory . 'ExampleSrc.php';
        $content = '<?php
        namespace vendor\project;
        class helloWorld {
            public function say() {
                return "Hello World!";
            }
        }
        ';
        file_put_contents($filename, $content);

        $runner = new AtoumAdapter($this->binary, $this->directory);


        $logFile = tempnam(sys_get_temp_dir(), 'unit-test');
        $runner->run(null, array(), $logFile);
        $collection = $runner->getSuiteResult($logFile);
        $this->assertInstanceOf('\Hal\MutaTesting\Test\UnitCollectionInterface', $collection);
        $this->assertEquals(1, sizeof($collection->all()));
        
        $unit = $collection->getByFile($this->directory . 'ExampleTest.php');
        $this->assertInstanceOf('\Hal\MutaTesting\Test\UnitInterface', $unit);
    }
    
    public function testICanGetTestSuitesWithMultipleTests()
    {

        // tests
        $filename = $this->directory . 'ExampleTest1.php';
        $content = '<?php
            namespace vendor\project\tests\units;
            require_once "ExampleSrc1.php";
            use \mageekguy\atoum;
            use \vendor\project;
            class helloWorld1 extends atoum\test {
                public function testSay() {
                    $this->string("1")->isEqualTo("2")
                    ;
                }
            }
        ';
        file_put_contents($filename, $content);
        
        $filename = $this->directory . 'ExampleTest2.php';
        $content = '<?php
            namespace vendor\project\tests\units;
            require_once "ExampleSrc2.php";
            use \mageekguy\atoum;
            use \vendor\project;
            class helloWorld2 extends atoum\test {
                public function testSay() {
                    $this->string("1")->isEqualTo("1")
                    ;
                }
            }
        ';
        file_put_contents($filename, $content);
        
        // sources
        $filename = $this->directory . 'ExampleSrc1.php';
        $content = '<?php
        namespace vendor\project;
        class helloWorld1 {
            public function say() {
                return "Hello World!";
            }
            public function foo() {
                return "Hello foo";
            }
        }
        ';
        file_put_contents($filename, $content);
        $filename = $this->directory . 'ExampleSrc2.php';
        $content = '<?php
        namespace vendor\project;
        class helloWorld2 {
            public function say() {
                return "Hello World!";
            }
            public function foo() {
                return "Hello foo";
            }
        }
        ';
        file_put_contents($filename, $content);

        $runner = new AtoumAdapter($this->binary, $this->directory);


        $logFile = tempnam(sys_get_temp_dir(), 'unit-test');
        $runner->run(null, array(), $logFile);
        $collection = $runner->getSuiteResult($logFile);
        $this->assertInstanceOf('\Hal\MutaTesting\Test\UnitCollectionInterface', $collection);
        $this->assertEquals(2, sizeof($collection->all()));
        
        $unit = $collection->getByFile($this->directory . 'ExampleTest1.php');
        $this->assertInstanceOf('\Hal\MutaTesting\Test\UnitInterface', $unit);
    }


    public function testICanGetTestedFilesFromUnitTest()
    {
        $filename = $this->directory . 'ExampleTest.php';
        $content = '<?php
            namespace vendor\project\tests\units;
            require_once "ExampleSrc.php";
            use \mageekguy\atoum;
            use \vendor\project;
            class helloWorld extends atoum\test {
                public function testSay() {
                    $helloWorld = new project\helloWorld();
                    $this->string($helloWorld->say())->isEqualTo("Hello World!")
                    ;
                }
            }
        ';
        file_put_contents($filename, $content);

        $filename = $this->directory . 'ExampleSrc.php';
        $content = '<?php
        namespace vendor\project;
        class helloWorld {
            public function say() {
                return "Hello World!";
            }
        }
        ';
        file_put_contents($filename, $content);

        $runner = new AtoumAdapter($this->binary, $this->directory);
        // @todo mock
        $unit = new \Hal\MutaTesting\Test\Unit;
        $unit->setFile($this->directory . 'ExampleTest.php');
        $runner->parseTestedFiles($unit);
        
        
        $testedFiles = $unit->getTestedFiles();
        $expected = array($this->directory . 'ExampleSrc.php');
        $this->assertEquals($expected, $testedFiles);
    }
}