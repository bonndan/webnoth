<?php

namespace Webnoth\Renderer\Plugin;

/**
 * Default plugin: Handles "Gg^Fsd" like terrains.
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class Separator extends Base implements \Webnoth\Renderer\Plugin
{
    const WATER_HEIGHT     = 0.0;
    const FLAT_HEIGHT      = 0.2;
    const HILL_HEIGHT      = 1;
    const MOUNTAIN_HEIGHT  = 3;
    
    /**
     * Modifies the stack. If the lowest terrain has an overlay, the overlay is removed.
     * 
     * @param array $tileStack
     * @param int   $column
     * @param int   $row
     */
    public function getTileTerrains(array &$tileStack, $column, $row)
    {
        $first     = $tileStack[0];
        $separated = $this->separateRawTerrain($first);
        $tileStack[0] = $separated['terrain'];
        $this->map->setHeightAt($column,  $row, $separated['height']);
        $this->map->setTerrainAt($column, $row, $separated['terrain']);
        $this->map->setOverlayAt($column, $row, $separated['overlay']);
    }
    
    /**
     * Separate a raw terrain string into several terrain types if needed
     * 
     * @param string $terrain
     * @return array
     */
    protected function separateRawTerrain($terrain)
    {
        $height  = self::FLAT_HEIGHT;
        $overlay = null;
        
        //either no caret or at the beginning
        if (strpos($terrain, '^') !== false) {
            $parts = explode('^', $terrain);
            $terrain = $parts[0];
            $overlay = '^' . $parts[1];
        }
        
        return array(
            'height'  => $height,
            'terrain' => $terrain,
            'overlay' => $overlay
        );
    }
}