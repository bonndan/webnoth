<?php

namespace Webnoth\WML\Element;

use \Webnoth\WML\Collection\TerrainTypes;

/**
 * A map layer with its own terrain types.
 * 
 * @author  Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class Layer
{
    /**
     * terrain types for this layer
     * @var TerrainTypes
     */
    protected $terrainTypes = null;
    
    /**
     * array of arrays referring terrain types
     * @var array y => x
     */
    protected $terrains = array();

    /**
     * Optionally pass the terrain types to use to the constructor.
     * 
     * @param \Webnoth\WML\Collection\TerrainTypes $terrainTypes
     */
    public function __construct(TerrainTypes $terrainTypes = null)
    {
        if ($terrainTypes !== null) {
            $this->setTerrainTypes($terrainTypes);
        }
    }
    
    /**
     * Set the terrain types to use for this layer.
     * 
     * @param \Webnoth\WML\Collection\TerrainTypes $terrainTypes
     */
    public function setTerrainTypes(TerrainTypes $terrainTypes)
    {
        $this->terrainTypes = $terrainTypes;
    }
    
    /**
     * Returns all the tiles as a stream.
     * 
     * @return array
     */
    public function getTiles()
    {
        $tiles = array();
        foreach ($this->terrains as $row) {
            $tiles = array_merge($tiles, $row);
        }
        
        return $tiles;
    }
    
    /**
	 * return surrounding tiles by direction
	 * 
     * @param int $column
     * @param int $row
	 * @return \Webnoth\WML\Collection\TerrainTypes
     * @link http://wiki.wesnoth.org/TerrainGraphicsTutorial#The_hex_coordinate_system
	 */
    public function getSurroundingTerrains($column, $row)
    {
        if ($column%2) {//odd column
			$surrounding = array(
				'ne' => $this->getTerrainAt($column+1, $row),
				'se' => $this->getTerrainAt($column+1, $row+1),
				's'  => $this->getTerrainAt($column,   $row+1),
				'sw' => $this->getTerrainAt($column-1, $row+1),
				'nw' => $this->getTerrainAt($column-1, $row),
				'n'  => $this->getTerrainAt($column,   $row-1)
			);
        } else {
			$surrounding = array(
				'ne' => $this->getTerrainAt($column+1, $row-1),
				'se' => $this->getTerrainAt($column+1, $row),
				's'  => $this->getTerrainAt($column,   $row+1),
				'sw' => $this->getTerrainAt($column-1, $row),
				'nw' => $this->getTerrainAt($column-1, $row-1),
				'n'  => $this->getTerrainAt($column,   $row-1)
			);
        }
        
        return $this->toTerrainTypeCollection($surrounding);
    }
    
    /**
     * Converts an terrain string array into a terraintype collection.
     * 
     * @param array $collection
     * @return \Webnoth\WML\Collection\TerrainTypes
     */
    protected function toTerrainTypeCollection(array $collection)
    {
        foreach ($collection as $key => $terrain) {
            $collection[$key] = $this->terrainTypes->get($terrain);
        }
            
        return new \Webnoth\WML\Collection\TerrainTypes($collection);
    }
    
    /**
     * Returns the terrain at a given offset
     * 
     * @param int    $column
     * @param int    $row
     * @return string|null
     */
    public function getTerrainAt($column, $row)
    {
        if (!isset($this->terrains[$row][$column])) {
            return null;
        }
        return $this->terrains[$row][$column];
    }
    
    /**
     * Set a specific terrain at a coordinate.
     * 
     * @param int    $column
     * @param int    $row
     * @param string $terrain
     */
    public function setTerrainAt($column, $row, $terrain)
    {
        $this->terrains[$row][$column] = $terrain;
    }
    
    /**
     * Returns the number of rows.
     * 
     * @return int
     */
    public function getRowCount()
    {
        return count($this->terrains);
    }
    
    /**
     * Returns the number columns (of the first row)
     * 
     * @return int
     */
    public function getWidth()
    {
        reset($this->terrains);
        return count(current($this->terrains));
    }
}