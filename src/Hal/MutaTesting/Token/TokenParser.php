<?php

namespace Hal\MutaTesting\Token;

class TokenParser
{

    private $tokens;

    public function __construct(TokenCollectionInterface $tokens)
    {
        $this->tokens = $tokens;
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

