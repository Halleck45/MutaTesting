<?php

namespace Hal\MutaTesting\Token;

class TokenInfo implements TokenInfoInterface
{

   private $dependencies = array();
   
   public function getDependencies()
   {
       return $this->dependencies;
   }

   public function setDependencies(array $dependencies)
   {
       $this->dependencies = $dependencies;
       return $this;
   }



}

