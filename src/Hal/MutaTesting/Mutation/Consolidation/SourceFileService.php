<?php

namespace Hal\MutaTesting\Mutation\Consolidation;

use Hal\MutaTesting\Mutation\MutationCollection;

class SourceFileService
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

    public function getMutants($file)
    {
        $collection = new MutationCollection();
        foreach ($this->mutations as $mutant) {
            if ($file === $mutant->getSourceFile()) {
                $collection->push($mutant);
            }
        }
        return $collection;
    }

    public function getScore($file)
    {
        $mutants = $this->getMutants($file);
        $survivors = $this->getSurvivors($file);

        if ($mutants->count() === 0) {
            return '--';
        }

        return 100 - ceil($survivors->count() / $mutants->count() * 100);
    }

    public function getSurvivors($file)
    {
        $collection = new MutationCollection();
        foreach ($this->getMutants($file)->getSurvivors() as $mutant) {
            $collection->push($mutant);
        }
        return $collection;
    }

    public function getAvailableFiles()
    {
        $files = array();
        foreach ($this->mutations as $mutant) {
            if (!in_array($mutant->getSourceFile(), $files)) {
                array_push($files, $mutant->getSourceFile());
            }
        }
        return $files;
    }

}

