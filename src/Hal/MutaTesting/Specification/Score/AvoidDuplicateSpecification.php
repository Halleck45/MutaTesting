<?php

namespace Hal\MutaTesting\Specification\Score;

use Hal\MutaTesting\Event\UnitsResultEvent;
use Hal\MutaTesting\Mutation\MutationInterface;
use Hal\MutaTesting\Specification\SpecificationInterface;
use Hal\MutaTesting\Specification\SubscribableSpecification;
use Hal\MutaTesting\Token\Parser;
use Hal\MutaTesting\Token\TokenInfo;

class AvoidDuplicateSpecification implements SpecificationInterface, SubscribableSpecification
{

    private $previousMutations = array();
    private $limit;

    public function __construct($limit = 3)
    {
        $this->limit = (int) $limit;
    }

    public function isSatisfedBy(MutationInterface $mutation, $index)
    {
        $count = $this->getCountOfPreviousMutationsFor($mutation, $index);
        return ($count + 1) < $this->limit;
    }

    private function getCountOfPreviousMutationsFor(MutationInterface $mutation, $index) {
        $file = $mutation->getSourceFile();
        if(!isset($this->previousMutations[$file])) {
            return 0;
        }
        if(!isset($this->previousMutations[$file][$index])) {
            return 0;
        }
        return $this->previousMutations[$file][$index];
    }


    public static function getSubscribedEvents()
    {
        return array(
            'mutate.parseTestedFilesDone' => array('onParseTestedFilesEnd', 0)
        );
    }

    public function onParseTestedFilesEnd(UnitsResultEvent $event)
    {

    }

    public function setPreviousMutations(array $previousMutations)
    {
        $this->previousMutations = $previousMutations;
    }

    public function getPreviousMutations()
    {
        return $this->previousMutations;
    }


}
