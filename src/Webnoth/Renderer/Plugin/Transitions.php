<?php

namespace Webnoth\Renderer\Plugin;

use \Webnoth\WML\Collection\TerrainTypes;

/**
 * A renderer plugin which which provides fluents transition between the tiles
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class Transitions extends Base implements \Webnoth\Renderer\Plugin
{
    const MAX_MERGED_TRANSITION = 3;
    
    /**
     * the terrain types
     * @var \Webnoth\WML\Collection\TerrainTypes
     */
    protected $terrainTypes = null;

    /**
     * clockwise rotation for traversing surrounding tiles
     * @var array
     */
    protected $rotation = array('n', 'ne', 'se', 's', 'sw', 'nw');
    
    /**
     * terrain transition rules
     * @var array
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
        $surrounding        = $this->map->getSurroundingTerrains($column, $row);
        $currentTerrain     = $this->map->getTerrainAt($column, $row);
        $currentBaseTerrain = $this->terrainTypes->getBaseTerrain($currentTerrain, true);
        $diffsByDirection   = $this->getDifferencesByDirection($surrounding, $currentTerrain);
        
        //iterate the different terrain types, creates transitions per terrain
        $terrainsToCheck = $this->getTerrainsToCheck($currentBaseTerrain);
        foreach ($terrainsToCheck as $settings) {
            $merged       = true;
            $checkAgainst = $settings;
            $imageBase    = null;
            
            if (is_array($settings)) {
                $checkAgainst = $settings[0];
                $imageBase    = ($settings[1] !== null) ? $settings[1] : null;
                $merged       = isset($settings[2]) ? $settings[2] : true;
            }
            
            $directions  = $this->getDifferencesFilteredBy($checkAgainst, $diffsByDirection);
            if ($merged) {
                $transitions = $this->getMergedTransitionsFor($directions, $imageBase);
            } else {
                $transitions = $this->getSeparateTransitionsFor($directions, $imageBase);
            }
            $tileStack = array_merge($tileStack, $transitions);
        }
    }

    /**
     * provides the transitions to check against for a specific terrain type
     * 
     * @param string $baseTerrain
     * @return array(string|array)
     */
    protected function getTerrainsToCheck($baseTerrain)
    {
        if (isset($this->transitions[$baseTerrain])) {
            return $this->transitions[$baseTerrain];
        } 
        
        return array();
    }
    
    /**
     * Returns an array of terrain by direction 
     * where the base terrain differs from the current base terrain
     * 
     * @param array  $surrounding
     * @param string $currentTerrain
     * @return array (direction => terrain|false)
     */
    protected function getDifferencesByDirection(array $surrounding, $currentTerrain)
    {
        $transitionsByDirection = array();
        $currentBase            = $this->terrainTypes->getBaseTerrain($currentTerrain, true);
        foreach ($this->rotation as $direction) {
            $directionBase = $this->terrainTypes->getBaseTerrain($surrounding[$direction], true);
            $transitionsByDirection[$direction] = ($directionBase != $currentBase) ? $surrounding[$direction] : null;
        }
        
        return $transitionsByDirection;
    }
    
    /**
     * Returns an array of directions where transitions for the checked terrain
     * type occur.
     * 
     * @param string $checkedTerrain
     * @param array  $diffByDirection
     * @return array(direction)
     */
    protected function getDifferencesFilteredBy($checkedTerrain, array $diffByDirection)
    {
        $checkedBase = $this->terrainTypes->getBaseTerrain($checkedTerrain, true);
        foreach ($diffByDirection as $key => $terrain) {
            if ($terrain == null) {
                continue;
            }
            $dirBase = $this->terrainTypes->getBaseTerrain($terrain, true);
            if ($checkedBase != $dirBase) {
                $diffByDirection[$key] = null;
            }
        };
        
        return $diffByDirection;
    }
    
    /**
     * Returns an array of transitions images for each direction.
     * 
     * @param array $terrainDirections
     * @param string $image
     * @return array
     */
    protected function getSeparateTransitionsFor(array $terrainDirections, $image = null)
    {
        $transitions = array();
        foreach ($this->rotation as $direction) {
            $terrain = $terrainDirections[$direction];
            if ($terrain == null) {
                continue;
            }
            
            //no need to request every time, all terrains the same
            if ($image == null) {
                $dirTerrainType = $this->getBaseTerrainIfNotHidden($terrain);
                $image = $dirTerrainType->getSymbolImage();
            }

            $transitions[] = $image . '-' . $direction;
        }
        
        return $transitions;
    }
    
    /**
     * The merged transitions consist of clusters, i.e. each cluster is made
     * of continuous transitions without breaks
     * 
     * @param array  $directions
     * @param string $image
     * @return array
     */
    protected function getMergedTransitionsFor(array $directions, $image = null)
    {
        $transitions = array();
        $tmp         = array();
        foreach ($this->rotation as $direction) {
            $terrain = $directions[$direction];
            if ($terrain != null) {
                //no need to request every time, all terrains the same
                if ($image == null) {
                    $dirTerrainType = $this->getBaseTerrainIfNotHidden($terrain);
                    $image = $dirTerrainType->getSymbolImage();
                }
                
                if (count($tmp) == self::MAX_MERGED_TRANSITION) {
                    $transitions[] = $image . '-' . implode('-', $tmp);
                    $tmp = array();
                }
                
                $tmp[] = $direction;
            } else {
                if (!empty($tmp)) {
                    $transitions[] = $image . '-' . implode('-', $tmp);
                }
                $tmp = array();
            }
        }
        
        if (!empty($tmp)) {
            $transitions[] = $image . '-' . implode('-', $tmp);
        }
        
        return $transitions;
    }
    
    /**
     * Returns the base TerrainType for a terrain string
     * 
     * @param type $terrain
     * @return \Webnoth\WML\Element\TerrainType
     * @throws \InvalidArgumentException
     */
    protected function getBaseTerrainIfNotHidden($terrain)
    {
        $base        = $this->terrainTypes->getBaseTerrain($terrain, true);
        $baseTerrain = $this->terrainTypes->get($base);
        
        if ($baseTerrain == null) {
            throw new \InvalidArgumentException('The terrain type ' . $terrain . ' could not be found.');
        }
        
        if ($baseTerrain->isHidden()) {
            $base        = $this->terrainTypes->getBaseTerrain($terrain);
            $baseTerrain = $this->terrainTypes->get($base);
        } 
        
        return $baseTerrain;
    }
}