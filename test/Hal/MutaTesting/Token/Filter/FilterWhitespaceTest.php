<?php

namespace Test\Hal\MutaTesting\Token;

require_once __DIR__ . '/../../../../../vendor/autoload.php';

/**
 * @group tokens
 * @group tokensfilter
 */
class FilterWhitespaceTest extends \PHPUnit_Framework_TestCase
{

    public function testICanRemoveWhitespacesFromTokens()
    {
        $code = '<?php echo     "ok"; foo(   1 );';
        $tokens = new \Hal\MutaTesting\Token\TokenCollection(token_get_all($code));
        $filter = new \Hal\MutaTesting\Token\Filter\FilterWhitespace;

        $tokens = $filter->filter($tokens);
        $this->assertEquals(9, $tokens->count());
    }

}
