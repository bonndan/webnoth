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
}