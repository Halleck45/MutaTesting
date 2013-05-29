<?php

namespace Hal\MutaTesting\Event;

use Hal\MutaTesting\Test\UnitCollectionInterface;
use Symfony\Component\EventDispatcher\Event;

class MutationsDoneEvent extends Event
{

    private $mutations;

    public function __construct(array $mutations)
    {
        $this->mutations = $mutations;
    }

    public function getMutations()
    {
        return $this->mutations;
    }

}
