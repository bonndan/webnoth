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
     * the default height if no alias is given
     * @var string
     */
    const DEFAULT_HEIGHT = 'flat';
    
    /**
     * map
     * @var \Webnoth\WML\Element\Map 
     */
    protected $map;
    
     /**
     * height aliases: which terrain appears how in the height map
     * @var array
     */
    protected $heightAliases = array();
    
    /**
     * Pass the map to the constructor.
     * 
     * @param \Webnoth\WML\Element\Map $map
     * @param array                    $heightAliases
     */
    public function __construct(Element\Map $map, array $heightAliases)
    {
        $this->map           = $map;
        $this->heightAliases = $heightAliases;
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
        $this->map->setHeightAt( $column, $row, $separated['height']);
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
            'overlay' => $overlay,
            'height'  => $this->getHeightAliasFor($terrain)
        );
    }
    
    /**
     * Returns the replacement for a terrain
     * 
     * @param string $terrain
     * @return string
     */
    protected function getHeightAliasFor($terrain)
    {
        if (!array_key_exists($terrain, $this->heightAliases)) {
            return self::DEFAULT_HEIGHT;
        }
        
        return $this->heightAliases[$terrain];
    }
}