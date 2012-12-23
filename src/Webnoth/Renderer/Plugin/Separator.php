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
    /**
     * Modifies the stack.
     * 
     * @param array $tileStack
     */
    public function getTileTerrains(array &$tileStack, $column, $row)
    {
        $offset = -1;
        foreach ($tileStack as $terrainType) {
            $offset++;
            $newTerrain = $this->separateTerrains($terrainType);
            if ($newTerrain === null) {
                continue;
            }
            
            array_splice($tileStack, $offset, 1, $newTerrain);
            return $this->getTileTerrains($tileStack, $column, $row);
        }
    }
    
    /**
     * Separate a raw terrain string into several terrain types if needed
     * 
     * @param string $terrainType
     * @return array
     */
    protected function separateTerrains($terrainType)
    {
        //either no caret or at the beginning
        if (strpos($terrainType, '^') == 0) {
            return null;
        }
        
        $parts = explode('^', $terrainType);
        foreach ($parts as $key => $part) {
            if ($key == 0) {
                continue;
            }
            
            $parts[$key] = '^' . $part;
        }
        return $parts;
    }
}