<?php

namespace Hal\MutaTesting\Test;

class UnitCollection implements UnitCollectionInterface
{

    private $datas = array();

    public function __construct(array $datas = array())
    {
        $this->datas = $datas;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->datas);
    }

    public function set($index, $v) {
        $this->datas[$index] = $v;
        return $this;
    }

    public function push(UnitInterface $unit)
    {
        $this->datas[] = $unit;
        return $this;
    }

    public function all()
    {
        return $this->datas;
    }

    public function getNumOfFailures()
    {
        $n = 0;
        foreach ($this->datas as $unit) {
            $n += $unit->getNumOfFailures();
        }
        return $n;
    }

    public function getNumOfErrors()
    {
        $n = 0;
        foreach ($this->datas as $unit) {
            $n += $unit->getNumOfErrors();
        }
        return $n;
    }

    public function getNumOfAssertions()
    {
        $n = 0;
        foreach ($this->datas as $unit) {
            $n += $unit->getNumOfAssertions();
        }
        return $n;
    }

    public function getByFile($file)
    {
        foreach ($this->datas as $unit) {
            if ($unit->getFile() == $file) {
                return $unit;
            }
        }
        return null;
    }

}
