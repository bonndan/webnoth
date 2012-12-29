<?php

namespace Webnoth\Renderer;

use Webnoth\WML\Collection\TerrainTypes;
use Webnoth\WML\Element\Map;
use Webnoth\WML\Element\TerrainType;
use Webnoth\Renderer\Plugin;
use Webnoth\Renderer\Resource\Factory;
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
     * resource factory
     * @var Factory 
     */
    protected $factory = null;
    
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
     * The constructor requires the terrain types to use an a resource factory.
     * 
     * @param TerrainTypes $terrainTypes
     * @param \Webnoth\Renderer\Resource\Factory $factory
     */
    public function __construct(TerrainTypes $terrainTypes, Factory $factory)
    {
        $this->setTerrainTypes($terrainTypes);
        $this->factory = $factory;
    }
    
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
     * @return \Webnoth\Renderer\Resource
     */
    public function render(Map $map)
    {
        //initialize the plugins with the map
        foreach ($this->plugins as $plugin) {
            $plugin->setMap($map);
        }
        
        $image = Factory::createForMap($map);
        
        $col = 0;
        $row = 0;
        foreach ($this->getTilesToRender($map) as $tile) {
            
            //offsets
            $yOffset = ($col%2) ? self::TILE_HEIGHT/2 : 0;
            
            $terrainImages = $this->getTerrainsForTile($tile, $col, $row);
            foreach ($terrainImages as $resource) {
                $x = ($col * (0.75 * self::TILE_WIDTH));
                $y = ($row) * self::TILE_HEIGHT + $yOffset;
                $image->add($resource, $x, $y);
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
     * @return \Webnoth\Renderer\Resource[]
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
                $terrains[] = $this->getResourceFor($image);
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
     * Returns a resource for a specific terrain type
     * 
     * @param mixed $terrain
     * @return \Webnoth\Renderer\Resource
     */
    protected function getResourceFor($terrain)
    {
        if (is_resource($terrain)) {
            return new \Webnoth\Renderer\Resource($terrain);
        }
        
        if ($terrain instanceof \Webnoth\Renderer\Resource) {
            return $terrain;
        }
        
        if (!isset($this->imageResources[$terrain])) {
            
            $terrainType = $this->terrainTypes->get($terrain);
            if ($terrainType === null) {
                //fallback to direct image loading
                $this->imageResources[$terrain] = $this->factory->createFromPng($terrain);
            } else {
                $file = $terrainType->getSymbolImage();
                $this->imageResources[$terrain] = $this->factory->createFromPng($file);
            }
        }
        
        if ($this->imageResources[$terrain] == false) {
            throw new \RuntimeException('Could not load the terrain ' . $terrain);
        }
        
        return $this->imageResources[$terrain];
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
    abstract protected function getTilesToRender(Map $map);
}