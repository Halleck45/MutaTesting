<?php

namespace Hal\MutaTesting\Token\Parser\General;

use Hal\MutaTesting\Token\TokenCollectionInterface;
use Hal\MutaTesting\Token\TokenInfoInterface;

/**
 * @todo manque les uses !
 */
class Coupling implements ParserInterface
{

    private $listOfTokens = array();
    private $prepared = false;
    private $dependencies = array();

    public function __construct(array $listOfTokens)
    {
        $this->listOfTokens = $listOfTokens;
    }

    private function prepare()
    {
        if (!$this->prepared) {

            // consolidate all calls in one array
            foreach ($this->listOfTokens as $tokens) {
                $parser = new \Hal\MutaTesting\Token\Parser\Coupling($tokens);
                $info = new \Hal\MutaTesting\Token\TokenInfo;
                $parser->parse($info);

                $dependencies = $info->getDependencies();
                foreach ($dependencies as $name => $count) {

                    // nclass name only (without the name of the method)
                    if (preg_match('!(.*?)[\-:]!', $name, $matches)) {
                        list(, $name) = $matches;
                    }
                    if (!isset($this->dependencies[$name])) {
                        $this->dependencies[$name] = 0;
                    }
                    $this->dependencies[$name] += $count;
                }
            }
        }
        $this->prepared = true;
    }

    public function parse(TokenCollectionInterface $tokens, \Hal\MutaTesting\Token\TokenInfoInterface $result)
    {
        $this->prepare();

        $parser = new \Hal\MutaTesting\Token\Parser\Declaration($tokens);
        $parser->parse($result);

        $classes = $result->getDeclaredClasses();

        $sum = 0;
        foreach ($classes as $class) {
            if (isset($this->dependencies[$class])) {
                $sum += $this->dependencies[$class];
            }
        }
        $result->setUsage($sum);
        return $result;
    }

}

