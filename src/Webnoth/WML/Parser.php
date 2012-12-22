<?php

namespace Webnoth\WML;

/**
 * WML Parser
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class Parser
{

    /**
     * lexer used to find the wml tokens
     * @var Lexer 
     */
    protected $lexer;

    /**
     * Pass a WML lexer instance to the constructor
     * 
     * @param \Webnoth\WML\Lexer $lexer
     */
    public function __construct(Lexer $lexer)
    {
        $this->lexer = $lexer;
    }

    /**
     * Parse the given input into a collection of WML elements
     * 
     * @param string $input
     * @return \Doctrine\Common\Collections\Collection
     */
    public function parse($input)
    {
        $this->lexer->setInput($input);

        $currentElement = null;
        $attrKey = null;
        $attrVal = null;
        $collection = new \Doctrine\Common\Collections\ArrayCollection();

        $this->lexer->moveNext();
        while (null !== $this->lexer->lookahead) {
            if (Lexer::T_OPEN_ELEMENT == $this->lexer->lookahead['type']) {
                $currentElement = new Element();
            }

            if (Lexer::T_VALUE == $this->lexer->lookahead['type']) {
                $next = $this->lexer->glimpse();
                if (Lexer::T_ASSIGN == $next['type']) {
                    $attrKey = $this->lexer->lookahead['value'];
                } else {
                    $attrVal = $this->lexer->lookahead['value'];
                    $currentElement->offsetSet($attrKey, $attrVal);
                }
            }

            if (Lexer::T_CLOSE_ELEMENT == $this->lexer->lookahead['type']) {
                $collection->add($currentElement);
                $currentElement = null;
            }

            $this->lexer->moveNext();
            continue;
        }

        return $collection;
    }

}
