<?php

namespace Test\Hal\MutaTesting\Mutation;
require_once __DIR__ . '/../../../../vendor/autoload.php';
/**
 * @group mutation
 */
class MutationTest extends \PHPUnit_Framework_TestCase
{

    public function testICanObtainTokens()
    {
        $code = '<?php echo ok;';
        $mutation = new \Hal\MutaTesting\Mutation\Mutation;
        $mutation->setTokens(new \Hal\MutaTesting\Token\TokenCollection(token_get_all($code)));
        $tokens = $mutation->getTokens();
        
        $this->assertEquals(5, sizeof($tokens->all()));
    }

}
