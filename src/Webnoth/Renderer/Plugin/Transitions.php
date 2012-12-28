<?php

namespace Webnoth\Renderer\Plugin;

use \Webnoth\WML\Collection\TerrainTypes;
use Webnoth\Renderer\Transition;

/**
 * A renderer plugin which which provides fluents transition between the tiles
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class Transitions extends Base implements \Webnoth\Renderer\Plugin
{
    /**
     * the terrain types
     * @var \Webnoth\WML\Collection\TerrainTypes
     */
    protected $terrainTypes = null;

    /**
     * terrain transition rules
     * @var array terrain => Transition[]
     */
    protected $transitions = null;
    
    /**
     * Initialize the renderer with the available terrains.
     * 
     * @param TerrainTypes $terrainTypes
     */
    public function __construct(TerrainTypes $terrainTypes)
    {
        $this->terrainTypes = $terrainTypes;
        $this->transitions  = include APPLICATION_PATH . '/config/transitions.php';
    }

    /**
     * Adds tiles which provides a fluent transition between the tiles
     * 
     * @param array $tileStack
     * @param int   $column
     * @param int   $row
     * 
     * @return array
     */
    public function getTileTerrains(array &$tileStack, $column, $row)
    {
        $surrounding    = $this->map->getSurroundingTerrains($column, $row, $this->terrainTypes);
        $currentTerrain = $this->map->getTerrainAt($column, $row);
        $transitions    = $this->getTransitionsToCheck($currentTerrain);
        
        //iterate the different terrain types, creates transitions per terrain
        foreach ($transitions as $transition) {
            /* @var $transition \Webnoth\Renderer\Transition */
            $images = $transition->getTransitionImages(clone $surrounding);
            $tileStack = array_merge($tileStack, $images);
        }
    }

    /**
     * provides the transitions to check against for a specific terrain type
     * 
     * @param string $terrain
     * @return \Webnoth\Renderer\Transition[]
     */
    protected function getTransitionsToCheck($terrain)
    {
        if (isset($this->transitions[$terrain])) {
            return $this->transitions[$terrain];
        } 
        
        return array();
    }
}