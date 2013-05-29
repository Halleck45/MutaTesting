<?php

namespace Hal\MutaTesting\Runner\Adapter;

use Hal\MutaTesting\Mutation\MutationInterface;
use Hal\MutaTesting\Mutation\MutationInterface as MutationInterface2;
use Hal\MutaTesting\Test\UnitCollectionInterface;
use Hal\MutaTesting\Test\UnitInterface;

interface AdapterInterface
{

    /**
     * create a bootstrapper to mock file system:
     *      the file mentionned in the mutation will be virtualized in the application and replaced 
     *      with own mutated file
     * 
     * @param MutationInterface $mutation
     * @return string
     */
    public function createFileSystemMock(MutationInterface2 $mutation);

    /**
     * Runs a mutation
     * 
     * @param MutationInterface $mutation
     * @return UnitInterface
     */
    public function runMutation(MutationInterface $mutation, $options = array(), $logFile = null, $prependFile = null);

    /**
     * Run tests
     * 
     * @param string $path
     * @param array $options
     * @param string $logFile
     * @param string $prependFile
     * @return string $output
     */
    public function run($path = null, array $options = array(), $logFile = null, $prependFile = null);

    /**
     * Run specific tests
     * 
     * @param \Hal\MutaTesting\Runner\Adapter\UnitCollectionInterface $collection
     * @param array $options
     * @param string $logFile
     * @param string $prependFile
     * @see Adapter::run()
     */
    public function runTests(UnitCollectionInterface $collection, array $options = array(), $logFile = null, $prependFile = null);

    /**
     * Get results of unit test suites by the file where the junit result is logged
     * 
     * @param string $logPath
     * @return UnitCollectionInterface
     */
    public function getSuiteResult($logPath);

    /**
     * Get the binary used to run tests
     * 
     * @return string
     */
    public function getBinary();

    /**
     * Get default options used to run tests
     * 
     * @return array
     */
    public function getOptions();

    /**
     * Get the directory where test (root) are located
     * 
     * @return string
     */
    public function getTestDirectory();

    /**
     * Parse tested files of the unit test and injects them in Unit::setTestedFiles()
     * 
     * @param \Hal\MutaTesting\Test\UnitInterface $unit
     * @return \Hal\MutaTesting\Test\UnitInterface
     */
    public function parseTestedFiles(UnitInterface &$unit);
}
