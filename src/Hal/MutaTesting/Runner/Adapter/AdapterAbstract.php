<?php

namespace Hal\MutaTesting\Runner\Adapter;

use Hal\MutaTesting\Test\UnitInterface;

abstract class AdapterAbstract implements AdapterInterface
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

    public function getBinary()
    {
        return $this->binary;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getTestDirectory()
    {
        return $this->testDirectory;
    }

}
