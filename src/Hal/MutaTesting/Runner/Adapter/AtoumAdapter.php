<?php

namespace Hal\MutaTesting\Runner\Adapter;

use Hal\MutaTesting\Mutation\Factory\MutationFactory;
use Hal\MutaTesting\Runner\Process\ProcessManagerInterface;
use Hal\MutaTesting\Test\Collection\Factory\XUnitFactory;
use Hal\MutaTesting\Test\UnitCollectionInterface;
use Hal\MutaTesting\Test\UnitInterface;

class AtoumAdapter extends BaseAdapter implements AdapterInterface
{

    public function __construct($binary, $testDirectory, array $options = array(), ProcessManagerInterface $processManager = null)
    {
        if (null !== $testDirectory) {
            $options = array_merge($options, array(sprintf('-d %s', $testDirectory)));
        }
        if(preg_match('!phar$!i', $binary) && !preg_match('!^php\s+!', $binary)) {
            $binary = 'php '.$binary;
        }
        parent::__construct($binary, null, $options, $processManager);
    }

    /**
     * @inherit
     */
    public function run($path = null, array $options = array(), $logFile = null, $prependFile = null, callable $callback = null)
    {

        // path
        if (null !== $path) {
            if (is_dir($path)) {
                $options[] = sprintf('-d %s', $path);
            } else {
                $options[] = sprintf('-f %s', $path);
            }
            $path = null;
        }

        if (!is_null($logFile)) {

            $bin = trim(preg_replace('!(php\s+)!i', '', $this->binary));
            $content = sprintf('?><?php require_once "%s"; ', realpath($bin) ? realpath($bin) : getcwd() . $this->binary)
                    . sprintf('$writer = new \mageekguy\atoum\writers\file("%s");', $logFile)
                    . '$xunit = new \mageekguy\atoum\reports\asynchronous\xunit();'
                    . '$xunit->addWriter($writer);'
                    . '$runner->addReport($xunit);'
                    . '?>';

            $this->addInConfiguration($content, $options);
        }


        if (!is_null($prependFile)) {
            $this->addInBootstrap(null, $options, $prependFile);
        }

        return parent::run(null, $options, null, null, $callback);
    }

    private function addInConfiguration($content, array &$options)
    {
        $file = null;
        foreach ($options as $key => $opt) {
            if (preg_match('!-c\s*(.*)!', $opt, $matches)) {
                $file = $matches[1];
                unset($options[$key]);
            }
            if (preg_match('!--configuration\s*(.*)!', $opt, $matches)) {
                $file = $matches[1];
                unset($options[$key]);
            }
        }

        if (is_null($file)) {
            $file = tempnam(sys_get_temp_dir(), 'atoum-config-default');
        }

        file_put_contents($file, $content, FILE_APPEND);
        $options[] = ' -c ' . $file;

        return $this;
    }

    private function addInBootstrap($content, array &$options, $prependFile)
    {

        $file = null;
        foreach ($options as $key => $option) {
            if (preg_match('!--bootstrap-file\s*(.*)!', $option, $matches)) {
                $file = $matches[1];
                unset($options[$key]);
            }
            if (preg_match('!-bf\s*(.*)!', $option, $matches)) {
                $file = $matches[1];
                unset($options[$key]);
            }
        }

        if (null !== $content && null !== $file) {
            $origine = $file;
            $originContent = file_get_contents($file);
            $originContent = str_replace('__FILE__', "'$origine'", $content);
            $originContent = str_replace('__DIR__', "'" . dirname($origine) . "'", $content);
            $content = $originContent . '?><?php ' . $content;

            file_put_contents($file, $content);
            file_put_contents($prependFile, sprintf("<?php require_once '%s';?>", $file), FILE_APPEND);
        }

        array_push($options, sprintf('--bootstrap-file %s', $prependFile));
        return $this;
    }

    /**
     * Parse tested files of the unit test and injects them in Unit::setTestedFiles()
     * 
     * @param UnitInterface $unit
     * @return UnitInterface
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
            });?>';
        file_put_contents($prependFile, $content);

        // run mutation
        $this->runMutation($mutation, array(), null, $prependFile);

        // get files
        $includedExport = unserialize(file_get_contents($filename));
        $includedFiles = array_filter($includedExport, function($file) use($prependFile, $filename, $unit) {
                    return
                            !preg_match('!(mageekguy\.atoum)|(vendor)|(Interface.php)!', $file)
                            // && !preg_match(sprintf('!^%s!', sys_get_temp_dir()), $file) 
                            && !in_array($file, array($prependFile, $filename, $unit->getFile()))
                    ;
                });
        $unit->setTestedFiles(array_values($includedFiles));

        return $unit;
    }

    /**
     * Get results of unit test suites by the file where the junit result is logged
     * 
     * @param string $logPath
     * @return UnitCollectionInterface
     */
    public function getSuiteResult($logPath)
    {
        $factory = new XUnitFactory();
        $results = $factory->factory(file_get_contents($logPath));
        return $results;
    }

}