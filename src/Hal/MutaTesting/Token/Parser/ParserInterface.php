<?php

namespace Hal\MutaTesting\Token\Parser;

use Hal\MutaTesting\Token\TokenCollectionInterface;
use Hal\MutaTesting\Token\TokenInfoInterface;

interface ParserInterface
{

    public function __construct(TokenCollectionInterface $tokens);

    public function parse(TokenInfoInterface $tokens);
}

