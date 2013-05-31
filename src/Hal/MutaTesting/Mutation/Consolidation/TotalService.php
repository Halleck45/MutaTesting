<?php

namespace Hal\MutaTesting\Mutation\Consolidation;

use Hal\MutaTesting\Mutation\MutationCollection;

class TotalService
{

    private $mutations;

    public function __construct($mutations)
    {
        $mutants = new MutationCollection();
        foreach ($mutations as $mutation) {
            foreach ($mutation->getMutations() as $mutant) {
                $mutants->push($mutant);
            }
        }
        $this->mutations = $mutants;
    }

    public function getScore()
    {

        $mutants = $this->getMutants();
        $survivors = $this->getSurvivors();

        if ($mutants->count() === 0) {
            return '--';
        }

        return 100 - ceil($survivors->count() / $mutants->count() * 100);
    }

    public function getMutants()
    {
        $collection = new MutationCollection();
        foreach ($this->mutations as $mutant) {
            $collection->push($mutant);
        }
        return $collection;
    }

    public function getSurvivors()
    {
        $collection = new MutationCollection();
        foreach ($this->getMutants()->getSurvivors() as $mutant) {
            $collection->push($mutant);
        }
        return $collection;
    }

}

