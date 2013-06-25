<?php

namespace Hal\MutaTesting\Token\Filter;

class ChainFilter implements FilterInterface
{

    private $filters;

    public function __construct(array $filters = array())
    {
        $this->filters = new \SplObjectStorage();
        foreach ($filters as $filter) {
            $this->attach($filter);
        }
    }

    public function filter(\Hal\MutaTesting\Token\TokenCollectionInterface $tokens)
    {
        foreach ($this->filters as $filter) {
            $tokens = $filter->filter($tokens);
        }
        return $tokens;
    }

    public function attach(FilterInterface $filter)
    {
        $this->filters->attach($filter);
        return $this;
    }

    public function detach(FilterInterface $filter)
    {
        $this->filters->detach($filter);
        return $this;
    }

}

