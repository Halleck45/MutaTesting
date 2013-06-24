<?php

namespace Test\Hal\MutaTesting\Token;

use Hal\MutaTesting\Token\Parser\Complexity;
use Hal\MutaTesting\Token\Parser\Coupling;
use Hal\MutaTesting\Token\TokenCollection;
use Hal\MutaTesting\Token\TokenInfo;
use Hal\MutaTesting\Token\TokenParser;
use PHPUnit_Framework_TestCase;

require_once __DIR__ . '/../../../../../vendor/autoload.php';

/**
 * @group tokens
 */
class TokenParserComplexityTest extends PHPUnit_Framework_TestCase
{

    public function testICanGetComplexityOfCode() {
        $code = '<?php
            function titi() { 
                if(true) {
                
                } else {
                
                }
            }
            
            if(false) {
            
            }
            ';
        
        $tokens = new TokenCollection(token_get_all($code));
        $parser = new Complexity($tokens);

        $this->assertEquals(3, $parser->getComplexity());
    }

}
