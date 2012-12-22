<?php

namespace Webnoth\WML\Element;

use Webnoth\WML\Element;

/**
 * TerrainType
 */
class TerrainType extends Element
{
    const VOID = 'Xv';
    
    /**
     * Returns the string attribute (used in map files)
     * 
     * @return string
     */
    public function getString()
    {
        return $this->offsetGet('string');
    }
    
    /**
     * Returns the symbol_image attribute
     * 
     * @return string
     */
    public function getSymbolImage()
    {
        return $this->offsetGet('symbol_image');
    }
}