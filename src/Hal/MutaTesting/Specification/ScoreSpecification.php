<?php

namespace Hal\MutaTesting\Specification;

use Hal\MutaTesting\Event\UnitsResultEvent;
use Hal\MutaTesting\Mutation\MutationInterface;
use Hal\MutaTesting\Token\Parser;
use Hal\MutaTesting\Token\TokenInfo;

class ScoreSpecification implements SpecificationInterface, SubscribableSpecification
{

    private $limit;
    private $allTokens = array();
    private $generalParser = array();

    public function __construct()
    {
        $this->limit = 300;
        $this->generalParser = new Parser\General\Coupling($this->allTokens);
    }

    public function isSatisfedBy(MutationInterface $mutation)
    {
        $tokens = $mutation->getTokens();
        $info = new TokenInfo();
        $parser = new Parser\Chain(
                array(new Parser\Coupling($tokens), new Parser\Complexity($tokens)
                ), $tokens);
        
        $parser->parse($info);
        $this->generalParser->parse($tokens, $info);

        $sum = $info->getComplexity() * $info->getCoupling();
        return $sum > $this->limit;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'mutate.parseTestedFilesDone' => array('onParseTestedFilesEnd', 0)
        );
    }

    public function onParseTestedFilesEnd(UnitsResultEvent $event)
    {
        $units = $event->getUnits();
        foreach ($units->all() as $unit) {
            $testedFiles = $unit->getTestedFiles();
            foreach ($testedFiles as $filename) {
                $tokens = new \Hal\MutaTesting\Token\TokenCollection(token_get_all(file_get_contents($filename)));
                array_push($this->allTokens, $tokens);
            }
        }
    }

}
