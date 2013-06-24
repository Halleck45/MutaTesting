<?php

namespace Test\Hal\MutaTesting\Token;

use Hal\MutaTesting\Token\Parser\Complexity;
use Hal\MutaTesting\Token\Parser\Coupling;
use Hal\MutaTesting\Token\Parser\Declaration;
use Hal\MutaTesting\Token\TokenCollection;
use Hal\MutaTesting\Token\TokenInfo;
use Hal\MutaTesting\Token\TokenParser;
use PHPUnit_Framework_TestCase;

require_once __DIR__ . '/../../../../../vendor/autoload.php';

/**
 * @group tokens
 */
class TokenParserDeclarationTest extends PHPUnit_Framework_TestCase
{

    public function testICanGetListOfDeclaredClasses()
    {
        $code = '<?php class Foo { } class Foo\Bar {  public function bar() {}  }';
        $expected = array('Foo', 'Foo\Bar');

        $tokens = new TokenCollection(token_get_all($code));
        $parser = new Declaration($tokens);

        $this->assertEquals($expected, $parser->getDeclaredClasses());
    }

}
