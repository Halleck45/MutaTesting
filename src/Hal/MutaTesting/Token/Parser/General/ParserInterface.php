<?php

namespace Hal\MutaTesting\Token\Parser\General;

use Hal\MutaTesting\Token\TokenCollectionInterface;
use Hal\MutaTesting\Token\TokenInfoInterface;

interface ParserInterface
{

    public function __construct(array $listOfTokens);

    public function parse(TokenCollectionInterface $tokens, TokenInfoInterface $tokens);
}

