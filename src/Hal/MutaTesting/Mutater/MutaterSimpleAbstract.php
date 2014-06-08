<?php

namespace Hal\MutaTesting\Mutater;

use Hal\Component\Token\Token;
use Hal\MutaTesting\Mutation\MutationInterface;

abstract class MutaterSimpleAbstract implements MutaterInterface
{

    protected function mutateOne(MutationInterface $original, $index, $expected, Token $newToken)
    {
        $token = $original->getTokens()->offsetGet($index);
        if ($token->getType() !== $expected) {
            throw new \UnexpectedValueException(sprintf('invalid token "%s" given in %s', token_name($token->getType()), get_class($this)));
        }

        $new = new \Hal\MutaTesting\Mutation\Mutation;
        $new
                ->setTokens($original->getTokens()->replace($index, $newToken))
                ->setUnit($original->getUnit())
                ->setSourceFile($original->getSourceFile())
                ->setTestFile($original->getTestFile())
                ->setMutedTokensIndexes(array_merge($original->getMutedTokensIndexes(), array($index)));

        return $new;
    }

}

