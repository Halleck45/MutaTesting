<?php

namespace Hal\MutaTesting\Runner\Process;

class ProcessCollection
{

    private $processes = array();

    public function all()
    {
        return $this->processes;
    }

    public function get($n)
    {
        return $this->processes[$n];
    }

    public function push(\Symfony\Component\Process\Process $process, callable $callback)
    {
        $this->processes[] = (object) array('process' => $process, 'callback' => $callback);
        return $this;
    }

    public function remove($n) {
        unset($this->processes[$n]);
        return $this;
    }
}
