<?php

namespace Hal\MutaTesting\Mutation;

use Hal\MutaTesting\Test\UnitInterface;

interface MutationInterface
{

    public function getTokens();

    public function getToken($index);

    public function replaceToken($index, $token);

    public function setTokens(array $tokens);

    public function addMutation(MutationInterface $mutation);

    public function getMutations();

    public function getUnit();

    public function setUnit(UnitInterface $unit);
}
