<?php

namespace Webnoth\Renderer\Plugin;

/**
 * Base for Renderer Plugins
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
abstract class Base
{
    /**
     * the layer which is used by the plugin
     * @var \Webnoth\WML\Element\Layer 
     */
    protected $layer;
    
    /**
     * Set the layer to work on.
     * 
     * @param \Webnoth\WML\Element\Layer $map
     */
    public function setLayer(\Webnoth\WML\Element\Layer $layer)
    {
        $this->layer = $layer;
    }
}