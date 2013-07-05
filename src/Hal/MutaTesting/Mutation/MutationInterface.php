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

    public function getSourceFile();

    public function setSourceFile($file);

    public function getTestFile();

    public function setTestFile($file);

    public function setMutedTokensIndexes($mutedTokensIndexes);

    public function getMutedTokensIndexes();

}
