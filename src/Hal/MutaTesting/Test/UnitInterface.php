<?php

namespace Hal\MutaTesting\Test;

interface UnitInterface
{

    public function getFile();

    public function setFile($file);

    public function getName();

    public function setName($name);

    public function getTime();

    public function setTime($time);

    public function getNumOfFailures();

    public function setNumOfFailures($numOfFailures);

    public function getNumOfErrors();

    public function setNumOfErrors($numOfErrors);

    public function getNumOfAssertions();

    public function setNumOfAssertions($numOfAssertions);

    public function getTestedFiles();

    public function setTestedFiles(array $testedFiles);
}
