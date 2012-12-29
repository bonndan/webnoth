<?php

namespace Webnoth\Renderer;

use Webnoth\WML\Collection\TerrainTypes;
use Webnoth\WML\Element\Map;

/**
 * Renderer for the terrain.
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class Terrain extends Base
{
    /**
     * Returns the terrain tiles.
     * 
     * @param \Webnoth\WML\Element\Map $map
     * @return array
     */
    public function getTilesToRender(Map $map)
    {
        return $map->getTiles();
    }
}