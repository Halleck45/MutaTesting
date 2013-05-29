<?php

namespace Hal\MutaTesting\Mutater;

use Hal\MutaTesting\Mutation\MutationInterface;

class MutaterBooleanTrue implements MutaterInterface
{

    public function mutate(MutationInterface $original, $index)
    {
        $token = $original->getTokens()->get($index);

        $newToken = $token;
        $newToken[0] = T_STRING;
        $newToken[1] = 'false';
        
        $new = new \Hal\MutaTesting\Mutation\Mutation;
        $new
                ->setTokens($original->getTokens()->replace($index, $newToken))
                ->setUnit($original->getUnit())
                ->setSourceFile($original->getSourceFile())
                ->setTestFile($original->getTestFile());
        
        return $new;
    }

}

