<?php

namespace Hal\MutaTesting\Mutation\Factory;

use Hal\Component\Token\TokenCollection;
use Hal\Component\Token\Tokenizer;
use Hal\MutaTesting\Mutater\Factory\MutaterFactoryInterface;
use Hal\MutaTesting\Mutation\Mutation;
use Hal\MutaTesting\Specification\SpecificationInterface;
use Hal\MutaTesting\Test\UnitInterface;

class MutationFactory
{

    private $mutaterFactory;
    private $specification;

    public function __construct(MutaterFactoryInterface $mutaterFactory = null, SpecificationInterface $specification = null)
    {
        $this->mutaterFactory = $mutaterFactory;
        $this->specification = $specification;
    }

    public function factory($fileOrigin, $testFile)
    {

        $mutation = new Mutation;
        $tokenizer = new Tokenizer();
        $mutation
                ->setTokens($tokenizer->tokenize($fileOrigin))
                ->setSourceFile($fileOrigin)
                ->setTestFile($testFile);


        foreach ($mutation->getTokens() as $index => $token) {
            if ($this->mutaterFactory->isMutable($token)) {
                $mutater = $this->mutaterFactory->factory($token);
                $mutated = $mutater->mutate($mutation, $index);
                if ($this->specification->isSatisfedBy($mutated, $index)) {
                    $mutation->addMutation($mutated);
                }
            }

        }

        return $mutation;
    }

    public function factoryFromUnit(UnitInterface $unit)
    {
        $mutation = new Mutation;
        $mutation
                ->setTestFile($unit->getFile())
//                ->setTokens(new TokenCollection(token_get_all(file_get_contents($unit->getTestFile()))))
                ->setTokens(new TokenCollection(array()))
                ->setUnit($unit);
        return $mutation;
    }

}
