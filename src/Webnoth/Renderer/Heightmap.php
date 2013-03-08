<?php

namespace Webnoth\Renderer;

use Webnoth\Renderer\Resource\Factory;

/**
 * Renderer for the height map.
 * 
 * Be sure to add the height provider plugin, then the transitions plugin.
 * 
 * @author  Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 * @deprecated
 */
class Heightmap extends Base
{
    const GRAY_VALUE = 50;
    
    /**
     * Calls the resource factory to create an image for the layer.
     * 
     * @param \Webnoth\WML\Element\Layer $layer
     * @return \Webnoth\Renderer\Resource
     */
    protected function createLayerImage(\Webnoth\WML\Element\Layer $layer)
    {
        $resource = Factory::createForLayer($layer);
        $image    = $resource->getImage();
        
        $gray = imagecolorallocate($image, self::GRAY_VALUE, self::GRAY_VALUE, self::GRAY_VALUE);
        imagefill($image, 0, 0, $gray);
        
        return $resource;
    }
}