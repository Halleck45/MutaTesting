<?php

namespace Hal\MutaTesting\Mutation;

use Hal\MutaTesting\Test\UnitInterface;
use Hal\MutaTesting\Token\TokenCollectionInterface;

class Mutation implements MutationInterface
{

    private $tokens;
    private $mutations;
    private $unit;
    private $file;

    public function __construct()
    {
        $this->mutations = new MutationCollection();
    }

    public function getTokens()
    {
        return $this->tokens;
    }

    public function setTokens(TokenCollectionInterface $originalCode)
    {
        $this->tokens = $originalCode;
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

    public function setUnit(UnitInterface $unit = null)
    {
        $this->unit = $unit;
        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

}
