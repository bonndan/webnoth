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
     * rendering x offset
     * @var int
     */
    protected $xOffset = 0;
    
    /**
     * rendering y offset
     * @var int
     */
    protected $yOffset = 0;
    
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
     * Write a string to the image (for debugging).
     * 
     * @param string $string
     * @param int    $x
     * @param int    $y
     */
    public function write($string, $x, $y)
    {
        $black = imagecolorallocate($this->image, 0, 0, 0);
        imagestring($this->image, 0, $x, $y, $string, $black);
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
    
    /**
     * A wrapper for image copy: adds the argument to the image at the given position
     * 
     * @param Resource $resource
     * @param int      $x
     * @param int      $y
     */
    public function add(Resource $resource, $x, $y)
    {
        imagecopy(
            $this->image,
            $resource->getImage(), 
            $x + $resource->getXOffset(),
            $y + $resource->getYOffset(),
            0,
            0,
            $resource->width,
            $resource->height
        );
    }
    
    /**
     * Returns the x offset for rendering
     * 
     * @return int
     */
    public function getXOffset()
    {
        return $this->xOffset;
    }

    /**
     * Set the y offset for rendering
     * 
     * @param int $xOffset
     */
    public function setXOffset($xOffset)
    {
        $this->xOffset = (int) $xOffset;
    }

    /**
     * Returns the y offset for rendering
     * 
     * @return int
     */
    public function getYOffset()
    {
        return $this->yOffset;
    }

    /**
     * Set the y offset for rendering.
     * 
     * @param int $yOffset
     */
    public function setYOffset($yOffset)
    {
        $this->yOffset = (int) $yOffset;
    }
}