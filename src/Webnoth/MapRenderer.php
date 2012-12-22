<?php

namespace Webnoth;

use Doctrine\Common\Collections\Collection;
use Webnoth\WML\Element\Map;
use Webnoth\WML\Element\TerrainType;

/**
 * MapRenderer
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class MapRenderer
{
    /**
     * width of a png tile
     * @var int
     */
    const TILE_WIDTH = 72;
    
    /**
     * height of a png tile
     * @var int
     */
    const TILE_HEIGHT = 72;
    
    /**
     * collection of all terrains
     * @var Collection 
     */
    protected $terrainTypes = null;
    
    /**
     * path to the png files
     * @var string
     */
    protected $imagePath = null;
    
    /**
     * gd resources for the different terrains
     * @var array
     */
    protected $terrainResources = array();
    
    /**
     * Initialize the renderer with the available terrains.
     * 
     * @param \Doctrine\Common\Collections\Collection $terrainTypes
     */
    public function __construct(Collection $terrainTypes)
    {
        foreach ($terrainTypes as $terrain) {
            /* @var $terrain TerrainType */
            $this->terrainTypes[$terrain->getString()] = $terrain;
        }
        
        $this->imagePath = APPLICATION_PATH . '/data/terrain/';
    }
    
    /**
     * Renders the map
     * 
     * @param \Webnoth\WML\Element\Map $map
     * @return resource
     */
    public function render(Map $map)
    {
        $image = $this->getImageResource($map);
        
        $col = 1;
        $row = 1;
        foreach ($map->getTiles() as $tile) {
            
            //offsets
            $yOffset = ($col%2) ? self::TILE_HEIGHT/2 : 0;
            
            $terrainImage = $this->getTerrainResource($tile);
            imagecopy(
                $image,
                $terrainImage, 
                (($col-1) * (0.75 * self::TILE_WIDTH)),
                ($row-1) * self::TILE_HEIGHT + $yOffset,
                0,
                0,
                self::TILE_WIDTH,
                self::TILE_HEIGHT
            );
            $col++;
            if ($col == $map->getWidth() +1) {
                $col = 1;
                $row++;
            }
        }
        
        return $image;
    }
    
    /**
     * Returns a gd image resource for a specific terrain type
     * 
     * @param string $terrain
     * @return resource
     */
    protected function getTerrainResource($terrain)
    {
        //fixme
        if (strpos($terrain, '^') !== false) {
            $terrains = explode('^', $terrain);
            $terrain = $terrains[0];
        }
        
        if (!isset($this->terrainResources[$terrain])) {
            $file = $this->terrainTypes[$terrain]->getSymbolImage();
            $path = $this->imagePath . $file . '.png';
            $this->terrainResources[$terrain] = imagecreatefrompng($path);
        }
        
        if ($this->terrainResources[$terrain] == false) {
            throw new \RuntimeException('Could not load the terrain ' . $terrain . ' from ' . $path);
        }
        
        return $this->terrainResources[$terrain];
    }
    
    /**
     * Creates a gd image resource based on the map
     * 
     * @param \Webnoth\WML\Element\Map $map
     * @return resource
     */
    protected function getImageResource(Map $map)
    {
        /*
         * width: tiles per row plus half tile extra for row x-offset
         */
        $width = $map->getWidth() * self::TILE_WIDTH * 0.75 + self::TILE_WIDTH * 0.25;
        /*
         * height: half of the rows plus half tile extra for row y-offset
         */
        $height = count($map->getTiles()) / $map->getWidth() * self::TILE_HEIGHT + self::TILE_HEIGHT/2;
        return imagecreatetruecolor($width, $height);
    }
}