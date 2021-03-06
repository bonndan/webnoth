<?php

namespace Webnoth\Renderer\Resource;

use \Webnoth\WML\Element\Layer;

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
     * The image path can be passed to the constructor.
     * 
     * @param string $imagePath
     */
    public function __construct($imagePath = null)
    {
        if ($imagePath !== null) {
            $this->setImagePath($imagePath);
        }
    }
    
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
     * Creates a resource with an empty image for a layer.
     * 
     * @param Layer $layer
     * @return \Webnoth\Renderer\Resource
     */
    public static function createForLayer(Layer $layer)
    {
        $width  = $layer->getWidth()    * self::TILE_WIDTH * 0.75 + self::TILE_WIDTH * 0.25;
        $height = $layer->getRowCount() * self::TILE_HEIGHT       + self::TILE_HEIGHT/2;
        
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
    
    /**
     * Creates a resource for a terrain.
     * 
     * @param string $imageBase
     * @throws \RuntimeException
     * @return \Webnoth\Renderer\Resource
     */
    public function createFromPng($imageBase)
    {
        $path = $this->imagePath . '/' . $imageBase . '.png';
        if (!is_file($path)) {
            throw new \RuntimeException('Could not load the file ' . $imageBase . ' from ' . $path);
        }
        $image = imagecreatefrompng($path);
        return new \Webnoth\Renderer\Resource($image);
    }
}