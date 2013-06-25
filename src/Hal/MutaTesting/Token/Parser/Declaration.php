<?php

namespace Hal\MutaTesting\Token\Parser;

use Hal\MutaTesting\Token\TokenCollectionInterface;
use Hal\MutaTesting\Token\TokenInfoInterface;

/**
 * Get declared classes in tokens
 */
class Declaration implements ParserInterface
{

    private $tokens;

    public function __construct(TokenCollectionInterface $tokens)
    {
        $this->tokens = $tokens;
    }

    public function parse(TokenInfoInterface $result)
    {
        $result->setDeclaredClasses($this->getDeclaredClasses());
    }

    public function getDeclaredClasses()
    {
        $classes = array();
        $parser = new \Hal\MutaTesting\Token\TokenParser($this->tokens);

        
        $filter = new \Hal\MutaTesting\Token\Filter\FilterNamespace();
        $tokens = $filter->filter($this->tokens);


        foreach ($tokens->all() as $index => $token) {

            if (T_WHITESPACE === $token[0]) {
                continue;
            }

            if ($index > 0) {
                $prev = $parser->getPreviousNonBlank($index);
                if (T_CLASS === $prev[0]) {
                    array_push($classes, $token[1]);
                }
            }
        }


        return $classes;
    }

}

