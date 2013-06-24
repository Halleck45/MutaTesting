<?php

namespace Hal\MutaTesting\Token\Parser;

use Hal\MutaTesting\Token\TokenCollectionInterface;
use Hal\MutaTesting\Token\TokenInfoInterface;

class Coupling implements ParserInterface
{

    private $tokens;

    public function __construct(TokenCollectionInterface $tokens)
    {
        $this->tokens = $tokens;
    }

    public function parse(TokenInfoInterface $result)
    {
        $result->setDependencies($this->getDependencies());
    }

    public function getCalls()
    {
        // regroup namespaces
        $filter = new \Hal\MutaTesting\Token\Filter\FilterNamespaceSeparator();
        $tokens = $filter->filter($this->tokens);

        $parser = new \Hal\MutaTesting\Token\TokenParser($tokens);

        $calls = array();
        $len = $tokens->count();
        for ($i = 0; $i < $len; $i++) {

            $token = $tokens->get($i);

            if ($i + 1 > $len) {
                break;
            }

            switch ($token[0]) {
                case T_OBJECT_OPERATOR:
                    // method call
                    $next = $parser->getNextNonBlank($i + 1);
                    $asArray = $tokens->all();
                    $name = $asArray[$i - 1][1] . $asArray[$i][1] . $asArray[$i + 1][1];
                    $i++;
                    break;
                case T_STRING:
                    // function call
                    $next = $parser->getNextNonBlank($i);
                    $name = $token[1];
                    break;
                default:
                    continue 2;
            }

            if ($next[0] == T_STRING && $next[1] == '(') {

                // avoid function's declaration
                if ($i > 1) {
                    $prev = $parser->getPreviousNonBlank($i);
                    if (T_FUNCTION === $prev[0]) {
                        continue;
                    }
                }

                if (!isset($calls[$name])) {
                    $calls[$name] = 0;
                }
                $calls[$name]++;
            }
        }
        return $calls;
    }

    public function getDependencies()
    {
        $tokens = $this->tokens;
        $found = array();

        $addFound = function($name, $count) use (&$found) {
                    if (!isset($found[$name])) {
                        $found[$name] = 0;
                    }
                    $found[$name] += $count;
                };

        // 1: calls
        $calls = $this->getCalls();
        foreach ($calls as $call => $count) {
            if (!preg_match('!(^\$this)|(\->)!', $call)) { // we remove call of methods
                $addFound($call, $count);
            }
        }

        // 2: type hinting
        $types = $this->getTypeHinting();
        foreach ($types as $call => $count) {
            $addFound($call, $count);
        }

        // 3: object construction
        $constructions = $this->getConstructions();
        foreach ($constructions as $call => $count) {
            $addFound($call, $count);
        }

        return $found;
    }

    public function getTypeHinting()
    {

        $filter = new \Hal\MutaTesting\Token\Filter\FilterNamespaceSeparator();
        $tokens = $filter->filter($this->tokens)->all();

        $types = array();

        $inParenthesis = false;
        $len = sizeof($tokens);
        for ($i = 0; $i < $len; $i++) {
            $token = $tokens[$i];

            // look for '('
            if (!$inParenthesis) {
                switch ($token[0]) {
                    case T_STRING:
                        if ('(' === $token[1]) {
                            $inParenthesis = true;
                            continue 2;
                        }
                        break;
                    default:
                        $inParenthesis = false;
                        continue;
                }
            }

            // look for class or interfaces
            if ($inParenthesis) {

                switch (true) {
                    case T_STRING === $token[0] && ')' === $token[1]:
                        $inParenthesis = false;
                        continue;
                        break;
                    case T_STRING == $token[0] && !preg_match('![,$]!', $token[1]):
                        $name = $token[1];
                        if (!isset($types[$name])) {
                            $types[$name] = 0;
                        }
                        $types[$name]++;
                        break;
                }
            }
        }

        return $types;
    }

    public function getConstructions()
    {
        $filter = new \Hal\MutaTesting\Token\Filter\FilterNamespaceSeparator();
        $tokens = $filter->filter($this->tokens)->all();
        $len = sizeof($tokens);

        $calls = array();

        foreach ($tokens as $k => $token) {
            if (T_NEW === $token[0]) {

                for ($i = $k + 1; $i < $len; $i++) {
                    if (T_STRING === $tokens[$i][0]) {

                        $name = $tokens[$i][1];
                        if (!isset($calls[$name])) {
                            $calls[$name] = 0;
                        }
                        $calls[$name]++;

                        $i = $len;
                    }
                }
            }
        }

        return $calls;
    }

}

