<?php

namespace Webnoth\Renderer;

use \Webnoth\WML\Element\Layer;

/**
 * Renderer Plugin
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
interface Plugin
{
    /**
     * Set the layer which is used by the plugin
     * 
     * @param Layer $map
     */
    public function setLayer(Layer $layer);
    
    /**
     * Modifies the stack of terrains for one tile (if needed)
     * 
     * @param array $tileStack
     * @param int   $column
     * @param int   $row
     */
    public function getTileTerrains(array &$tileStack, $column, $row);
}