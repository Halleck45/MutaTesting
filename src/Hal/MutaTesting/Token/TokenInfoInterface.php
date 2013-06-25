<?php

namespace Hal\MutaTesting\Token;

interface TokenInfoInterface
{

    public function getDependencies();

    public function getCoupling();

    public function setDependencies(array $dependencies);

    public function getComplexity();

    public function setComplexity($complexity);

    public function getUsage();

    public function setUsage($sum);

    public function getDeclaredClasses();

    public function setDeclaredClasses(array $declaredClasses);
}

