<?php

namespace Webnoth;

use Webnoth\WML\Collection\TerrainTypes;
use Webnoth\WML\Element\Map;
use Webnoth\WML\Element\TerrainType;
use \Webnoth\Renderer\Plugin;

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
     * behave gracefully?
     * @var boolean
     */
    protected $isGraceful = false;
    
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
        
        $col = 0;
        $row = 0;
        foreach ($map->getTiles() as $tile) {
            
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
            $this->stampCoordinates($image, $x, $y, $col . '.' . $row);
            $col++;
            if ($col == $map->getWidth()) {
                $col = 0;
                $row++;
            }
        }
        
        return $image;
    }
    
    /**
     * prints coordinates on the image
     * 
     * @param resource $image
     * @param int      $x
     * @param int      $y
     * @param string   $string
     */
    protected function stampCoordinates($image, $x, $y, $string)
    {
        $black = imagecolorallocate($image, 0, 0, 0);
        imagestring($image, 0, $x + self::TILE_HEIGHT/2, $y + self::TILE_HEIGHT/2, $string, $black);
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
            try {
                $terrains[] = $this->getTerrainResource($terrainType);
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
     * @param string $terrainString
     * @return resource
     */
    protected function getTerrainResource($terrainString)
    {
        if (is_resource($terrainString)) {
            return $terrainString;
        }
        
        if (!isset($this->terrainResources[$terrainString])) {
            
            $terrain = $this->terrainTypes->get($terrainString);
            if ($terrain === null) {
                //fallback to direct image loading
                $resource = $this->getTerrainImageResource($terrainString);
                if ($resource != false) {
                    return $resource;
                }
                
                throw new \RuntimeException('Could not get() ' . $terrainString . ' from terrain types.');
            }
            
            $file = $terrain->getSymbolImage();
            $path = $this->imagePath . $file . '.png';
            $this->terrainResources[$terrainString] = imagecreatefrompng($path);
        }
        
        if ($this->terrainResources[$terrainString] == false) {
            throw new \RuntimeException('Could not load the terrain ' . $terrainString);
        }
        
        return $this->terrainResources[$terrainString];
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
        if (!isset($this->terrainResources[$symbolImage])) {
            $path = $this->imagePath . $symbolImage . '.png';
            if (!is_file($path)) {
                throw new \RuntimeException('Could not load the terrain ' . $symbolImage . ' from ' . $path);
            }
            $this->terrainResources[$symbolImage] = imagecreatefrompng($path);
        }
        
        if ($this->terrainResources[$symbolImage] == false) {
            throw new \RuntimeException('Could not create image ' . $symbolImage . ' from ' . $path);
        }
        
        return $this->terrainResources[$symbolImage];
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
}