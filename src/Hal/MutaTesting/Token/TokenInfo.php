<?php

namespace Hal\MutaTesting\Token;

class TokenInfo implements TokenInfoInterface
{

    private $dependencies = array();
    private $complexity;
    private $usage;
    private $declaredClasses = array();

    public function getDependencies()
    {
        return $this->dependencies;
    }

    public function getCoupling()
    {
        return sizeof($this->getDependencies());
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

    public function getUsage()
    {
        return $this->usage;
    }

    public function setUsage($usage)
    {
        $this->usage = (int) $usage;
        return $this;
    }

    public function getDeclaredClasses()
    {
        return $this->declaredClasses;
    }

    public function setDeclaredClasses(array $declaredClasses)
    {
        $this->declaredClasses = $declaredClasses;
        return $this;
    }

}

