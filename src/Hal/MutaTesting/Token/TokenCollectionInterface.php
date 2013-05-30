<?php

namespace Hal\MutaTesting\Token;

interface TokenCollectionInterface
{

    public function asPhp($tag = false);

    public function all();

    public function replace($index, $token);

    public function remove($index, $end = null);

    public function get($index);
}
