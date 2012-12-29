<?php

namespace Webnoth\Renderer\Resource;

/**
 * Resource factory
 */
class Factory
{
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
}