<?php

namespace Hal\MutaTesting\Specification;

use Hal\MutaTesting\Mutation\MutationInterface;

class RandomSpecification implements SpecificationInterface
{

    private $max;
    private $level;

    public function __construct($level, $max = 5)
    {
        $this->max = (int) $max;
        $level = (int) $level;
        if (($level < 1) || ($level > $this->max)) {
            throw new \OutOfRangeException(sprintf('given level %s is invalid', $level));
        }
        $this->level = $level;
    }

    public function isSatisfedBy(MutationInterface $mutation)
    {
        return $this->level >= rand(1, $this->max);
    }

}
