<?php

namespace Hal\MutaTesting\Runner\Process;

class ProcessManager implements ProcessManagerInterface
{

    private $max = 5;
    private $collection;

    public function __construct($max = 5)
    {
        $this->max = (int) $max;
        $this->collection = new ProcessCollection;
    }

    public function wait()
    {

//        $process = new \Symfony\Component\Process\Process;
        ;
        $finished = false;
        while (!$finished) {
            foreach ($this->collection->all() as $n => $process) {
                $p = $process->process;
                $cb = $process->callback;

                if (!$p->isStarted() && $this->getNbRunning() < $this->max) {
                    $p->start();
                }

                if ($p->isTerminated()) {
                    $cb($p->getOutput());
                    $this->collection->remove($n);
                }
            }

            if ($this->getNbRunning() == 0) {
                $finished = true;
            }
            gc_collect_cycles();
            sleep(2);
        }
    }

    public function getNbRunning()
    {
        $n = 0;
        foreach ($this->collection->all() as $process) {
            if ($process->process->isRunning()) {
                $n++;
            }
        }
        return $n;
    }

    public function push(\Symfony\Component\Process\Process $process, callable $callback)
    {
        $this->collection->push($process, $callback);
    }

}

