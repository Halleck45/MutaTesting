<?php

namespace Hal\MutaTesting\Mutater;

use Hal\Component\Token\Token;
use Hal\MutaTesting\Mutation\MutationInterface;

class MutaterIsNotEqual extends MutaterSimpleAbstract implements MutaterInterface
{

    public function mutate(MutationInterface $original, $index)
    {
        return $this->mutateOne($original, $index, T_IS_NOT_EQUAL, new Token(array(T_IS_EQUAL, '==')));
    }

}

