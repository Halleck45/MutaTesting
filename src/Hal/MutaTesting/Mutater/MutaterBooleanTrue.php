<?php

namespace Hal\MutaTesting\Mutater;

use Hal\Component\Token\Token;
use Hal\MutaTesting\Mutation\MutationInterface;

class MutaterBooleanTrue extends MutaterSimpleAbstract implements MutaterInterface
{

    public function mutate(MutationInterface $original, $index)
    {
        return $this->mutateOne($original, $index, T_STRING, new Token(array(T_STRING, 'false')));
    }

}

