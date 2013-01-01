<?php

namespace Webnoth\WML;

/**
 * Plugin for the map which handles "Gg^Fsd" like terrains.
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class TerrainSeparator
{
    /**
     * map
     * @var \Webnoth\WML\Element\Map 
     */
    protected $map;
    
    /**
     * Pass the map to the constructor.
     * 
     * @param \Webnoth\WML\Element\Map $map
     */
    public function __construct(Element\Map $map)
    {
        $this->map = $map;
    }
    
    /**
     * Processes raw terrain input.
     * 
     * @param array $tileStack
     * @param int   $column
     * @param int   $row
     */
    public function processRawTerrain($column, $row, $rawTerrain)
    {
        /*
         * starting positions are noted like "... GG, 3 Ke, Gg, ..."
         */
        if (strpos($rawTerrain, ' ') !== false) {
            $parts = explode(' ', $rawTerrain);
            $this->map->setStartingPosition($column, $row, $parts[0]);
            $rawTerrain = $parts[1];
        }
            
        $separated = $this->separateRawTerrain($rawTerrain);
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
        $overlay = null;
        
        //either no caret or at the beginning
        if (strpos($terrain, '^') !== false) {
            $parts = explode('^', $terrain);
            $terrain = $parts[0];
            $overlay = '^' . $parts[1];
        }
        
        return array(
            'terrain' => $terrain,
            'overlay' => $overlay
        );
    }
}