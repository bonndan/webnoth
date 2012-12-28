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
     * Initialize the renderer with the available terrains.
     * 
     * @param TerrainTypes $terrainTypes
     */
    public function __construct(TerrainTypes $terrainTypes)
    {
        $this->setTerrainTypes($terrainTypes);
        $this->imagePath    = APPLICATION_PATH . '/data/terrain/';
    }
    
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