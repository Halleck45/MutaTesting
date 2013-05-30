<?php

namespace Hal\MutaTesting\Mutater;

use Hal\MutaTesting\Mutation\MutationInterface;

class MutaterElse implements MutaterInterface
{

    public function mutate(MutationInterface $original, $index)
    {
        $token = $original->getTokens()->get($index);
        if ($token[0] !== T_ELSE) {
            throw new \UnexpectedValueException(sprintf('invalid token "%s" given in %s', token_name($token[0]), get_class($this)));
        }

        // look for the closing bracket
        $tokens = $original->getTokens();
        $len = sizeof($tokens->all());
        $end = false;
        for ($i = $index; $i < $len; $i++) {
            $token = $tokens->get($i);
            if (!isset($token[1])) {
                $token = array(T_STRING, $token[0]);
            }
            // "}"
            if ($token[0] === T_STRING && $token[1] === '}') {
                $end = $i;
                break;
            }
        }
        if (false === $end) {
            throw new \OutOfRangeException('closing bracket not found for else');
        }


        // remove all concerned tokens
        $tokens = $tokens->remove($index, $end);


        $new = new \Hal\MutaTesting\Mutation\Mutation;
        $new
                ->setTokens($tokens)
                ->setUnit($original->getUnit())
                ->setSourceFile($original->getSourceFile())
                ->setTestFile($original->getTestFile());

        return $new;
    }

}

