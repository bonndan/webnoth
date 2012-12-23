<?php

namespace Webnoth\WML\Element;

use Webnoth\WML\Element;

/**
 * TerrainType
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class TerrainType extends Element
{
    /**
     * Void terrain type string
     * @var string
     */
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
     * Returns the editor_image attribute or symbol_image attribute as fallback
     * 
     * @return string
     */
    public function getSymbolImage()
    {
        if ($this->offsetExists('editor_image')) {
            return $this->offsetGet('editor_image');
        }
        return $this->offsetGet('symbol_image');
    }
    
    /**
     * Check if the terrain is hidden (virtual).
     * 
     * @return boolean
     */
    public function isHidden()
    {
        return $this->offsetExists('hidden') && $this->offsetGet('hidden') == 'yes';
    }
}