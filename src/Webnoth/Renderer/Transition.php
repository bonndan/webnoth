<?php

namespace Webnoth\Renderer;

use Webnoth\WML\Element\TerrainType;

/**
 * Class representing the transition between two different terrains.
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class Transition
{
    /**
     * the terrain type the transition is responsible for
     * @var \Webnoth\WML\Element\TerrainType
     */
    protected $terrainType = null;
    
    /**
     * image bases to use instead of the default ones
     * @var array
     */
    protected $imageBases = array();
    
    /**
     * Factory method, invoked with the image base configuration
     * 
     * @param array $imageBases array(image => maxAdjacentTiles)
     * @return Transition
     */
    public static function create(TerrainType $terrainType, array $imageBases)
    {
        $transition = new static($terrainType);
        foreach ($imageBases as $imageBase => $maxAdjacentTiles) {
            $transition->imageBases[$imageBase] = $maxAdjacentTiles;
        }
        
        return $transition;
    }
    
    /**
     * Pass the terrain type the transition is responsible for.
     * 
     * @param \Webnoth\WML\Element\TerrainType $terrainType
     */
    public function __construct(TerrainType $terrainType)
    {
        $this->terrainType = $terrainType;
    }
    
}