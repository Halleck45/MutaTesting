<?php

namespace Hal\MutaTesting\Specification;

use Hal\MutaTesting\Mutation\MutationInterface;

interface SpecificationInterface
{

    public function isSatisfedBy(MutationInterface $mutation);
}
