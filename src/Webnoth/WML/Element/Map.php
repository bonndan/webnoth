<?php

namespace Webnoth\WML\Element;
use \Webnoth\WML\Element;
use \Webnoth\WML\Collection\TerrainTypes;

/**
 * Element containing map data
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class Map extends Element
{
    /**
     * width in tiles
     * @var int
     */
    protected $width = null;
    
    /**
     * array of arrays referring terrain types
     * @var array y => x
     */
    protected $terrains = array();
    
    /**
     * array of arrays referring terrain heights
     * @var array
     */
    protected $heights = array();
    
    /**
     * array of arrays referring terrain overlays / obstacles
     * @var array
     */
    protected $overlays = array();
    
    /**
     * all starting positions of the sides
     * @var array (tilenumber => position)
     */
    protected $startingPositions = array();
        
    /**
     * adds a row of tiles (terrain type strings)
     * 
     * @param array $tiles
     */
    public function addRawTileRow(array $tiles)
    {
        $this->setWidth(count($tiles));
        foreach ($tiles as $key => $rawTile) {
            
            /*
             * starting positions are noted like "... GG, 3 Ke, Gg, ..."
             */
            if (strpos($rawTile, ' ') !== false) {
                $parts = explode(' ', $rawTile);
                $this->startingPositions[$parts[0]] = array(count($this->terrains), $key);
                $rawTile = $parts[1];
            }
            $tiles[$key] = $rawTile;
        }
        
        $this->terrains[] = $tiles;
    }
    
    /**
     * Set the width of the map. If the value differs from the current, an exception
     * is thrown.
     * 
     * @param int $width
     * @throws RuntimeException
     */
    protected function setWidth($width)
    {
        if ($this->width != $width && $this->width !== null) {
            throw new \RuntimeException('Width mismatch: ' . $width . ' differs from current '. $this->width);
        }
        
        $this->width = (int)$width;
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
     * Returns the width (in tiles) of the map.
     * 
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }
    
    /**
     * Returns the height as number of rows
     * 
     * @return int
     */
    public function getHeight()
    {
        return count($this->terrains);
    }
    
    /**
     * Returns the starting positions per side
     * @return array
     */
    public function getStartingPositions()
    {
        return $this->startingPositions;
    }
    
    /**
	 * return surrounding tiles by direction
	 * 
     * @param int $column
     * @param int $row
     * @param \Webnoth\WML\Collection\TerrainTypes $terrains
	 * @return \Webnoth\WML\Collection\TerrainTypes
     * @link http://wiki.wesnoth.org/TerrainGraphicsTutorial#The_hex_coordinate_system
	 */
    public function getSurroundingTerrains($column, $row, TerrainTypes $terrains)
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
        
        return $this->toTerrainTypeCollection($surrounding, $terrains);
    }
    
    /**
     * Converts an terrain string array into a terraintype collection.
     * 
     * @param array $collection
     * @param \Webnoth\WML\Collection\TerrainTypes $terrains
     * @return \Webnoth\WML\Collection\TerrainTypes
     */
    protected function toTerrainTypeCollection(array $collection, TerrainTypes $terrains)
    {
        foreach ($collection as $key => $terrain) {
            $collection[$key] = $terrains->get($terrain);
        }
            
        return new \Webnoth\WML\Collection\TerrainTypes($collection);
    }
    
    /**
     * Returns the terrain type (raw) at a given offset
     * 
     * @param int $column
     * @param int $row
     * @return string
     */
    public function getTerrainAt($column, $row)
    {
        if (!isset($this->terrains[$row][$column])) {
            return TerrainType::VOID;
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
     * Set a value to the height map.
     * 
     * @param int   $column
     * @param int   $row
     * @param float $height
     */
    public function setHeightAt($column, $row, $height)
    {
        $this->heights[$row][$column] = $height;
    }
    
    /**
     * Set a value to the overlay map.
     * 
     * @param int   $column
     * @param int   $row
     * @param float $terrain
     */
    public function setOverlayAt($column, $row, $terrain)
    {
        $this->overlays[$row][$column] = $terrain;
    }
}