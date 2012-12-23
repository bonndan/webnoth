<?php

namespace Webnoth;

use Webnoth\WML\Collection\TerrainTypes;
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
     * render plugins
     * @var Plugin[]
     */
    protected $plugins = array();
    
    /**
     * Initialize the renderer with the available terrains.
     * 
     * @param TerrainTypes $terrainTypes
     */
    public function __construct(TerrainTypes $terrainTypes)
    {
        $this->terrainTypes = $terrainTypes;
        $this->imagePath    = APPLICATION_PATH . '/data/terrain/';
    }
    
    /**
     * Renders the map
     * 
     * @param \Webnoth\WML\Element\Map $map
     * @return resource
     */
    public function render(Map $map)
    {
        //initialize the plugins with the map
        foreach ($this->plugins as $plugin) {
            $plugin->setMap($map);
        }
        
        //create the output image
        $image = $this->getImageResource($map);
        
        $col = 1;
        $row = 1;
        foreach ($map->getTiles() as $tile) {
            
            //offsets
            $yOffset = ($col%2) ? self::TILE_HEIGHT/2 : 0;
            
            $terrainImages = $this->getTerrainsForTile($tile, $col, $row);
            foreach ($terrainImages as $terrainImage) {
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
            }
            $col++;
            if ($col == $map->getWidth() +1) {
                $col = 1;
                $row++;
            }
        }
        
        return $image;
    }
    
    /**
     * Returns array of gd image resource for the tile
     * 
     * @param string $tile
     * @param int    $column
     * @param int    $row
     * @return array(resource)
     */
    protected function getTerrainsForTile($tile, $column, $row)
    {
        $stack = array($tile);
        foreach ($this->plugins as $plugin) {
            $plugin->getTileTerrains($stack, $column, $row);
        }
        
        $terrains = array();
        foreach ($stack as $terrainType) {
            $terrains[] = $this->getTerrainResource($terrainType);
        }
        return $terrains;
    }
    
    /**
     * Returns a gd image resource for a specific terrain type
     * 
     * @param string $terrain
     * @return resource
     */
    protected function getTerrainResource($terrain)
    {
        if (!isset($this->terrainResources[$terrain])) {
            $file = $this->terrainTypes->get($terrain)->getSymbolImage();
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
        $width = $map->getWidth() * self::TILE_WIDTH * 0.75 + self::TILE_WIDTH * 0.25;
        $height = count($map->getTiles()) / $map->getWidth() * self::TILE_HEIGHT + self::TILE_HEIGHT/2;
        return imagecreatetruecolor($width, $height);
    }
    
    /**
     * Add a plugin that can modify the terrain stack for a tile
     * 
     * @param \Webnoth\Renderer\Plugin $plugin
     */
    public function addPlugin(\Webnoth\Renderer\Plugin $plugin)
    {
        $this->plugins[] = $plugin;
    }
}