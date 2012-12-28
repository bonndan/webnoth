<?php

namespace Webnoth\Renderer;

/**
 * Renderer for the terrain overlays.
 * 
 * @author  Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class Overlay extends Base
{
    /**
     * Set the path to the terrain images on initialisation.
     * 
     * @param string $imagePath
     */
    public function __construct($imagePath)
    {
        $this->imagePath = $imagePath;
    }
    
    /**
     * Returns the overlay tiles.
     * 
     * @param \Webnoth\WML\Element\Map $map
     * @return array
     */
    protected function getTilesToRender(\Webnoth\WML\Element\Map $map)
    {
        return $map->getOverlayTiles();
    }
}