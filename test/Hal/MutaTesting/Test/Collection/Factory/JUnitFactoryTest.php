<?php

namespace Test\Hal\MutaTesting\Test\Collection\Factory;

require_once __DIR__ . '/../../../../../../vendor/autoload.php';

use Hal\MutaTesting\Test\Collection\Factory\JUnitFactory;

class JUnitFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->object = new JUnitFactory;
    }

    public function testICanFactoryUnitCollection()
    {
        $content = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<testsuites>
  <testsuite name="ArrayTest"
             file="/home/sb/ArrayTest.php"
             tests="2"
             assertions="2"
             failures="0"
             errors="0"
             time="0.016030">
    <testcase name="testNewArrayIsEmpty"
              class="ArrayTest"
              file="/home/sb/ArrayTest.php"
              line="6"
              assertions="1"
              time="0.008044"/>
    <testcase name="testArrayContainsAnElement"
              class="ArrayTest"
              file="/home/sb/ArrayTest.php"
              line="15"
              assertions="1"
              time="0.007986"/>
  </testsuite>
</testsuites>
EOT;
        $collection = $this->object->factory($content);
        $this->assertInstanceOf('\Hal\MutaTesting\Test\UnitCollectionInterface', $collection);
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testExceptionIsThrownWhenXmlIsNotValid()
    {
        $content = '';
        $collection = $this->object->factory($content);
    }

    public function testAllTestsSuitesAreFoundAndAreValid()
    {
        $content = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<testsuites>
  <testsuite name="test" tests="6" assertions="6" failures="1" errors="0" time="0.012366">
    <testsuite name="Test\Hal\MutaTesting\Runner\UnitRunnerTest" file="/home/data/www/jeff/git/mutator/MutaTesting/test/Hal/MutaTesting/Runner/UnitRunnerTest.php" namespace="Test\Hal\MutaTesting\Runner" fullPackage="Test.Hal.MutaTesting.Runner" tests="3" assertions="3" failures="0" errors="0" time="0.009188">
      <testcase name="testRunnerUsesAdapterToRunTests" class="Test\Hal\MutaTesting\Runner\UnitRunnerTest" file="/home/data/www/jeff/git/mutator/MutaTesting/test/Hal/MutaTesting/Runner/UnitRunnerTest.php" line="12" assertions="1" time="0.006536"/>
      <testcase name="testICanGetTestsSuite" class="Test\Hal\MutaTesting\Runner\UnitRunnerTest" file="/home/data/www/jeff/git/mutator/MutaTesting/test/Hal/MutaTesting/Runner/UnitRunnerTest.php" line="25" assertions="1" time="0.000485"/>
      <testcase name="testICanObtainTestedFilesFromTest" class="Test\Hal\MutaTesting\Runner\UnitRunnerTest" file="/home/data/www/jeff/git/mutator/MutaTesting/test/Hal/MutaTesting/Runner/UnitRunnerTest.php" line="36" assertions="1" time="0.002167"/>
    </testsuite>
    <testsuite name="Test\Hal\MutaTesting\Test\Collection\Factory\JUnitFactoryTest" file="/home/data/www/jeff/git/mutator/MutaTesting/test/Hal/MutaTesting/Test/Collection/Factory/JUnitFactoryTest.php" namespace="Test\Hal\MutaTesting\Test\Collection\Factory" fullPackage="Test.Hal.MutaTesting.Test.Collection.Factory" tests="3" assertions="3" failures="1" errors="0" time="0.003178">
      <testcase name="testICanFactoryUnitCollection" class="Test\Hal\MutaTesting\Test\Collection\Factory\JUnitFactoryTest" file="/home/data/www/jeff/git/mutator/MutaTesting/test/Hal/MutaTesting/Test/Collection/Factory/JUnitFactoryTest.php" line="17" assertions="1" time="0.000964"/>
      <testcase name="testExceptionIsThrownWhenXmlIsNotValid" class="Test\Hal\MutaTesting\Test\Collection\Factory\JUnitFactoryTest" file="/home/data/www/jeff/git/mutator/MutaTesting/test/Hal/MutaTesting/Test/Collection/Factory/JUnitFactoryTest.php" line="51" assertions="1" time="0.000762"/>
      <testcase name="testAllTestsSuitesAreFound" class="Test\Hal\MutaTesting\Test\Collection\Factory\JUnitFactoryTest" file="/home/data/www/jeff/git/mutator/MutaTesting/test/Hal/MutaTesting/Test/Collection/Factory/JUnitFactoryTest.php" line="57" assertions="1" time="0.001452">
        <failure type="PHPUnit_Framework_ExpectationFailedException">Test\Hal\MutaTesting\Test\Collection\Factory\JUnitFactoryTest::testAllTestsSuitesAreFound
Failed asserting that 0 matches expected 2.

/home/data/www/jeff/git/mutator/MutaTesting/test/Hal/MutaTesting/Test/Collection/Factory/JUnitFactoryTest.php:101
</failure>
      </testcase>
    </testsuite>
  </testsuite>
</testsuites>
EOT;

        $collection = $this->object->factory($content);
        $this->assertEquals(2, sizeof($collection->all()));


        foreach ($collection->all() as $unit) {
            $this->assertEquals(3, $unit->getNumOfAssertions());
            break;
        }
    }

}