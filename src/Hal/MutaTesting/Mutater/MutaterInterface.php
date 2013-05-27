<?php

namespace Hal\MutaTesting\Mutater;

interface MutaterInterface
{

    public function mutate(\Hal\MutaTesting\Mutation\MutationInterface $mutation, $index);
}
