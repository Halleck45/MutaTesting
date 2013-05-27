<?php

namespace Hal\MutaTesting\Mutation;

use Hal\MutaTesting\Test\UnitInterface;
use SplObjectStorage;

class Mutation implements MutationInterface
{

    private $originalTokens;
    private $mutations;
    private $unit;

    public function __construct()
    {
        $this->mutations = new MutationCollection();
    }

    public function getToken($index)
    {
        return isset($this->originalTokens[$index]) ? $this->originalTokens[$index] : null;
    }

    public function getTokens()
    {
        return $this->originalTokens;
    }
    
    public function replaceToken($index, $token) {
        $this->originalTokens[$index] = $token;
        return $this;
    }

    public function setTokens(array $originalCode)
    {
        $this->originalTokens = $originalCode;
        return $this;
    }

    public function addMutation(MutationInterface $mutation)
    {
        $this->mutations->push($mutation);
        return $this;
    }

    public function getMutations()
    {
        return $this->mutations;
    }

    public function getUnit()
    {
        return $this->unit;
    }

    public function setUnit(UnitInterface $unit)
    {
        $this->unit = $unit;
        return $this;
    }

}
