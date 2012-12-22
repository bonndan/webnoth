<?php

namespace Webnoth\WML\Element;

use Webnoth\WML\Element;

/**
 * TerrainType
 */
class TerrainType extends Element
{
    /**
     * Returns the string attribute (used in map files)
     * 
     * @return string
     */
    public function getString()
    {
        return $this->offsetGet('string');
    }
}