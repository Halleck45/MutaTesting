<?php

namespace Hal\MutaTesting\Runner\Adapter;

use Hal\MutaTesting\Test\UnitCollectionInterface;
use Hal\MutaTesting\Test\UnitInterface;

interface AdapterInterface
{

    public function getTestSuites();

    public function analyzeTestedFiles(UnitInterface &$test);

    public function run($path = null, array $options = array());

    public function runTests(UnitCollectionInterface $collection, array $options = array());

    public function getBinary();

    public function getOptions();

    public function getTestDirectory();
}
