<?php

namespace Hal\MutaTesting\Mutation;

use Hal\MutaTesting\Test\UnitInterface;
use Hal\MutaTesting\Token\TokenCollectionInterface;

interface MutationInterface
{

    public function getTokens();

    public function setTokens(TokenCollectionInterface $tokens);

    public function addMutation(MutationInterface $mutation);

    public function getMutations();

    public function getUnit();

    public function setUnit(UnitInterface $unit);

    public function getFile();

    public function setFile($file);
}
