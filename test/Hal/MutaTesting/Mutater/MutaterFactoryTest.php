<?php

namespace Test\Hal\MutaTesting\Mutater;

use Hal\Component\Token\Token;

require_once __DIR__ . '/../../../../vendor/autoload.php';

/**
 * @group mutater
 */
class MutaterFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testICanFactoryMutaterByToken()
    {
        $token = new Token(array(0 => T_IS_EQUAL, 1 => '==', 2 => 1));

        $factory = new \Hal\MutaTesting\Mutater\Factory\MutaterFactory;
        $instance = $factory->factory($token);

        $this->assertInstanceOf('\Hal\MutaTesting\Mutater\MutaterInterface', $instance);
        $this->assertInstanceOf('\Hal\MutaTesting\Mutater\MutaterIsEqual', $instance);
    }

    public function testICanObtainTheNameOfMutaterFromToken()
    {
        $token = new Token(array(0 => T_IS_EQUAL, 1 => '==', 2 => 1));
        $factory = new \Hal\MutaTesting\Mutater\Factory\MutaterFactory;
        $classname = $factory->getClassnameForToken($token);

        $this->assertEquals('\Hal\MutaTesting\Mutater\MutaterIsEqual', $classname);
    }

}
