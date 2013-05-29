<?php

namespace Test\Hal\MutaTesting\Mutation;
require_once __DIR__ . '/../../../../vendor/autoload.php';
/**
 * @group mutation
 */
class MutationFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testICanFactoryMutationByCode()
    {

        $mutation = $this->getMock('\Hal\MutaTesting\Mutation\MutationInterface');
        
        $mutater = $this->getMock('\Hal\MutaTesting\Mutater\MutaterInterface');
        $mutater->expects($this->any())
                ->method('mutate')
                ->will($this->returnValue($mutation));

        $mutaterFactory = $this->getMock('\Hal\MutaTesting\Mutater\Factory\MutaterFactoryInterface');
        $mutaterFactory
                ->expects($this->any())
                ->method('factory')
                ->will($this->returnValue($mutater));



        $code = '<?php echo ok;';
        $file = '/tmp/src1.php';
        $testfile = null;
        
        $factory = new \Hal\MutaTesting\Mutation\Factory\MutationFactory($mutaterFactory);
        $instance = $factory->factory($code, $file, $testfile);
        $this->assertInstanceOf('\Hal\MutaTesting\Mutation\MutationInterface', $instance);
        $this->assertInstanceOf('\Hal\MutaTesting\Mutation\MutationCollectionInterface', $instance->getMutations());
    }

}
