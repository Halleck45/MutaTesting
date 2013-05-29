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
        return $this->datas;
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
