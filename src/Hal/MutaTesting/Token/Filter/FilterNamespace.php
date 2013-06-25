<?php

namespace Hal\MutaTesting\Token\Filter;

use Hal\MutaTesting\Token\TokenCollection;
use Hal\MutaTesting\Token\TokenCollectionInterface;

class FilterNamespace implements FilterInterface
{

    public function filter(TokenCollectionInterface $tokens)
    {
        $tokens = $this->replaceClassnames($tokens);
        $tokens = $this->replaceInstanciations($tokens);
        return $tokens;
    }

    private function getUsesIn($tokens)
    {
        $filter = new FilterNamespaceSeparator;
        $tokens = $filter->filter($tokens);
        $parser = new \Hal\MutaTesting\Token\TokenParser($tokens);

        // get uses
        $uses = array();
        foreach ($tokens->all() as $index => $token) {

            if ((0 === $index) || (T_WHITESPACE == $token[0])) {
                continue;
            }

            $prev = $parser->getPreviousNonBlank($index);
            switch ($prev[0]) {
                case T_USE:
                    array_push($uses, '\\' . $token[1]);
                    break;
            }
        }
        return $uses;
    }

    private function replaceInstanciations($tokens)
    {

        $filter = new FilterNamespaceSeparator;
        $tokens = $filter->filter($tokens);

        $parser = new \Hal\MutaTesting\Token\TokenParser($tokens);
        $uses = $this->getUsesIn($tokens);
        $mapped = $tokens->all();
        // replace each calls by its fullname
        $currentNamespace = '\\';
        foreach ($tokens->all() as $index => $token) {

            if (($index >= $tokens->count()) || (T_WHITESPACE == $token[0])) {
                continue;
            }
            if (preg_match('![^\\A-Za-z0-9]!', $token[1])) {
                continue;
            }

            $prev = $parser->getPreviousNonBlank($index);
            switch ($prev[0]) {
                case T_NAMESPACE:
                    $currentNamespace = '\\' . rtrim($token[1], '\\') . '\\';
                    break;
                case T_NEW:
                    $found = false;
                    $name = $token[1];
                    // look for the name in uses
                    foreach ($uses as $use) {
                        if (substr($use, 0 - strlen($name)) === $name) {
                            $fullname = $use;
                            $found = true;
                        }
                        if (!$found) {
                            if ('\\' !== $name[0]) {
                                $fullname = $currentNamespace . $token[1];
                                $found = true;
                            }
                        }

                        if ($found) {
                            $mapped[$index] = array(T_STRING, $fullname);
                        }
                    }
                    break;
            }
        }

        return new TokenCollection($mapped);
    }

    private function replaceClassnames($tokens)
    {
        // replace classes by their fullname
        $parser = new \Hal\MutaTesting\Token\TokenParser($tokens);
        $mapped = $tokens->all();

        $currentNamespace = '\\';
        foreach ($tokens->all() as $index => $token) {

            if (T_WHITESPACE === $token[0]) {
                continue;
            }

            $prev = $parser->getPreviousNonBlank($index);

            switch ($prev[0]) {
                case T_NAMESPACE:
                    $currentNamespace = '\\' . rtrim($token[1], '\\') . '\\';
                    break;
                case T_CLASS:
                    $mapped[$index] = array(T_STRING, $currentNamespace . $token[1]);
                    break;
            }
        }

        return new TokenCollection($mapped);
    }

}

