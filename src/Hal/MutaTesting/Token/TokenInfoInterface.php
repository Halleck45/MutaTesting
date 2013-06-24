<?php

namespace Hal\MutaTesting\Token;

interface TokenInfoInterface
{

    public function getDependencies();

    public function setDependencies(array $dependencies);

    public function getComplexity();

    public function setComplexity($complexity);
}

