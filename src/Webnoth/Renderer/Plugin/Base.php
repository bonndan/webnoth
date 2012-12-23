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
     * map
     * @var \Webnoth\WML\Element\Map 
     */
    protected $map;
    
    /**
     * set the map
     * 
     * @param \Webnoth\WML\Element\Map $map
     */
    public function setMap(\Webnoth\WML\Element\Map $map)
    {
        $this->map = $map;
    }
}