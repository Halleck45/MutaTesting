<?php

namespace Hal\MutaTesting\Specification;

use Hal\Component\Token\Tokenizer;
use Hal\Metrics\Complexity\Structural\CardAndAgresti\FileSystemComplexity;
use Hal\Metrics\Complexity\Text\Halstead\Halstead;
use Hal\Metrics\Complexity\Text\Halstead\Result;
use Hal\MutaTesting\Event\UnitsResultEvent;
use Hal\MutaTesting\Mutation\MutationInterface;
use Hal\MutaTesting\Token\Parser;
use Hal\MutaTesting\Token\TokenInfo;

class ScoreSpecification implements SpecificationInterface, SubscribableSpecification
{

    private $notes = array();
    private $alreadyMuted = array();
    private $limit;
    private $halstead;


    public function __construct(Halstead $halstead, $limit)
    {
        $this->halstead = $halstead;
        $this->limit = (float) $limit;
    }

    public function isSatisfedBy(MutationInterface $mutation, $index)
    {

        $filename = $mutation->getSourceFile();

        //
        // Avoid to make same mutations
        if(!isset($this->alreadyMuted[$filename])) {
            $this->alreadyMuted[$filename] = array();
        }
        foreach($mutation->getMutedTokensIndexes() as $index) {
            if(isset($this->alreadyMuted[$filename][$index])) {
                return false;
            }
            $this->alreadyMuted[$filename][$index] = 1;
        }

        //
        // keep only complex files
        if(!isset($this->notes[$filename])) {
            $bugs = $this->halstead->calculate($filename)->getBugs();
            $this->notes[$filename] = $bugs;
        }

        return $this->notes[$filename] > $this->limit;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'mutate.parseTestedFilesDone' => array('onParseTestedFilesEnd', 0)
        );
    }

    public function onParseTestedFilesEnd(UnitsResultEvent $event)
    {
        $tokenizer = new Tokenizer();
        $units = $event->getUnits();
        foreach ($units->all() as $unit) {
            $testedFiles = $unit->getTestedFiles();
            foreach ($testedFiles as $filename) {
                $tokens = new \Hal\MutaTesting\Token\TokenCollection($tokenizer->tokenize($filename));
                array_push($this->allTokens, $tokens);
            }
        }
    }

}
