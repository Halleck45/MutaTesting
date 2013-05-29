<?php

namespace Hal\MutaTesting\Mutater;

use Hal\MutaTesting\Mutation\MutationInterface;

class MutaterTISNOTEQUAL implements MutaterInterface
{

    public function mutate(MutationInterface $original, $index)
    {
        $token = $original->getTokens()->get($index);
        if ($token[0] !== T_IS_NOT_EQUAL) {
            throw new \UnexpectedValueException(sprintf('invalid token "%s" given in %s', token_name($token[0]), get_class($this)));
        }

        $newToken = $token;
        $newToken[0] = T_IS_EQUAL;
        $newToken[1] = '==';

        $new = new \Hal\MutaTesting\Mutation\Mutation;
        $new
                ->setTokens($original->getTokens()->replace($index, $newToken))
                ->setUnit($original->getUnit())
        ;
        return $new;
    }

}

