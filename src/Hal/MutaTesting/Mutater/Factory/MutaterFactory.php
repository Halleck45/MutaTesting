<?php

namespace Hal\MutaTesting\Mutater\Factory;

class MutaterFactory implements MutaterFactoryInterface
{

    private static $OPERATOR_MAP = array(
        '/' => 'T_MATH_DIV'
        , '*' => 'T_MATH_MUL'
        , '+' => 'T_MATH_ADD'
        , '-' => 'T_MATH_SUB'
    );

    public function isMutable($token)
    {
        $name = $this->getClassnameForToken($token);
        return null !== $name && class_exists($name);
    }

    public function factory($token)
    {
        if (!$this->isMutable($token)) {
            return null;
        }

        $classname = $this->getClassnameForToken($token);
        return new $classname;
    }

    public function getClassnameForToken($token)
    {
        if (is_array($token)) {
            $type = $token[0];
            $value = $token[1];
        } else {
            $type = T_STRING;
            $value = $token;
        }


        $classname = null;
        switch ($type) {
            case T_STRING:
                // case of operators
                if (isset(self::$OPERATOR_MAP[$value])) {
                    $classname = self::$OPERATOR_MAP[$value];
                }
                break;
            default:
                $classname = token_name($type);
                break;
        }
        $classname = str_replace('_', '', $classname);
        if (null !== $classname) {
            $classname = '\Hal\MutaTesting\Mutater\Mutater' . $classname;
        }
        return $classname;
    }

}
