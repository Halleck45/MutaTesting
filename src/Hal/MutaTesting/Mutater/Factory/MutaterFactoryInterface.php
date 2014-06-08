<?php

namespace Hal\MutaTesting\Mutater\Factory;

use Hal\Component\Token\Token;

interface MutaterFactoryInterface
{

    public function factory(Token $token);

    public function isMutable($token);

    public function getClassnameForToken(Token $token);
}
