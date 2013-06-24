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

    public function testICanParseTokens()
    {
        $code = '<?php foo(); $this->truc(); $toto->foo()';

        $tokens = new TokenCollection(token_get_all($code));
        $parser = new TokenParser($tokens);
        $result = $this->getMock('Hal\MutaTesting\Token\TokenInfoInterface');
        $result->expects($this->any())->method('setCalls')->will($this->returnValue($result));

        $parser->parse($result);
    }

}
