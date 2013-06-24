<?php

namespace Test\Hal\MutaTesting\Token;

use Hal\MutaTesting\Token\Parser\Coupling;
use Hal\MutaTesting\Token\TokenCollection;
use Hal\MutaTesting\Token\TokenInfo;
use Hal\MutaTesting\Token\TokenParser;
use PHPUnit_Framework_TestCase;

require_once __DIR__ . '/../../../../../vendor/autoload.php';

/**
 * @group tokens
 */
class TokenParserCouplingTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider provideTokensForCalls
     */
    public function testIKnowCalls($code, $expected)
    {
        $tokens = new TokenCollection(token_get_all($code));
        $parser = new Coupling($tokens);
        $this->assertEquals($expected, $parser->getCalls());
    }

    public function provideTokensForCalls()
    {
        return array(
            array('<?php echo 1; foo();', array('foo' => 1))
            , array('<?php $toto->titi(); echo 1; foo($a);', array('$toto->titi' => 1, 'foo' => 1))
            , array('<?php foo(); $toto->titi(); echo 1; foo($a);', array('$toto->titi' => 1, 'foo' => 2))
            , array('<?php \Foo\foo(); foo($a);', array('\Foo\foo' => 1, 'foo' => 1))
            , array('<?php function bar() { foo(); }', array( 'foo' => 1))
        );
    }
    
    
    public function testICanGetAllDependencies() {
        $code = '<?php foo(); 
            class Toto { public function bar() { $this->truc(); } }
            $toto = new \Foo\Toto;
            $toto->foo()
            
            ';
        $expected = array(
            'foo' => 1
            ,'\Foo\Toto' => 1
        );
        
        $tokens = new TokenCollection(token_get_all($code));
        $parser = new Coupling($tokens);

        $this->assertEquals($expected, $parser->getDependencies());
    }
    
    public function testICanGetTypeHinting() {
        $code = '<?php function titi(\Foo\Toto $toto) {} public function tutu(Titi $titi, \Foo\Toto $tata) {}';
        $expected = array('\Foo\Toto' => 2, 'Titi' => 1);
        
        $tokens = new TokenCollection(token_get_all($code));
        $parser = new Coupling($tokens);

        $this->assertEquals($expected, $parser->getTypeHinting());
    }

    public function testICanGetConstructions() {
        $code = '<?php new Foo; $o = new Foo; function bar() { $var = new \Foo\Bar){ }';
        $expected = array('Foo' => 2, '\Foo\Bar' => 1);
        
        $tokens = new TokenCollection(token_get_all($code));
        $parser = new Coupling($tokens);

        $this->assertEquals($expected, $parser->getConstructions());
    }

}
