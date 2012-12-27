<?php

namespace Webnoth\WML\Collection;

use \Doctrine\Common\Collections\ArrayCollection;

/**
 * TerrainTypes collection
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class TerrainTypes extends ArrayCollection
{
    /**
     * Resolves a terrain to its base terrain
     * 
     * @param string $rawTerrain
     * @return string
     * @throws \RuntimeException
     */
    public function getBaseTerrain($rawTerrain, $allowHidden = false)
    {
        //no caret or not at the beginning means two terrains in string
        if (strpos($rawTerrain, '^') > 0) {
            $rawTerrain = current(explode('^', $rawTerrain));
        }
        
        /* @var $terrain \Webnoth\WML\Element\TerrainType */
        $terrain = $this->get($rawTerrain);
        if ($terrain === null) {
            throw new \RuntimeException('Unknown terrain: ' . $rawTerrain);
        }
        
        $baseTerrain = $terrain->getBaseTerrain();
        $baseTerrainType = $this->get($baseTerrain);
        if (!$baseTerrainType->isHidden() || $allowHidden) {
            $rawTerrain = $baseTerrainType->getString();
        }

        return $rawTerrain;
    }
}