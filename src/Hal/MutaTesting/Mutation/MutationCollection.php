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

    public function getSurvivors()
    {

        $collection = new MutationCollection;
        foreach ($this->mutations as $mutation) {
            $unit = $mutation->getUnit();
            if ((null === $unit) || ($unit->getNumOfFailures() == 0 && $unit->getNumOfErrors() == 0)) {
                $collection->push($mutation);
            }
        }
        return $collection;
    }

    public function count()
    {
        return sizeof($this->mutations);
    }

}
