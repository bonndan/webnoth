<?php

namespace Webnoth\WML\Element;

use \Webnoth\WML\TerrainSeparator;

/**
 * Element containing map data
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class Map extends \Webnoth\WML\Element
{
    const LAYER_TERRAIN  = 'terrains';
    const LAYER_HEIGHTS  = 'heights';
    const LAYER_OVERLAYS = 'overlays';
    
    /**
     * raw terrain splitter
     * @var \Webnoth\WML\TerrainSeparator
     */
    protected $separator = null;
    
    /**
     * width in tiles
     * @var int
     */
    protected $width = null;
    
    /**
     * terrain layer
     * @var Layer
     */
    protected $terrains = null;
    
    /**
     * height layer
     * @var Layer
     */
    protected $heights = null;
    
    /**
     * overlay layer
     * @var Layer
     */
    protected $overlays = null;
    
    /**
     * all starting positions of the sides
     * @var array (tilenumber => position)
     */
    protected $startingPositions = array();
    
    /**
     * Creates a completely configured map
     * 
     * @return Map
     */
    public static function create()
    {
        $terrainTypes = self::getCache()->fetch('terrain');
        $terrainLayer = new Layer($terrainTypes);
        $overlayLayer = new Layer($terrainTypes);
        
        $terrainTypes = include APPLICATION_PATH . '/config/height-terrains.php';
        $heightLayer  = new Layer($terrainTypes);
        
        $map = new Map(
            $terrainLayer,
            $overlayLayer,
            $heightLayer
        );
        $separator = new TerrainSeparator($map, include APPLICATION_PATH . '/config/terrain-heightaliases.php');
        $map->setTerrainSeparator($separator);
        
        return $map;
    }
    
    /**
     * Pass the three layer instances to the constructor.
     * 
     * @param \Webnoth\WML\Element\Layer $terrainLayer
     * @param \Webnoth\WML\Element\Layer $overlayLayer
     * @param \Webnoth\WML\Element\Layer $heightLayer
     */
    public function __construct(Layer $terrainLayer, Layer $overlayLayer, Layer $heightLayer)
    {
        $this->terrains  = $terrainLayer;
        $this->overlays  = $overlayLayer;  
        $this->heights   = $heightLayer;
        
    }
    
    /**
     * Inject the terrain separator.
     * 
     * @param \Webnoth\WML\TerrainSeparator $separator
     */
    public function setTerrainSeparator(TerrainSeparator $separator)
    {
        $this->separator = $separator;
    }
    
    /**
     * adds a row of tiles (terrain type strings)
     * 
     * @param array $tiles
     */
    public function addRawTileRow(array $tiles)
    {
        $this->setWidth(count($tiles));
        $column = 0;
        $row = $this->terrains->getRowCount();
        foreach ($tiles as $rawTile) {
            $this->separator->processRawTerrain($column, $row, $rawTile);
            $column++;
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
        return $this->terrains->getRowCount();
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
     * Set a specific terrain to the terrain layer
     * 
     * @param int    $column
     * @param int    $row
     * @param string $terrain
     */
    public function setTerrainAt($column, $row, $terrain)
    {
        $this->terrains->setTerrainAt($column, $row, $terrain);
    }
    
    /**
     * Set a value to the height map.
     * 
     * @param int    $column
     * @param int    $row
     * @param string $height
     */
    public function setHeightAt($column, $row, $height)
    {
        $this->heights->setTerrainAt($column, $row, $height);
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
        $this->overlays->setTerrainAt($column, $row, $terrain); 
    }
    
    /**
     * Fetch a specified layer.
     * 
     * @param string $layer
     * @return Layer
     * @throws \InvalidArgumentException
     */
    public function getLayer($layer)
    {
        if (!in_array($layer, array(self::LAYER_HEIGHTS, self::LAYER_OVERLAYS, self::LAYER_TERRAIN))) {
            throw new \InvalidArgumentException('Unknown layer: ' . $layer);
        }
        
        return $this->$layer;
    }
    
    /**
     * Set a starting position
     * 
     * @param int   $column
     * @param int   $row
     * @param float $terrain
     */
    public function setStartingPosition($column, $row, $number)
    {
        $this->startingPositions[$number] = array($row, $column);
    }
    
    /**
     * creates a cache instance
     * 
     * @return \Doctrine\Common\Cache\FilesystemCache
     */
    protected static function getCache()
    {
        $cache = new \Doctrine\Common\Cache\FilesystemCache(CACHE_PATH);
        return $cache;
    }
}