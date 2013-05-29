<?php

namespace Test\Hal\MutaTesting\Runner\Adapter;

use Hal\MutaTesting\Runner\Adapter\BaseAdapter;

require_once __DIR__ . '/../../../../../vendor/autoload.php';

/**
 * @group adapter
 * @group baseadapter
 */
class BaseAdapterTest extends \PHPUnit_Framework_TestCase
{

    public function testICanGetFIleToPrependInOrderToMockSources()
    {
        $mutation = $this->getMock('\Hal\MutaTesting\Mutation\MutationInterface');
        $tokens = $this->getMock('\Hal\MutaTesting\Token\TokenCollection', array(), array(array()));
        $mutation->expects($this->any())
                ->method('getTokens')
                ->will($this->returnValue($tokens));

        $adapter = new BaseAdapter(null, null);
        $filename = $adapter->createFileSystemMock($mutation);

        $this->assertTrue(file_exists($filename));
        $this->assertContains('No syntax errors detected', `php -l $filename`);
        // clean up
        unlink($filename);
    }

}