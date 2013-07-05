<?php

namespace Test\Hal\MutaTesting\Mutater;

require_once __DIR__ . '/../../../../vendor/autoload.php';

/**
 * @group mutater
 */
class MutaterLogicElseTest extends \PHPUnit_Framework_TestCase
{

    public function testICanMutateElse()
    {

        $tokens = token_get_all("<?php if(1 == 2) { echo 'ok'; } else {echo 'nok'; }");
        $token = new \Hal\MutaTesting\Token\TokenCollection($tokens);
        
        $mutation = $this->getMock('\Hal\MutaTesting\Mutation\MutationInterface');
        $mutation->expects($this->any())
                ->method('getTokens')
                ->will($this->returnValue($token));
        $mutation->expects($this->any())
                ->method('getMutedTokensIndexes')
                ->will($this->returnValue(array()));



        $mutater = new \Hal\MutaTesting\Mutater\MutaterElse;
        $result = $mutater->mutate($mutation, 19);
        
        $this->assertFalse((boolean) preg_match('!else!', $result->getTokens()->asPhp()));
    }

}
