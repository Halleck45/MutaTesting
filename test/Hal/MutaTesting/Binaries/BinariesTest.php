<?php

namespace Test\Hal\MutaTesting\Binaries;

require_once __DIR__ . '/../../../../vendor/autoload.php';

/**
 * @group binary
 */
class BinariesTest extends \PHPUnit_Framework_TestCase {


    public function setUp() {
        // copy phar in another directory => allow to detects errors in autoloader
        $this->path = getcwd();
        $this->phar = sys_get_temp_dir().'/mutatesting.phar';
        copy(__DIR__.'/../../../../build/mutatesting.phar', $this->phar);
        chdir(sys_get_temp_dir());
    }

    public function tearDown() {
        chdir($this->path);
        unlink($this->phar);
    }

    private function getAdapter($type) {
        $name = $type == 'phpunit' ? 'phpunit' : 'mageekguy.atoum';
        return sprintf(__DIR__.'/../../../resources/adapter/binary/%s.phar', $name);
    }

    private function getTestsPath($type) {
        return sprintf(__DIR__.'/../../../resources/app1/tests%s', ucfirst(strtolower($type)));
    }

    private function getPhar() {
        return $this->phar;
    }

    private function getBin() {
        return __DIR__.'/../../../../bin/mutatesting';
    }

    public function testICanRunBin() {

        $command = sprintf('php %1$s   %2$s %3$s %4$s --bugs=0'
            , $this->getBin()
            , 'phpunit'
            , $this->getAdapter('phpunit')
            , $this->getTestsPath('good')
        );
        $output = shell_exec($command);
        $this->assertRegExp('/Mutation testing tool/', $output);
        $this->assertRegExp('/1 assertions/', $output);
        $this->assertRegExp('/1 mutants/', $output);
        $this->assertRegExp('/0 survivors/', $output);
    }


    /**
     * @dataProvider provideDataForDetection
     */
    public function testMutantsAreKilledAndDetected($binary, $tool, $type, $nbMutants, $nbSurvivors) {

        $command = sprintf('php %1$s   %2$s %3$s %4$s --bugs=0'
            , $binary == 'bin' ? $this->getBin() : $this->getPhar()
            , $tool
            , $this->getAdapter($tool)
            , $this->getTestsPath($type)
        );
        $output = shell_exec($command);
        $this->assertRegExp(sprintf('/%d mutants/', $nbMutants), $output);
        $this->assertRegExp(sprintf('/%d survivors/', $nbSurvivors), $output);
    }

    public function provideDataForDetection() {
        return array(
            array('bin', 'phpunit', 'good', 1, 0)
            , array('bin', 'phpunit', 'bad', 1, 1)
            , array('phar', 'phpunit', 'good', 1, 0)
            , array('phar', 'phpunit', 'bad', 1, 1)
        );
    }


    public function testHtmlReportIsCreated() {

        $to = sys_get_temp_dir().'/tmpunit.html';
        $command = sprintf('php %1$s   %2$s %3$s %4$s --bugs=0 --report-html=%5$s'
            , $this->getPhar()
            , 'phpunit'
            , $this->getAdapter('phpunit')
            , $this->getTestsPath('good')
            , $to
        );
        shell_exec($command);


        $this->assertFileExists($to);
        $content = file_get_contents($to);
        $this->assertRegExp('<html>', $content);
        $this->assertRegExp('<body>', $content);
        unlink($to);
    }

}