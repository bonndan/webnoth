<?php

namespace Webnoth\WML;

/**
 * Lexer for WML
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class Lexer extends \Doctrine\Common\Lexer
{
    const T_OPEN_ELEMENT = 1;
    const T_CLOSE_ELEMENT = 2;
    const T_ASSIGN = 3;
    const T_VARIABLE = 10;
    const T_VALUE = 20;
    
    const T_UNDERSCORE = 300;
    
    /**
     * Lexical catchable patterns.
     *
     * @return array
     */
    protected function getCatchablePatterns()
    {
        return array(
            '\[\/?[A-Za-z0-9_]*\]', //opening and closing of elements
            '"[A-Za-z0-9_\/\s]*"', //anything in parentheses
            '[A-Za-z0-9_"\/]*(?=\=)',
            '[A-Za-z0-9_"\/]*(?<=\=)',
            '=',
        );
    }

    /**
     * Lexical non-catchable patterns.
     *
     * @return array
     */
    protected function getNonCatchablePatterns()
    {
        return array(
            '\s+',
            '\_ ',
            '#.*[\r\n|\n]', //ignore comments
        );
    }

    /**
     * Retrieve token type. Also processes the token value if necessary.
     *
     * @param string $value
     * @return integer
     */
    protected function getType(&$value)
    {
        if ($value[0] == '[') {
            if ($value[1] != '/') {
                return self::T_OPEN_ELEMENT;
            } else {
                return self::T_CLOSE_ELEMENT;
            }
        }
        
        if ($value[0] == '=') {
            return self::T_ASSIGN;
        }
        
        return self::T_VALUE;
    }
}
