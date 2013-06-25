<?php

namespace Test\Hal\MutaTesting\Token;

require_once __DIR__ . '/../../../../../vendor/autoload.php';

/**
 * @group tokens
 * @group tokensfilter
 */
class FilterNamespaceSeparatorTest extends \PHPUnit_Framework_TestCase
{

    public function testIMergeNamespaceSeparatorsInOneString()
    {
        $code = '<?php $o = new \Foo\Bar(); function toto(Titi\Toto) {}';
        $tokens = new \Hal\MutaTesting\Token\TokenCollection(token_get_all($code));
        $filter = new \Hal\MutaTesting\Token\Filter\FilterNamespaceSeparator;

        $tokens = $filter->filter($tokens);

        $found1 = false;
        $found2 = false;
        foreach ($tokens->all() as $token) {
            if (T_STRING === $token[0] && '\Foo\Bar' === $token[1])
                $found1 = true;
            if (T_STRING === $token[0] && 'Titi\Toto' === $token[1])
                $found2 = true;
        }

        $this->assertTrue($found1, sprintf('"%s" not found in "%s"', '\Foo\Bar', $tokens->asPhp()));
        $this->assertTrue($found2, sprintf('"%s" not found in "%s"', 'Titi\Toto', $tokens->asPhp()));
    }
    
    public function testOnlySuccessivesNamespacesAreMerged()
    {
        $code = '<?php
            namespace Foo;
            use Bar\Example1;
            use Bar\Example2;
            $o = new Example1;';
        $tokens = new \Hal\MutaTesting\Token\TokenCollection(token_get_all($code));
        $filter = new \Hal\MutaTesting\Token\Filter\FilterNamespaceSeparator;

        $tokens = $filter->filter($tokens);
        $this->assertEquals(array(T_STRING, 'Example1'), $tokens->get(27));
    }

}
