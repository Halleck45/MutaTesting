<?php

namespace Hal\MutaTesting\Test\Collection\Factory;

use Hal\MutaTesting\Test\UnitCollection;

class JUnitFactory
{

    public function factory($xmlContent)
    {
        $collection = new UnitCollection();

        $xml = simplexml_load_string($xmlContent);
        if (!$xml) {
            throw new \UnexpectedValueException('Invalid xml given');
        }

        foreach ($xml->xpath('//testsuite/testsuite') as $info) {
            $unit = new \Hal\MutaTesting\Test\Unit;
            $unit
                    ->setName((string) $info['name'])
                    ->setFile((string) $info['file'])
                    ->setNumOfAssertions((integer) $info['assertions'])
                    ->setNumOfErrors((integer) $info['errors'])
                    ->setNumOfFailures((integer) $info['failures'])
                    ->setTime((string) $info['time'])
            ;
            $collection->push($unit);
        }
        return $collection;
    }

}
