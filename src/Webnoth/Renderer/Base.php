<?php

namespace Webnoth\Renderer;

use Webnoth\WML\Collection\TerrainTypes;
use Webnoth\WML\Element\Map;
use Webnoth\WML\Element\TerrainType;
use \Webnoth\Renderer\Plugin;

/**
 * Base class for renderers
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
abstract class Base
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
    protected $imageResources = array();
    
    /**
     * render plugins
     * @var Plugin[]
     */
    protected $plugins = array();
    
    /**
     * behave gracefully?
     * @var boolean
     */
    protected $isGraceful = false;
    
    /**
     * Set the terrain types to use when rendering the map.
     * 
     * @param \Webnoth\WML\Collection\TerrainTypes $terrainTypes
     */
    public function setTerrainTypes(TerrainTypes $terrainTypes)
    {
        $this->terrainTypes = $terrainTypes;
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
        
        $col = 0;
        $row = 0;
        foreach ($this->getTilesToRender($map) as $tile) {
            
            //offsets
            $yOffset = ($col%2) ? self::TILE_HEIGHT/2 : 0;
            
            $terrainImages = $this->getTerrainsForTile($tile, $col, $row);
            foreach ($terrainImages as $terrainImage) {
                $x = ($col * (0.75 * self::TILE_WIDTH));
                $y = ($row) * self::TILE_HEIGHT + $yOffset;
                imagecopy(
                    $image,
                    $terrainImage, 
                    $x,
                    $y,
                    0,
                    0,
                    self::TILE_WIDTH,
                    self::TILE_HEIGHT
                );
            }
            $col++;
            if ($col == $map->getWidth()) {
                $col = 0;
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
        //overlays can be null
        if ($tile === null) {
            return array();
        }
        
        $stack = array($tile);
        foreach ($this->plugins as $plugin) {
            $plugin->getTileTerrains($stack, $column, $row);
        }
        
        $terrains = array();
        foreach ($stack as $image) {
            try {
                $terrains[] = $this->getTerrainResource($image);
            } catch (\RuntimeException $exception) {
                if ($this->isGraceful) {
                    continue;
                } else {
                    throw $exception;
                }
            }
        }
        return $terrains;
    }
    
    /**
     * Returns a gd image resource for a specific terrain type
     * 
     * @param string $image
     * @return resource
     */
    protected function getTerrainResource($image)
    {
        if (is_resource($image)) {
            return $image;
        }
        
        if (!isset($this->imageResources[$image])) {
            
            $terrain = $this->terrainTypes->get($image);
            if ($terrain === null) {
                //fallback to direct image loading
                $resource = $this->getTerrainImageResource($image);
                if ($resource != false) {
                    return $resource;
                }
                
                throw new \RuntimeException('Could not get() ' . $image . ' from terrain types.');
            }
            
            $file = $terrain->getSymbolImage();
            $path = $this->imagePath . $file . '.png';
            $this->imageResources[$image] = imagecreatefrompng($path);
        }
        
        if ($this->imageResources[$image] == false) {
            throw new \RuntimeException('Could not load the terrain ' . $image);
        }
        
        return $this->imageResources[$image];
    }
    
    /**
     * Returns a gd image resource for a specific terrain image
     * 
     * @param string $symbolImage
     * @return resource
     * @throws \RuntimeException
     */
    protected function getTerrainImageResource($symbolImage)
    {
        if (!isset($this->imageResources[$symbolImage])) {
            $path = $this->imagePath . $symbolImage . '.png';
            if (!is_file($path)) {
                throw new \RuntimeException('Could not load the terrain ' . $symbolImage . ' from ' . $path);
            }
            $this->imageResources[$symbolImage] = imagecreatefrompng($path);
        }
        
        if ($this->imageResources[$symbolImage] == false) {
            throw new \RuntimeException('Could not create image ' . $symbolImage . ' from ' . $path);
        }
        
        return $this->imageResources[$symbolImage];
    }
    
    /**
     * Creates a gd image resource based on the map
     * 
     * @param \Webnoth\WML\Element\Map $map
     * @return resource
     */
    protected function getImageResource(Map $map)
    {
        $width  = $map->getWidth()  * self::TILE_WIDTH * 0.75 + self::TILE_WIDTH * 0.25;
        $height = $map->getHeight() * self::TILE_HEIGHT       + self::TILE_HEIGHT/2;
        return imagecreatetruecolor($width, $height);
    }
    
    /**
     * Add a plugin that can modify the terrain stack for a tile
     * 
     * @param Plugin $plugin
     */
    public function addPlugin(Plugin $plugin)
    {
        $this->plugins[] = $plugin;
    }
    
    /**
     * toggles graceful behaviour if images cannot be found
     * 
     * @param bool $flag
     */
    public function setGraceful($flag)
    {
        $this->isGraceful = (bool)$flag;
    }
    
    /**
     * Returns all the tiles as a stream.
     * 
     * @param Map $map
     * @see Map::getTiles
     * @return array
     */
    abstract function getTilesToRender(Map $map);
}