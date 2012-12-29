<?php

namespace Webnoth\Renderer;

/**
 * Renderer for the height map.
 * 
 * Be sure to add the height provider plugin, then the transitions plugin.
 * 
 * @author  Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class Heightmap extends Base
{
    /**
     * Returns the regular tiles.
     * 
     * @param \Webnoth\WML\Element\Map $map
     * @return array
     */
    protected function getTilesToRender(\Webnoth\WML\Element\Map $map)
    {
        return $map->getTiles();
    }
}