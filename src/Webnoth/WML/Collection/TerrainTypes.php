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
    public function getBaseTerrain($rawTerrain)
    {
        if (strpos($rawTerrain, '^') !== false) {
            $rawTerrain = current(explode('^', $rawTerrain));
        }

        
        /* @var $terrain \Webnoth\WML\Element\TerrainType */
        $terrain = $this->get($rawTerrain);
        if ($terrain === null) {
            throw new \RuntimeException('Unknown terrain: ' . $rawTerrain);
        }
        
        if ($terrain->offsetExists('default_base')) {
            $rawTerrain = $terrain->offsetGet('default_base');
        } elseif ($terrain->offsetExists('alias_of')) {
            $rawTerrain = $terrain->offsetGet('alias_of');
        }

        return $rawTerrain;
    }
}