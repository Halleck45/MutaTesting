<?php

namespace Hal\MutaTesting\Specification;

class FactorySpecification
{

    public function factory($name, $option)
    {
        $class = '\Hal\MutaTesting\Specification\\' . ucfirst($name) . 'Specification';
        if (!class_exists($class)) {
            throw new \UnexpectedValueException(sprintf('specification "%s" doesn"t exist'));
        }

        return new $class($option);
    }

}
