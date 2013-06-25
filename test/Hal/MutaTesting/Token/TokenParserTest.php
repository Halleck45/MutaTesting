<?php

namespace Test\Hal\MutaTesting\Token;

use Hal\MutaTesting\Token\TokenCollection;
use Hal\MutaTesting\Token\TokenInfo;
use Hal\MutaTesting\Token\TokenParser;
use PHPUnit_Framework_TestCase;

require_once __DIR__ . '/../../../../vendor/autoload.php';

/**
 * @group tokens
 */
class TokenParserTest extends PHPUnit_Framework_TestCase
{

    public function testICanGetNonWhitespaceTokens()
    {
        $code = '<?php foo();        $this->truc(); $toto->foo()';

        $tokens = new TokenCollection(token_get_all($code));
        $parser = new TokenParser($tokens);
        
        $this->assertEquals(array(T_VARIABLE, '$this', 1), $parser->getNextNonBlank(5));
    }

}
