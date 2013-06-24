<?php

namespace Hal\MutaTesting\Token\Filter;

use Hal\MutaTesting\Token\TokenCollection;
use Hal\MutaTesting\Token\TokenCollectionInterface;

/**
 * Merge classnames in one T_STRING token
 * 
 * Ex:
 *      array(T_STRING, 'Foo')
 *      , array(T_NS_SEPARATOR)
 *      array(T_STRING, 'Bar')
 * 
 * Become:
 * 
 *      array(T_STRING, 'Foo\Bar')
 */
class FilterNamespaceSeparator implements FilterInterface
{

    public function filter(TokenCollectionInterface $tokens)
    {

        $map = $mapped = $tokens->all();

        // if token is not a NS_SEPARATOR OR T_STRING matching A-Z, it's changed to whitespace in a map
        foreach ($map as &$token) {
            switch ($token[0]) {
                case T_STRING:
                case T_NS_SEPARATOR:
                    if (!preg_match('![A-Za-z\\\\]!', $token[1])) {
                        $token = array(T_WHITESPACE);
                    }
                    break;
                default:
                    $token = array(T_WHITESPACE);
                    break;
            }
        }

        // we use the map to merge unchanged strings
        $prev = array(T_WHITESPACE);
        $start = false;
        foreach ($map as $k => $token) {
            // begin of future T_STRING
            if (T_WHITESPACE === $prev[0] && T_WHITESPACE !== $token[0]) {
                $start = $k;
                $mapped[$start] = array(T_STRING, '');
            }

            if (T_WHITESPACE === $token[0]) {
                $start = false;
            }

            if (false !== $start) {
                if ($start !== $k) {
                    unset($mapped[$k]);
                }
                $mapped[$start][1] .= $token[1];
            }

            $prev = $token;
        }

        $mapped = array_values($mapped);
        return new TokenCollection($mapped);
    }

}

