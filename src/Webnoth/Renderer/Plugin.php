<?php

namespace Webnoth\Renderer;

/**
 * Renderer Plugin
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
interface Plugin
{
    /**
     * Pass the map to plugin.
     * 
     * @param \Webnoth\WML\Element\Map $map
     */
    public function setMap(\Webnoth\WML\Element\Map $map);
    
    /**
     * Modifies the stack of terrains for one tile (if needed)
     * 
     * @param array $tileStack
     */
    public function getTileTerrains(array &$tileStack);
}