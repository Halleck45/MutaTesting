<?php

namespace Hal\MutaTesting\Runner\Adapter;

use Hal\MutaTesting\Mutation\Factory\MutationFactory;
use Hal\MutaTesting\Test\UnitInterface;

class PHPUnitAdapter extends BaseAdapter implements AdapterInterface
{

    /**
     * @inherit
     */
    public function run($path = null, array $options = array(), $logFile = null, $prependFile = null)
    {
        if (!is_null($logFile)) {
            array_push($options, sprintf('--log-junit %s', $logFile));
        }

        if (!is_null($prependFile)) {
            // @todo inverse the following lines
            // We should use auto_prepend_file ini directive
            // but there is a bug ?! when we use the directeive with PHPUnit as phar
            // $options = array(sprintf('-d auto_prepend_file=%s', $bootstrapName));
            array_push($options, sprintf('--bootstrap %s', $prependFile));
        }
        return parent::run($path, $options);
    }

    /**
     * Parse tested files of the unit test and injects them in Unit::setTestedFiles()
     * 
     * @param \Hal\MutaTesting\Test\UnitInterface $unit
     * @return \Hal\MutaTesting\Test\UnitInterface
     */
    public function parseTestedFiles(UnitInterface &$unit)
    {

        $factory = new MutationFactory;
        $mutation = $factory->factoryFromUnit($unit);

        $prependFile = $this->createFileSystemMock($mutation);

        // add logger
        $filename = tempnam(sys_get_temp_dir(), 'tested-files');
        $content = '<?php
            register_shutdown_function(function() {
                file_put_contents(\'' . $filename . '\', serialize( get_included_files() ));
            });';
        file_put_contents($prependFile, $content, FILE_APPEND);

        // run mutation
        $this->runMutation($mutation, array(), null, $prependFile);

        // get files
        $includedExport = unserialize(file_get_contents($filename));
        $includedFiles = array_filter($includedExport, function($file) use($prependFile, $filename) {
                    return
                            !preg_match('!(PHPUnit)|(Test.php)|(phpunit.phar)!', $file) && !in_array($file, array($prependFile, $filename));
                });
        $unit->setTestedFiles(array_values($includedFiles));

        unlink($prependFile);
        unlink($filename);
        return $unit;
    }

}