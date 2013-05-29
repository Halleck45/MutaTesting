<?php

namespace Hal\MutaTesting\Runner\Process;

use Symfony\Component\Process\Process;

interface ProcessManagerInterface
{

    public function wait();

    public function push(Process $process, callable $callback);
}

