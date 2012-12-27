<?php

namespace Webnoth\WML;

/**
 * Plugin for the map which handles "Gg^Fsd" like terrains.
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class TerrainSeparator extends \Webnoth\Renderer\Plugin\Base
{
    const WATER_HEIGHT     = 0.0;
    const FLAT_HEIGHT      = 0.2;
    const HILL_HEIGHT      = 1;
    const MOUNTAIN_HEIGHT  = 3;
    
    /**
     * Processes raw terrain input.
     * 
     * @param array $tileStack
     * @param int   $column
     * @param int   $row
     */
    public function processRawTerrain($column, $row, $rawTerrain)
    {
        $separated = $this->separateRawTerrain($rawTerrain);
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