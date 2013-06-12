<?php

namespace Hal\MutaTesting\Mutater;

use Hal\MutaTesting\Mutation\MutationInterface;

abstract class MutaterSimpleAbstract implements MutaterInterface
{

    protected function mutateOne($original, $index, $expected, $newToken)
    {
        $token = $original->getTokens()->get($index);
        if ($token[0] !== $expected) {
            throw new \UnexpectedValueException(sprintf('invalid token "%s" given in %s', token_name($token[0]), get_class($this)));
        }

        $new = new \Hal\MutaTesting\Mutation\Mutation;
        $new
                ->setTokens($original->getTokens()->replace($index, $newToken))
                ->setUnit($original->getUnit())
                ->setSourceFile($original->getSourceFile())
                ->setTestFile($original->getTestFile());

        return $new;
    }

}

