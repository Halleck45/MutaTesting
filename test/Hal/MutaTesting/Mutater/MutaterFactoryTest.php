<?php

namespace Test\Hal\MutaTesting\Mutater;

/**
 * @group mutater
 */
class MutaterFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testICanFactoryMutaterByToken()
    {
        $token = array(0 => T_IS_EQUAL, 1 => '==', 2 => 1);

        $factory = new \Hal\MutaTesting\Mutater\Factory\MutaterFactory;
        $instance = $factory->factory($token);

        $this->assertInstanceOf('\Hal\MutaTesting\Mutater\MutaterInterface', $instance);
        $this->assertInstanceOf('\Hal\MutaTesting\Mutater\MutaterTISEQUAL', $instance);
    }

    public function testICanObtainTheNameOfMutaterFromToken()
    {
        $token = array(0 => T_IS_EQUAL, 1 => '==', 2 => 1);
        $factory = new \Hal\MutaTesting\Mutater\Factory\MutaterFactory;
        $classname = $factory->getClassnameForToken($token);

        $this->assertEquals('\Hal\MutaTesting\Mutater\MutaterTISEQUAL', $classname);
    }

}
