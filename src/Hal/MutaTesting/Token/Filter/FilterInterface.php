<?php

namespace Hal\MutaTesting\Token\Filter;

use Hal\MutaTesting\Token\TokenCollectionInterface;

interface FilterInterface
{

    public function filter(TokenCollectionInterface $tokens);

}

