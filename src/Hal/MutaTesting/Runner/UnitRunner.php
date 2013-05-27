<?php

namespace Hal\MutaTesting\Runner;

use Hal\MutaTesting\Test\UnitInterface;

class UnitRunner
{

    private $adapter;

    public function __construct(Adapter\AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function run($path = null, array $options = array())
    {
        return $this->adapter->run($path, $options);
    }

    public function getTestSuites()
    {
        return $this->adapter->getTestSuites();
    }

    public function analyzeTestedFiles(UnitInterface $test)
    {
        return $this->adapter->analyzeTestedFiles($test);
    }

}
