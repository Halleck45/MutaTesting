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
        return $this->tokens;
    }

    public function replace($index, $token)
    {
        $tokens = $this->tokens;
        $tokens[$index] = $token;
        return new TokenCollection($tokens);
    }

    public function remove($index, $end = null)
    {
        $tokens = $this->tokens;
        if (null === $end) {
            $end = $index;
        }
        for ($i = $index; $i <= $end; $i++) {
            unset($tokens[$i]);
        }
        return new TokenCollection($tokens);
    }

    public function get($index)
    {
        return isset($this->tokens[$index]) ? $this->tokens[$index] : null;
    }

}

