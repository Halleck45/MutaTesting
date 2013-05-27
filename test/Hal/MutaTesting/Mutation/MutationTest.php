<?php

namespace Test\Hal\MutaTesting\Mutation;

/**
 * @group mutation
 */
class MutationTest extends \PHPUnit_Framework_TestCase
{

    public function testICanObtainTokens()
    {
        $code = '<?php echo ok;';
        $mutation = new \Hal\MutaTesting\Mutation\Mutation;
        $mutation->setTokens(token_get_all($code));
        $tokens = $mutation->getTokens();
        
        $this->assertEquals(5, sizeof($tokens));
    }

}
