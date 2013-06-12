<?php

namespace Hal\MutaTesting\Mutater;

use Hal\MutaTesting\Mutation\MutationInterface;

class MutaterIsEqual extends MutaterSimpleAbstract implements MutaterInterface
{

    public function mutate(MutationInterface $original, $index)
    {
        return $this->mutateOne($original, $index, T_IS_EQUAL, array(T_IS_NOT_EQUAL, '!='));
    }

}

