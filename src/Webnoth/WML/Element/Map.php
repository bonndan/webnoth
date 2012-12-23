<?php

namespace Webnoth\WML\Element;
use Webnoth\WML\Element;

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
     * array of strings referring terrain types
     * @var array
     */
    protected $tiles = array();
    
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
        foreach ($tiles as $rawTile) {
            
            /*
             * starting positions are noted like "... GG, 3 Ke, Gg, ..."
             */
            if (strpos($rawTile, ' ') !== false) {
                $parts = explode(' ', $rawTile);
                $this->startingPositions[count($this->tiles)] = $parts[0];
                $rawTile = $parts[1];
            }
            $this->tiles[] = $rawTile;
        }
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
     * Returns all the tiles.
     * 
     * @return array
     */
    public function getTiles()
    {
        return $this->tiles;
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
	 * @return array
	 */
    public function getSurroundingTerrains($column, $row)
    {
		if($column%2) {//odd column
			return array(
				'ne' => $this->getTerrainAt($column+1, $row),
				'se' => $this->getTerrainAt($column+1, $row+1),
				's'  => $this->getTerrainAt($column, $row+1),
				'sw' => $this->getTerrainAt($column-1, $row+1),
				'nw' => $this->getTerrainAt($column-1, $row),
				'n'  => $this->getTerrainAt($column, $row-1)
			);
        } else {
			return array(
				'ne' => $this->getTerrainAt($column+1, $row-1),
				'se' => $this->getTerrainAt($column+1, $row),
				's'  => $this->getTerrainAt($column, $row+1),
				'sw' => $this->getTerrainAt($column-1, $row),
				'nw' => $this->getTerrainAt($column-1, $row-1),
				'n'  => $this->getTerrainAt($column, $row-1)
			);
        }
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
        $offset = ($row -1) * $this->getWidth() + ($column -1);
        if (!isset($this->tiles[$offset])) {
            return TerrainType::VOID;
        }
        return $this->tiles[$offset];
    }
}