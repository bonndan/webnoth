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
     * clockwise rotation for traversing surrounding tiles
     * @var array
     */
    protected $rotation = array('n', 'ne', 'se', 's', 'sw', 'nw');
    
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
    
    /**
     * Pass an array of surrounding terrain differences
     * 
     * @param \Webnoth\WML\Collection\TerrainTypes $surrounding, unfiltered
     * @return array
     */
    public function getTransitionImages(\Webnoth\WML\Collection\TerrainTypes $surrounding)
    {
        $checkedBase = $this->terrainType->getBaseTerrain();
        
        /*
         * filter, keep only relevant entries
         */
        foreach ($surrounding as $direction => $terrain) {
            if ($terrain === null) {
                continue;
            }
            /* @var $terrain \Webnoth\WML\Element\TerrainType */
            $dirBase = $terrain->getBaseTerrain($terrain);
            
            if ($checkedBase != $dirBase) {
                $surrounding[$direction] = null;
            }
        };
        
        $transitions = array();
        foreach ($this->imageBases as $image => $maxAdjacent) {
            $transitions = array_merge($transitions, $this->getTransitionsForImage($surrounding, $image, $maxAdjacent));
        }
        
        return $transitions;
    }
    
    /**
     * 
     * @param \Webnoth\WML\Collection\TerrainTypes $surrounding
     * @param type $image
     * @param type $maxAdjacent
     * @return array
     */
    protected function getTransitionsForImage(\Webnoth\WML\Collection\TerrainTypes $surrounding, $image, $maxAdjacent) 
    {
        if ($maxAdjacent == 1) {
            return $this->getSeparateTransitionsFor($surrounding->toArray(), $image);
        } else {
            return $this->getMergedTransitionsFor($surrounding->toArray(), $image, $maxAdjacent);
        }
    }
    
    /**
     * Returns an array of transitions images for each direction.
     * 
     * @param array $terrainDirections
     * @param string $image
     * @return array
     */
    protected function getSeparateTransitionsFor(array $terrainDirections, $image)
    {
        $transitions = array();
        foreach ($this->rotation as $direction) {
            $terrain = $terrainDirections[$direction];
            if ($terrain == null) {
                continue;
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
    protected function getMergedTransitionsFor(array $directions, $image, $maxAdjacent)
    {
        $transitions = array();
        $tmp         = array();
        foreach ($this->rotation as $direction) {
            $terrain = $directions[$direction];
            if ($terrain != null) {
                if (count($tmp) == $maxAdjacent) {
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
}