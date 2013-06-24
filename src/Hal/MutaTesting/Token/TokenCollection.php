<?php

namespace Hal\MutaTesting\Token;

class TokenCollection implements TokenCollectionInterface
{

    private $tokens;

    public function __construct($tokens)
    {
        $this->tokens = $tokens;
    }

    public function asPhp($tag = false)
    {
        $ret = "";
        foreach ($this->tokens as $token) {
            if (is_string($token)) {
                $ret.= $token;
            } else {
                list($id, $text) = $token;

                $ret.= $text;
            }
        }
        return ($tag ? '<?php ' : '' )
                . trim(str_replace(array('<?php', '?>'), array('', ''), $ret));
    }

    public function all()
    {
        $return = array();
        $len = $this->count();
        for($i = 0; $i < $len ; $i++) {
            $return[] = $this->get($i);
        }
        return $return;
    }

    public function replace($index, $token)
    {
        $tokens = $this->all();
        $tokens[$index] = $token;
        return new TokenCollection($tokens);
    }

    public function remove($index, $end = null)
    {
        $tokens = $this->all();
        if (null === $end) {
            $end = $index;
        }
        for ($i = $index; $i <= $end; $i++) {
            unset($tokens[$i]);
        }
        return new TokenCollection(array_values($tokens));
    }

    public function get($index)
    {

        if(!isset($this->tokens[$index])) {
            return null;
        }
        
        $token = $this->tokens[$index];
        if (!isset($token[1])) {
            $token = array(T_STRING, $token[0]);
        }
        return $token;
    }

    public function count()
    {
        return sizeof($this->tokens);
    }

}

