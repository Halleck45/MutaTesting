<?php

namespace Hal\MutaTesting\Test;

interface UnitCollectionInterface extends \IteratorAggregate
{

    public function push(UnitInterface $unit);

    public function all();
}
