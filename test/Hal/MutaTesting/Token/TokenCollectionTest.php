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

        $base = array(
            array(T_STRING, 1)
            , array(T_STRING, 2)
            , array(T_STRING, 3)
        );
        $expected = array(
            array(T_STRING, 1)
            , array(T_STRING, 'A')
            , array(T_STRING, 3)
        );

        $tokens = new \Hal\MutaTesting\Token\TokenCollection($base);
        $tokensReplaced = $tokens->replace(1, 'A');

        $this->assertEquals($expected, $tokensReplaced->all());
        $this->assertTrue($tokens !== $tokensReplaced); // object value
    }

    public function testICanRemoveTokens()
    {
        
        $base = array(
            array(T_STRING, 1)
            , array(T_STRING, 2)
            , array(T_STRING, 3)
        );
        $expected = array(
            array(T_STRING, 2)
            , array(T_STRING, 3)
        );
        
        $tokens = new \Hal\MutaTesting\Token\TokenCollection($base);
        $tokensReplaced = $tokens->remove(0);
        $this->assertEquals($expected, $tokensReplaced->all());
        $this->assertTrue($tokens !== $tokensReplaced); // object value
    }

    public function testICanRemoveSectionOfTokens()
    {
        
        $base = array(
            array(T_STRING, 1)
            , array(T_STRING, 2)
            , array(T_STRING, 3)
            , array(T_STRING, 4)
        );
        $expected = array(
            array(T_STRING, 3)
            , array(T_STRING, 4)
        );
        
        
        $tokens = new \Hal\MutaTesting\Token\TokenCollection($base);
        $tokensReplaced = $tokens->remove(0, 1);
        $this->assertEquals($expected, $tokensReplaced->all());
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

    public function testICanCountTokens()
    {
        $tokens = new \Hal\MutaTesting\Token\TokenCollection(token_get_all('<?php echo 1;echo 2;'));
        $this->assertEquals(9, $tokens->count());
    }

}
