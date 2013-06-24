<?php

namespace Hal\MutaTesting\Token\Filter;

use Hal\MutaTesting\Token\TokenCollection;
use Hal\MutaTesting\Token\TokenCollectionInterface;

class FilterWhitespace implements FilterInterface
{

    public function filter(TokenCollectionInterface $tokens)
    {
        $tokens = array_filter($tokens->all(), function($token) {
                    return $token[0] !== T_WHITESPACE;
                });
        $tokens = array_values($tokens);
        return new TokenCollection($tokens);
    }

}

