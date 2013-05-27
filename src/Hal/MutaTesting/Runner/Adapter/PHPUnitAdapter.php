<?php

namespace Hal\MutaTesting\Runner\Adapter;


use Hal\MutaTesting\Test\Collection\Factory\JUnitFactory;
use Hal\MutaTesting\Test\UnitCollectionInterface;
use Hal\MutaTesting\Test\UnitInterface;

class PHPUnitAdapter extends AdapterAbstract implements AdapterInterface
{

    public function run($path = null, array $options = array())
    {

        if (is_null($path)) {
            $path = $this->getTestDirectory();
        }

        $binary = escapeshellcmd($this->getBinary());
        $options = array_merge($this->getOptions(), $options);

        $args = '';
        foreach ($options as $option) {
            $args .= ' ' . $option;
        }
        $output = shell_exec("$binary $args $path");
        return $output;
    }

    public function runTests(UnitCollectionInterface $collection, array $options = array())
    {
        $path = '';
        foreach ($collection->all() as $unit) {
            $path.= ' ' . $unit->GetFile();
        }
        $this->run($path, $options);
    }

    public function getTestSuites()
    {
        $filename = tempnam(sys_get_temp_dir(), 'mutator-tests-suites');
        $options = array(sprintf('--log-junit %s', $filename));
        $this->run($this->getTestDirectory(), $options);

        $factory = new JUnitFactory;
        return $factory->factory(file_get_contents($filename));
    }

    public function analyzeTestedFiles(UnitInterface &$test)
    {
        // run only the test
        // adding a php auto_preprend_file with register_shutdown_function, storing included

        $filename = tempnam(sys_get_temp_dir(), 'tested-files');
        $appendingContent = '<?php
register_shutdown_function(function() {
    file_put_contents(\'' . $filename . '\', serialize( get_included_files() ));
});
            ';
        $bootstrapName = tempnam(sys_get_temp_dir(), 'bootstrap');
        file_put_contents($bootstrapName, $appendingContent);
        // @todo remove this fixe
        // We should use auto_prepend_file ini directive
        // but there is a bug ?! when we use the directeive with a phar 
        // $options = array(sprintf('-d auto_prepend_file=%s', $bootstrapName));
        $options = array(sprintf('--bootstrap %s', $bootstrapName));
        $this->run($test->getFile(), $options);


        // remove internal files
        $includedExport = unserialize(file_get_contents($filename));
        $files = array();
        foreach ($includedExport as $key => $file) {
            if (preg_match('!PHPUnit!', $file)) {
                continue;
            }
            if (preg_match('!Test.php$!', $file)) {
                continue;
            }
            if (preg_match('!phpunit.phar!i', $file)) {
                continue;
            }
            if (in_array($file, array($bootstrapName, $filename))) {
                continue;
            }
            array_push($files, $file);
        }

        $test->setTestedFiles($files);

        unlink($bootstrapName);
        unlink($filename);
        unset($files);
        unset($includedExport);
        return $test;
    }

}