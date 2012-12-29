<?php

namespace Webnoth\Renderer\Resource;

use \Webnoth\WML\Element\Map;

/**
 * Resource factory
 * 
 * @author  Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class Factory
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
     * path where the terrain images reside
     * @var string
     */
    protected $imagePath;
    
    /**
     * Set the path where the terrain images reside.
     * 
     * @param string $path
     */
    public function setImagePath($path)
    {
        if (!is_dir($path)) {
            throw new \InvalidArgumentException($path . ' is not a directory.');
        }
        
        $this->imagePath = $path;
    }
    
    /**
     * Creates a resource with an empty image for a map.
     * 
     * @param Map $map
     * @return \Webnoth\Renderer\Resource
     */
    public static function createForMap(Map $map)
    {
        $width  = $map->getWidth()  * self::TILE_WIDTH * 0.75 + self::TILE_WIDTH * 0.25;
        $height = $map->getHeight() * self::TILE_HEIGHT       + self::TILE_HEIGHT/2;
        
        return self::create($width, $height);
    }
    
    /**
     * Creates a resource with an empty image.
     * 
     * @param int $width
     * @param int $height
     * @return \Webnoth\Renderer\Resource
     */
    public static function create($width, $height)
    {
        $image  = imagecreatetruecolor($width, $height);
        imagesavealpha($image, true);
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefill($image, 0, 0, $transparent);
        
        return new \Webnoth\Renderer\Resource($image);
    }
}