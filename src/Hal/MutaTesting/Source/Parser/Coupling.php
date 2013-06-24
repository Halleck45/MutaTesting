<?php

namespace Hal\MutaTesting\Source\Parser;

class Coupling
{

    private $files;
    private $usage = array();

    public function __construct(array $files)
    {
        $this->files = $files;
    }

    public function parse()
    {
        foreach ($this->files as $filename) {
            $content = file_get_contents($filename);
            $tokens = new \Hal\MutaTesting\Token\TokenCollection(token_get_all($content));
            
            
            $len = sizeof($tokens);
            
            
            
        }
    }

    public function getCodeUsage($filename)
    {
        
    }

}