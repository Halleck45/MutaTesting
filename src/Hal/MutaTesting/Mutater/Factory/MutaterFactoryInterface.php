<?php

namespace Hal\MutaTesting\Mutater\Factory;

interface MutaterFactoryInterface
{

    public function factory($token);

    public function isMutable($token);

    public function getClassnameForToken($token);
}
