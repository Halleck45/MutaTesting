<?php

namespace Hal\MutaTesting\Mutater;

use Hal\Component\Token\Token;
use Hal\MutaTesting\Mutation\MutationInterface;

class MutaterBooleanOr extends MutaterSimpleAbstract implements MutaterInterface
{

    public function mutate(MutationInterface $original, $index)
    {
        return $this->mutateOne($original, $index, T_BOOLEAN_OR, new Token(array(T_BOOLEAN_AND, '&&')));
    }

}

