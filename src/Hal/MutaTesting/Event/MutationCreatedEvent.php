<?php

namespace Hal\MutaTesting\Event;

use Hal\MutaTesting\Test\UnitInterface;
use Symfony\Component\EventDispatcher\Event;

class MutationCreatedEvent extends Event
{

    private $unit;

    public function __construct(UnitInterface $unit = null)
    {
        $this->unit = $unit;
    }

    public function getUnit()
    {
        return $this->unit;
    }

}
