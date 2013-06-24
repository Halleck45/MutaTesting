<?php

namespace Hal\MutaTesting\Token;

class TokenParser implements Parser\ParserInterface
{

    private $parsers;
    private $tokens;

    public function __construct(TokenCollectionInterface $tokens)
    {
        $this->parsers = array(
            new Parser\Coupling($tokens)
        );
        $this->tokens = $tokens;
    }

    public function parse(TokenInfoInterface $result)
    {
        foreach ($this->parsers as $parser) {
            $parser->parse($result);
        }
        return $result;
    }

    public function getNextNonBlank($index)
    {
        $len = $this->tokens->count();
        for ($i = $index + 1; $i < $len; $i++) {
            $token = $this->tokens->get($i);
            if (T_WHITESPACE !== $token[0]) {
                return $token;
            }
        }
        return null;
    }

    public function getPreviousNonBlank($index)
    {
        $len = $this->tokens->count();
        for ($i = $index - 1; $i >= 0; $i--) {
            $token = $this->tokens->get($i);
            if (T_WHITESPACE !== $token[0]) {
                return $token;
            }
        }
        return null;
    }

}

