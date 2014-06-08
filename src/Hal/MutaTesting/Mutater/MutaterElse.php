<?php

namespace Hal\MutaTesting\Mutater;

use Hal\MutaTesting\Mutation\MutationInterface;

class MutaterElse implements MutaterInterface
{

    public function mutate(MutationInterface $original, $index)
    {
        $token = $original->getTokens()->offsetGet($index);
        if ($token->getType() !== T_ELSE) {
            throw new \UnexpectedValueException(sprintf('invalid token "%s" given in %s', token_name($token->getType()), get_class($this)));
        }

        // look for the closing bracket
        $tokens = $original->getTokens();
        $len = $tokens->count();
        $end = false;
        for ($i = $index; $i < $len; $i++) {
            $token = $tokens->offsetGet($i);
            if ($token->getType() === T_STRING && $token->getValue() === '}') {
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
                ->setTestFile($original->getTestFile())
                ->setMutedTokensIndexes(array_merge($original->getMutedTokensIndexes(), range($index, $end)));

        return $new;
    }

}

