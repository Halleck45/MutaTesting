<?php

namespace Hal\MutaTesting\Mutation\Factory;

class MutationFactory
{

    private $mutaterFactory;

    public function __construct(\Hal\MutaTesting\Mutater\Factory\MutaterFactoryInterface $mutaterFactory)
    {
        $this->mutaterFactory = $mutaterFactory;
    }

    public function factory($code)
    {

        $mutation = new \Hal\MutaTesting\Mutation\Mutation;
        $mutation->setTokens(token_get_all($code));


        $tokens = token_get_all($code);
        foreach ($tokens as $index => $token) {
            if ($this->mutaterFactory->isMutable($token)) {
                $mutater = $this->mutaterFactory->factory($token);
                $mutation->addMutation($mutater->mutate($mutation, $index));
            }
        }


        return $mutation;
    }

}
