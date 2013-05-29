<?php

namespace Hal\MutaTesting\Test;

interface UnitCollectionInterface extends \IteratorAggregate
{

    public function push(UnitInterface $unit);

    public function all();

    public function getByFile($file);

    public function getNumOfFailures();

    public function getNumOfErrors();

    public function getNumOfAssertions();
}
