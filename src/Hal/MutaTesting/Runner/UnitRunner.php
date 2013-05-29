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

    public function run($path = null, array $options = array(), $logFile = null, $prependFile = null)
    {
        return $this->adapter->run($path, $options);
    }

    public function getSuiteResult($logFile)
    {
        return $this->adapter->getSuiteResult($logFile);
    }

    public function parseTestedFiles(UnitInterface &$unit)
    {
        return $this->adapter->parseTestedFiles($unit);
    }

}
