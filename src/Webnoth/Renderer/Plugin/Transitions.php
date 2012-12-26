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
     * filters for tile naming
     * @var array
     */
    protected $tileImageFilters = null;

    /**
     * Initialize the renderer with the available terrains.
     * 
     * @param TerrainTypes $terrainTypes
     */
    public function __construct(TerrainTypes $terrainTypes)
    {
        $this->terrainTypes = $terrainTypes;
        $this->transitions  = include APPLICATION_PATH . '/config/transitions.php';
        $this->tileImageFilters  = include APPLICATION_PATH . '/config/tileimagefilters.php';
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
            $terrain         = $currentTerrain;
            $merged          = true;
            $checkAgainst    = $settings;
            
            if (is_array($settings)) {
                $checkAgainst     = $settings[0];
                $pretendedTerrain = $settings[1];
                $merged           = $settings[2];
                if ($pretendedTerrain != false) {
                    $terrain = $pretendedTerrain;
                }
            }
            
            $directions  = $this->getDifferencesFilteredBy($checkAgainst, $diffsByDirection);
            if ($merged) {
                $mergedTrans = $this->getMergedTransitionsFor($directions);
            } else {
                $mergedTrans = $this->getSeparateTransitionsFor($directions);
            }
            $transitions = $this->filterTransitions($mergedTrans, $currentBaseTerrain);
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
            $checkAgainst = $this->transitions[$baseTerrain];
        } else {
            $checkAgainst = array();
        }
        
        //alway check against void
        $checkAgainst[] = array('Xv', false, false);
        
        return $checkAgainst;
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
     * @return array
     */
    protected function getSeparateTransitionsFor(array $terrainDirections)
    {
        $transitions = array();
        $image       = null;
        foreach ($this->rotation as $direction) {
            $terrain = $terrainDirections[$direction];
            if ($terrain == null) {
                continue;
            }
            
            //no need to request every time, all terrains the same
            if ($image === null) {
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
     * @param array $directions
     * @return array
     */
    protected function getMergedTransitionsFor(array $directions)
    {
        $transitions = array();
        $tmp         = array();
        $image       = null;
        foreach ($this->rotation as $direction) {
            $terrain = $directions[$direction];
            if ($terrain != null) {
                //no need to request every time, all terrains the same
                if ($image === null) {
                    $dirTerrainType = $this->getBaseTerrainIfNotHidden($terrain);
                    $image = $dirTerrainType->getSymbolImage();
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
    
    /**
     * replaces image url where the symbol image is not usable
     * 
     * @param array $transitions
     * @param string $currentTerrain
     * 
     * @return array
     */
    protected function filterTransitions(array $transitions, $currentTerrain)
    {
        if (empty($transitions)) {
            return $transitions;
        }
        
        $filters = isset($this->tileImageFilters[$currentTerrain]) ? $this->tileImageFilters[$currentTerrain] : array();
        $filters['void/void-editor'] = 'void/void';
        foreach ($transitions as $key => $transition) {
            $transitions[$key] = str_replace(array_keys($filters), $filters, $transition);
        }
        
        return $transitions;
    }
}