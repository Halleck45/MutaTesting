<?php

namespace Hal\MutaTesting\Test;

class Unit implements UnitInterface
{

    private $file;
    private $name;
    private $time;
    private $numOfFailures;
    private $numOfErrors;
    private $numOfAssertions;
    private $testedFiles = array();

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }

    public function getNumOfFailures()
    {
        return $this->numOfFailures;
    }

    public function setNumOfFailures($numOfFailures)
    {
        $this->numOfFailures = $numOfFailures;
        return $this;
    }

    public function getNumOfErrors()
    {
        return $this->numOfErrors;
    }

    public function setNumOfErrors($numOfErrors)
    {
        $this->numOfErrors = $numOfErrors;
        return $this;
    }

    public function getNumOfAssertions()
    {
        return $this->numOfAssertions;
    }

    public function setNumOfAssertions($numOfAssertions)
    {
        $this->numOfAssertions = $numOfAssertions;
        return $this;
    }

    public function getTestedFiles()
    {
        return $this->testedFiles;
    }

    public function setTestedFiles(array $testedFiles)
    {
        $this->testedFiles = $testedFiles;
        return $this;
    }

    public function hasFail()
    {
        return ($this->getNumOfFailures() > 0 || $this->getNumOfErrors() > 0);
    }
}
