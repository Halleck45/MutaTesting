<?php

namespace Hal\MutaTesting\Mutation;

class MutationCollection implements MutationCollectionInterface
{

    private $mutations;

    public function __construct()
    {
        $this->mutations = new \SplObjectStorage();
    }

    public function all()
    {
        return $this->mutations;
    }

    public function getIterator()
    {
        return $this->mutations;
    }

    public function push(MutationInterface $mutation)
    {
        $this->mutations->attach($mutation);
        return $this;
    }

}
