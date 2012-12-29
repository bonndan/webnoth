<?php

namespace Webnoth\Renderer;

/**
 * Wrapper for gd image resources
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class Resource
{
    /**
     * the gd resource
     * @var resource
     */
    protected $image;
    
    /**
     * image width
     * @var int
     */
    protected $width;
    
    /**
     * image height
     * @var int
     */
    protected $height;
    
    /**
     * Initialise with a gd resource.
     * 
     * @param resource $resource
     */
    public function __construct($resource)
    {
        if (!is_resource($resource)) {
            throw new \InvalidArgumentException("Argument is not a resource");
        }
        $this->image  = $resource;
        $this->width  = imagesx($resource);
        $this->height = imagesy($resource);
    }
    
    /**
     * Returns the gd resource.
     * 
     * @return resource
     */
    public function getImage()
    {
        return $this->image;
    }
}