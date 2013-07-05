<?php

namespace Test\Hal\MutaTesting\Token;

use Hal\MutaTesting\Mutation\Mutation;
use Hal\MutaTesting\Specification\Score\AvoidDuplicateSpecification;
use Hal\MutaTesting\Token\Parser\Complexity;
use Hal\MutaTesting\Token\Parser\Coupling;
use Hal\MutaTesting\Token\TokenCollection;
use Hal\MutaTesting\Token\TokenInfo;
use Hal\MutaTesting\Token\TokenParser;
use PHPUnit_Framework_TestCase;

require_once __DIR__ . '/../../../../../vendor/autoload.php';

/**
 * @group specification
 */
class AvoidDuplicateSpecificationTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider provideDataForSpecification
     */
    public function testSpecificationIsSastisfiedIfCodeHasBenAlreadyMuted($limit, $numberOfPreviousMutation, $expected)
    {

        $previousMutations = array(
            'file1.php' => array(
                10 => 1  // line 10 muted once
                , 12 => $numberOfPreviousMutation
            )
        );

        $mutation = $this->getMock('\Hal\MutaTesting\Mutation\MutationInterface');
        $mutation->expects($this->any())
            ->method('getMutedTokensIndexes')
            ->will($this->returnValue(array(12)));
        $mutation->expects($this->any())
            ->method('getSourceFile')
            ->will($this->returnValue('file1.php'));


        $specification = new AvoidDuplicateSpecification($limit);
        $specification->setPreviousMutations($previousMutations);
        $result = $specification->isSatisfedBy($mutation, 12);

        $this->assertEquals($expected, $result);
    }

    public function provideDataForSpecification() {
        return array(
            array(3, 1, true)
            , array(3, 2, false)
        );
    }

}
