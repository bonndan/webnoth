<?php

namespace Webnoth\Renderer\Plugin;

use \Webnoth\WML\Collection\TerrainTypes;

/**
 * A renderer plugin which which provides fluents transition between the tiles
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class Transitions implements \Webnoth\Renderer\Plugin
{
    /**
     * the terrain types
     * @var \Webnoth\WML\Collection\TerrainTypes
     */
    protected $terrainTypes = null;

    /**
     * the map
     * @var \Webnoth\WML\Element\Map 
     */
    protected $map = null;

    /**
     * clockwise rotation for traversing surrounding tiles
     * @var array
     */
    protected $rotation = array('ne', 'se', 's', 'sw', 'nw', 'n');
    
    /**
     * preliminary transition rules
     * @var array currentTerrain => checkAgainstTerrain[]
     */
    protected $transitions = array(
        'Aa' => array(
            'Ai'
        ),
        
        //grassland
        'Gt' => array(
            'Aa',
            'Fp',
            array('Ss', false, false),
            array('Hh', false, false),
            'Mm',
            'Gs',
            'Ql',
            'Rr',
            'Ds'
        ),

        //medium shallow water
        'Ww' => array(
            'Mm',
            array('Wo', false, false),
            array('Gg', false, false),
            'Gs',
            'Aa',
            'Ds',
            array('Hh', false, false),
            'Rr',
            'Ss',
            'Ql',
            'Ai'
        ),
        
        //hills
        'Hh' => array(
            'Aa',
            'Ds',
            'Ql',
        ),

        //ocean
        'Wo' => array(
            array('Gg', false, false),
            array('Hh', false, false),
            'Gs',
            'Ds',
            'Ss',
            'Ai'
        ),
        
        //regular dirt
        'Re' => array(
            array('Gg', false, false),
            array('Ww', 'Gg', false),
            array('Wo', 'Gg', false),
            'Rr'
        ),

        //road
        'Rr' => array(
            'Ds'
        ),

        //forest
        '^Fp' => array(
            'Hh',
            array('Ww', 'Gg'),//coast->grass
            'Ss'
        ),

        //Mountains
        'Mm' => array(
            'Ql'
        )
    );

    /**
     * Initialize the renderer with the available terrains.
     * 
     * @param TerrainTypes $terrainTypes
     */
    public function __construct(TerrainTypes $terrainTypes)
    {
        $this->terrainTypes = $terrainTypes;
    }

    /**
     * Injects the map.
     * 
     * @param \Webnoth\WML\Element\Map $map
     */
    public function setMap(\Webnoth\WML\Element\Map $map)
    {
        $this->map = $map;
    }
    
    /**
     * Adds tiles which provides a fluent transition between the tiles
     * 
     * @param array $tileStack
     * @param int   $column
     * @param int   $row
     * 
     * @return array
     */
    public function getTileTerrains(array &$tileStack, $column, $row)
    {
        $surrounding        = $this->map->getSurroundingTerrains($column, $row);
        $currentTerrain     = $this->map->getTerrainAt($column, $row);
        $currentBaseTerrain = $this->terrainTypes->getBaseTerrain($currentTerrain);

        $transitionsToCheck = $this->getTransitionsToCheck($currentBaseTerrain);
        
        foreach ($transitionsToCheck as $transition) {
            $terrain         = $currentBaseTerrain;
            $merged          = true;
            $checkAgainst    = $transition;
            
            if (is_array($transition)) {
                $checkAgainst     = $transition[0];
                $pretendedTerrain = $transition[1];
                $merged           = $transition[2];
                if ($pretendedTerrain != false) {
                    $terrain = $pretendedTerrain;
                }
            }
            
            $tileStack += $this->getTerrainTransitions($surrounding, $terrain, $checkAgainst, $merged);
        }
    }

    /**
     * provides the transitions to check against for a specific terrain type
     * 
     * @param string $baseTerrain
     * @return array(string|array)
     */
    protected function getTransitionsToCheck($baseTerrain)
    {
        if (isset($this->transitions[$baseTerrain])) {
            $checkAgainst = $this->transitions[$baseTerrain];
        } else {
            $checkAgainst = array();
        }
        
        //alway check against void
        $checkAgainst[] = array('Xv', false, false);
        
        return $checkAgainst;
    }
    
    /**
     * add string indicators which build the css classes
     * 
     * @param array   $surrounding    surrounding tiles
     * @param string  $currentTerrain current terrain
     * @param boolean $merged
     * @return array
     */
    public function getTerrainTransitions(array $surrounding, $currentTerrain, $merged = true)
    {
        $currentTerrainImage = $this->terrainTypes->get($currentTerrain)->getSymbolImage();
        $transitions = array();
        
        /* true where terrain different */
        $hasTransition = array();
        foreach ($this->rotation as $direction) {
            $baseTerrain = $this->terrainTypes->getBaseTerrain($surrounding[$direction]);
            $hasTransition[$direction] = ($baseTerrain != $currentTerrain);
        }

        /*  no different terrain around */
        if (!in_array(true, $hasTransition)) {
            return $transitions;
        }

        /* separate files */
        if ($merged != true) {
            foreach ($hasTransition as $direction => $set) {
                if ($set)
                    $transitions[] = $currentTerrainImage . '_' . $direction;
            }
            return $transitions;
        }

        $cluster = array();
        $p = 0;
        //merge if "ne" and "n" have transitions
        $mergeFirstLast = ($hasTransition[$this->rotation[0]] && $hasTransition[$this->rotation[5]]);

        foreach ($this->rotation as $direction) {
            if (!$hasTransition[$direction])
                $p++;
            else
                $cluster[$p][] = $direction;
        }

        //merge if the last in rotation is different
        // but not if just the last in rotation is different
        if ($mergeFirstLast && $p < 5) {
            //find first with different and merge with last
            for ($i = 0; $i <= $p; $i++)
                if (count($cluster[$p]) > 0) {
                    $first = $i;
                    break;
                }
            $cluster[$first] = array_merge($cluster[$first], $cluster[$p]);
            unset($cluster[$p]);
        }

        //one div for each cluster
        foreach ($cluster as $c) {
            $transitions[] = $currentTerrainImage . '_' . implode('_', $c);
        }
        
        return $transitions;
    }
}