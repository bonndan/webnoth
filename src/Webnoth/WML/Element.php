<?php

namespace Webnoth\WML;

/**
 * A WML element, provides array access to the attributes
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class Element implements \ArrayAccess
{
    /**
     * key-value attributes
     * @var array
     */
    protected $attributes = array();
    
    /**
     * nested elements
     * @var Element[]
     */
    protected $elements = array();
    
    /**
     * check if an attribute has been set
     * 
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * get the value of an attribute
     * 
     * @param string $offset
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new \InvalidArgumentException('Unknown attribute ' . $offset);
        }
        
        return $this->attributes[$offset];
    }

    /**
     * set an attribute value
     * 
     * @param string $offset
     * @param mixed  $value
     */
    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    /**
     * unset a specific attribute
     * 
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }
}