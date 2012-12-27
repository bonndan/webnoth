<?php

namespace Webnoth\Renderer\Plugin;

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
     * 
     * @param array $tileStack
     * @param type $column
     * @param type $row
     */
    public function getTileTerrains(array &$tileStack, $column, $row)
    {
        //offsets
        $yOffset = ($column % 2) ? $this->size / 2 : 0;
        $x = ($column * (0.75 * $this->size));
        $y = ($row) * $this->size + $yOffset;

        $terrain = $this->map->getTerrainAt($column, $row);
        $tileStack[] = $this->getStamp($column . '.' . $row, $terrain);
    }

    /**
     * Creates a stamp with the debug text.
     * 
     * @param string $coords
     * @param string $terrain
     * @return resource
     */
    protected function getStamp($coords, $terrain)
    {
        $image = imagecreatetruecolor($this->size, $this->size);
        imagesavealpha($image, true);
        $trans = imagecolorallocatealpha($image, 0, 0, 0, 127);
        $black = imagecolorallocate($image, 0, 0, 0);
        imagefill($image, 0, 0, $trans);
        imagestring($image, 0, $this->size / 3, $this->size / 3, $coords, $black);
        imagestring($image, 0, $this->size / 3, $this->size / 2, $terrain, $black);
        return $image;
    }

}