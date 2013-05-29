<?php

namespace Hal\MutaTesting\Mutation\Factory;

use Hal\MutaTesting\Mutater\Factory\MutaterFactoryInterface;
use Hal\MutaTesting\Mutation\Mutation;
use Hal\MutaTesting\Test\UnitInterface;
use Hal\MutaTesting\Token\TokenCollection;

class MutationFactory
{

    private $mutaterFactory;

    public function __construct(MutaterFactoryInterface $mutaterFactory = null)
    {
        $this->mutaterFactory = $mutaterFactory;
    }

    public function factory($code, $fileOrigin, $testFile)
    {

        $mutation = new Mutation;
        $mutation
                ->setTokens(new TokenCollection(token_get_all($code)))
                ->setSourceFile($fileOrigin)
                ->setTestFile($testFile);


        $tokens = token_get_all($code);
        foreach ($tokens as $index => $token) {
            if ($this->mutaterFactory->isMutable($token)) {
                $mutater = $this->mutaterFactory->factory($token);
                $mutated = $mutater->mutate($mutation, $index);
                $mutation->addMutation($mutated);
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
