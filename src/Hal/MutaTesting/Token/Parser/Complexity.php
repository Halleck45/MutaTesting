<?php

namespace Hal\MutaTesting\Token\Parser;

use Hal\MutaTesting\Token\TokenCollectionInterface;
use Hal\MutaTesting\Token\TokenInfoInterface;

/**
 * Very basic complexity implementation
 */
class Complexity implements ParserInterface
{

    private $tokens;

    public function __construct(TokenCollectionInterface $tokens)
    {
        $this->tokens = $tokens;
    }

    public function parse(TokenInfoInterface $result)
    {
        $result->setComplexity($this->getComplexity());
    }

    public function getComplexity()
    {
        $parser = new \Hal\MutaTesting\Token\TokenParser($this->tokens);

        $total = 0;
        $isFunc = false;
        foreach ($this->tokens->all() as $n => $token) {
            //
            // avoid declarations
            $previous = $parser->getPreviousNonBlank($n);
            if (T_FUNCTION === $previous[0]) {
                $isFunc = true;
            }
            if (T_STRING == $token[0] && '{' == $token[1]) {
                if (false === $isFunc) {
                    $total++;
                }
                $isFunc = false;
            }
        }
        return $total;
    }

}

