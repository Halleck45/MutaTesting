<?php

namespace Hal\MutaTesting\Event;

use Hal\MutaTesting\Test\UnitCollectionInterface;
use Symfony\Component\EventDispatcher\Event;

class UnitsResultEvent extends Event
{

    private $units;

    public function __construct(UnitCollectionInterface $units)
    {
        $this->units = $units;
    }

    public function getUnits()
    {
        return $this->units;
    }

}
