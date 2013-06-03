<?php

namespace Test\Hal\MutaTesting\Token;

use Hal\MutaTesting\Runner\UnitRunner;

require_once __DIR__ . '/../../../../vendor/autoload.php';

/**
 * @group tokens
 */
class TokenCollectionTest extends \PHPUnit_Framework_TestCase
{

    public function testICanReplaceTokens()
    {
        $tokens = new \Hal\MutaTesting\Token\TokenCollection(array(1, 2, 3));
        $tokensReplaced = $tokens->replace(0, 'A');

        $this->assertEquals(array('A', 2, 3), $tokensReplaced->all());
        $this->assertTrue($tokens !== $tokensReplaced); // object value
    }

    public function testICanRemoveTokens()
    {
        $tokens = new \Hal\MutaTesting\Token\TokenCollection(array(1, 2, 3));
        $tokensReplaced = $tokens->remove(0);
        $this->assertEquals(array(1 => 2, 2 => 3), $tokensReplaced->all());
        $this->assertTrue($tokens !== $tokensReplaced); // object value
    }

    public function testICanRemoveSectionOfTokens()
    {
        $tokens = new \Hal\MutaTesting\Token\TokenCollection(array(1, 2, 3));
        $tokensReplaced = $tokens->remove(0, 1);
        $this->assertEquals(array(2 => 3), $tokensReplaced->all());
        $this->assertTrue($tokens !== $tokensReplaced); // object value
    }

    /**
     * @dataProvider provideTokenizePhp
     */
    public function testICanGetPhpRepresentationOfTokens($code, $withOpenTag, $expectedCode)
    {
        $tokens = new \Hal\MutaTesting\Token\TokenCollection(token_get_all($code));
        $this->assertEquals($expectedCode, $tokens->asPhp($withOpenTag));
    }

    public function provideTokenizePhp()
    {
        return array(
            array('<?php echo "ok";', false, 'echo "ok";')
            , array('<?php echo "ok";', true, '<?php echo "ok";')
            , array('<?php if(true){}', false, 'if(true){}')
        );
    }

}
