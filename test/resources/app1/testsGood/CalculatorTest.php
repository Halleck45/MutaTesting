<?php
require_once __DIR__.'/../Calculator.php';
class CalculatorTest extends PHPUnit_Framework_TestCase {
    public function testAdd(){
        $calc = new Calculator();

        $this->assertEquals(9, $calc->add(5, 4));
    }
}