<?php

namespace Hal\MutaTesting\Token;

class TokenInfo implements TokenInfoInterface
{

    private $dependencies = array();
    private $complexity;

    public function getDependencies()
    {
        return $this->dependencies;
    }

    public function setDependencies(array $dependencies)
    {
        $this->dependencies = $dependencies;
        return $this;
    }

    public function getComplexity()
    {
        return $this->complexity;
    }

    public function setComplexity($complexity)
    {
        $this->complexity = (int) $complexity;
        return $this;
    }

}

