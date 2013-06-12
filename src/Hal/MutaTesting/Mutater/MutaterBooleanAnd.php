<?php

namespace Hal\MutaTesting\Mutater;

use Hal\MutaTesting\Mutation\MutationInterface;

class MutaterBooleanAnd extends MutaterSimpleAbstract implements MutaterInterface
{

    public function mutate(MutationInterface $original, $index)
    {
        return $this->mutateOne($original, $index, T_BOOLEAN_AND, array(T_BOOLEAN_OR, '||'));
    }

}

