<?php

namespace Hal\MutaTesting\Mutater;

use Hal\MutaTesting\Mutation\MutationInterface;

class MutaterBooleanTrue extends MutaterSimpleAbstract implements MutaterInterface
{

    public function mutate(MutationInterface $original, $index)
    {
        return $this->mutateOne($original, $index, T_STRING, array(T_STRING, 'false'));
    }

}

