<?php

namespace Webnoth\Renderer\Plugin;

use \Webnoth\Renderer\Resource\Factory;

/**
 * Debug plugin renders coordinates and terrains as strings
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class Debug extends Base implements \Webnoth\Renderer\Plugin
{

    /**
     * stamp dimension
     * @var int
     */
    protected $size = 0;

    /**
     * Sets the stamp dimension
     * 
     * @param int $size
     */
    public function __construct($size)
    {
        $this->size = $size;
    }

    /**
     * Adds a transparent image to the stack containing debug infos.
     * 
     * @param array $tileStack
     * @param int $column
     * @param int $row
     */
    public function getTileTerrains(array &$tileStack, $column, $row)
    {
        $terrain = $tileStack[0];
        $coords = $column . '.' . $row;
        $tileStack[] = $this->getStamp($coords, $terrain);
    }

    /**
     * Creates a stamp with the debug text.
     * 
     * @param string $coords
     * @param string $terrain
     * @return \Webnoth\Renderer\Resource
     */
    protected function getStamp($coords, $terrain)
    {
        $resource = Factory::create($this->size, $this->size);
        $resource->write($coords, $this->size / 3, $this->size / 3);
        $resource->write($terrain, $this->size / 3, $this->size / 2);
        return $resource;
    }
}