<?php

namespace Hal\MutaTesting\Mutater\Factory;

class MutaterFactory implements MutaterFactoryInterface
{

    private static $OPERATOR_MAP = array(
        '/' => 'T_MATH_DIV'
        , '*' => 'T_MATH_MUL'
        , '+' => 'T_MATH_ADD'
        , '-' => 'T_MATH_SUB'
        , '>' => 'T_MATH_GREATER'
        , '>=' => 'T_MATH_GREATEROREQUAL'
        , '<' => 'T_MATH_LESS'
        , '<=' => 'T_MATH_LESSOREQUAL'
        , 'true' => 'T_BOOLEAN_TRUE'
        , 'false' => 'T_BOOLEAN_FALSE'
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

        // camelcase
        $classname = strtolower($classname);
        $classname = preg_replace_callback('/_(.?)/', function ($matches) {
            return strtoupper($matches[1]);
        }, $classname);
        $classname = preg_replace('!(^t)!', '', $classname);
        if (null !== $classname) {
            $classname = '\Hal\MutaTesting\Mutater\Mutater' . $classname;
        }
        return $classname;
    }

}
