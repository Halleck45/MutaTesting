<?php

namespace Hal\MutaTesting\Runner\Adapter;

use Hal\MutaTesting\Mutation\MutationInterface;
use Hal\MutaTesting\Test\Collection\Factory\JUnitFactory;
use Hal\MutaTesting\Test\UnitCollectionInterface;
use Hal\MutaTesting\Test\UnitInterface;

class BaseAdapter implements AdapterInterface
{

    protected $binary;
    protected $options;
    protected $testDirectory;

    public function __construct($binary, $testDirectory, array $options = array())
    {
        $this->binary = $binary;
        $this->options = $options;
        $this->testDirectory = $testDirectory;
    }

    /**
     * create a bootstrapper to mock file system:
     *      the file mentionned in the mutation will be virtualized in the application and replaced 
     *      with own mutated file
     * 
     * @param MutationInterface $mutation
     * @return string
     */
    public function createFileSystemMock(MutationInterface $mutation)
    {
        // temporary file
        $temporaryFile = tempnam(sys_get_temp_dir(), 'mutate-mock');
        file_put_contents($temporaryFile, $mutation->getTokens()->asPhp());

        // mocking system
        $bootstrapContent = ''
                . file_get_contents(__DIR__ . '/../../StreamWrapper/FileMutator.php')
                . "\n \Hal\MutaTesting\StreamWrapper\FileMutator::initialize();"
                . sprintf("\n \Hal\MutaTesting\StreamWrapper\FileMutator::addMutatedFile('%s', '%s'); ?>"
                        , $mutation->getFile(), $temporaryFile);

        $bootstrapFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bootstrap-' . md5($mutation->getFile()) . '.php';
        file_put_contents($bootstrapFile, $bootstrapContent);

        return $bootstrapFile;
    }

    /**
     * Runs a mutation
     * 
     * @param MutationInterface $mutation
     * @return UnitInterface
     */
    public function runMutation(MutationInterface $mutation, $options = array(), $logFile = null, $prependFile = null)
    {
        if (is_null($prependFile)) {
            $prependFile = $this->createFileSystemMock($mutation);
        }
        if (is_null($logFile)) {
            $logFile = tempnam(sys_get_temp_dir(), 'mutate-junit');
        }
        $this->run($mutation->getFile(), array(), $logFile, $prependFile);

        $results = $this->getSuiteResult($logFile);
        return $results->getByFile($mutation->getFile());
    }

    /**
     * Run tests
     * 
     * @param string $path
     * @param array $options
     * @param string $logFile
     * @param string $prependFile
     * @return string $output
     */
    public function run($path = null, array $options = array(), $logFile = null, $prependFile = null)
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

    /**
     * Run specific tests
     * 
     * @param \Hal\MutaTesting\Runner\Adapter\UnitCollectionInterface $collection
     * @param array $options
     * @param string $logFile
     * @param string $prependFile
     * @see Adapter::run()
     */
    public function runTests(UnitCollectionInterface $collection, array $options = array(), $logFile = null, $prependFile = null)
    {
        $path = '';
        foreach ($collection->all() as $unit) {
            $path.= ' ' . $unit->GetFile();
        }
        $this->run($path, $options, $logFile, $prependFile);
    }

    /**
     * Get results of unit test suites by the file where the junit result is logged
     * 
     * @param string $logPath
     * @return UnitCollectionInterface
     */
    public function getSuiteResult($logPath)
    {
        $factory = new JUnitFactory;
        $results = $factory->factory(file_get_contents($logPath));
        return $results;
    }

    public function parseTestedFiles(UnitInterface &$unit)
    {
        throw new Exception('Please override me');
    }

    /**
     * Get the binary used to run tests
     * 
     * @return string
     */
    public function getBinary()
    {
        return $this->binary;
    }

    /**
     * Get default options used to run tests
     * 
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get the directory where test (root) are located
     * 
     * @return string
     */
    public function getTestDirectory()
    {
        return $this->testDirectory;
    }

}
