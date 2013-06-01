<?php

namespace Hal\MutaTesting\Test\Collection\Factory;

use Hal\MutaTesting\Test\UnitCollection;

class XUnitFactory
{

    public function factory($xmlContent)
    {
        $collection = new UnitCollection();

        $xml = simplexml_load_string($xmlContent);
        if (!$xml) {
            throw new \UnexpectedValueException('Invalid xml given');
        }


        $nodes = $xml->xpath('//testsuite/testsuite');
        if (!$nodes) {
            $nodes = $xml->xpath('//testsuites/testsuite');
        }
        foreach ($nodes as $n => $info) {
            $unit = new \Hal\MutaTesting\Test\Unit;

            $unit
                    ->setName((string) $info['name'])
                    ->setNumOfAssertions('?') // not privided by atoum
                    ->setNumOfErrors((integer) $info['errors'])
                    ->setNumOfFailures((integer) $info['failures'])
                    ->setTime((string) $info['time']);

            $testcases = $info->children();
            if (sizeof($testcases) > 0) {
                $unit->setFile((string) $testcases[0]['file']);
            }
            
            $collection->push($unit);
        }
        return $collection;
    }

}