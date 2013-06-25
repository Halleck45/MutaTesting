<?php

namespace Test\Hal\MutaTesting\Token;

require_once __DIR__ . '/../../../../../vendor/autoload.php';

/**
 * @group tokens
 * @group tokensfilter
 */
class FilterNamespaceTest extends \PHPUnit_Framework_TestCase
{

    public function testICanGetTheFullNameOfDeclaredClasses()
    {
        $code = '<?php
            namespace Foo;
            class Bar {
            
            }';
        $tokens = new \Hal\MutaTesting\Token\TokenCollection(token_get_all($code));
        $filter = new \Hal\MutaTesting\Token\Filter\FilterNamespace;

        $tokens = $filter->filter($tokens);
        $this->assertEquals(array(T_STRING, '\\Foo\Bar'), $tokens->get(9));
    }

    /**
     * @group tmp
     */
    public function testICanGetTheFullNameOfCalledClasses()
    {
        $code = '<?php
            namespace Foo;
            use Bar\Example1;
            use Bar\Example2;
            $o = new Example1;';
        $tokens = new \Hal\MutaTesting\Token\TokenCollection(token_get_all($code));
        $filter = new \Hal\MutaTesting\Token\Filter\FilterNamespace;

        $tokens = $filter->filter($tokens);
        $this->assertEquals(array(T_STRING, '\\Bar\Example1'), $tokens->get(27));
    }

}
