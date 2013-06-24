<?php

namespace Hal\MutaTesting\Token;

interface TokenInfoInterface
{

    public function getDependencies();

    public function setDependencies(array $dependencies);
}

