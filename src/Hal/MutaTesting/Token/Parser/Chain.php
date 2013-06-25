<?php

namespace Hal\MutaTesting\Token\Parser;

use Hal\MutaTesting\Token\TokenCollectionInterface;
use Hal\MutaTesting\Token\TokenInfoInterface;

class Chain implements ParserInterface
{

    private $parsers;
    private $tokens;

    public function __construct(array $parsers, TokenCollectionInterface $tokens)
    {
        $this->parsers = $parsers;
        $this->tokens = $tokens;
    }

    public function parse(TokenInfoInterface $result)
    {
        foreach ($this->parsers as $parser) {
            $parser->parse($result);
        }
    }

}

