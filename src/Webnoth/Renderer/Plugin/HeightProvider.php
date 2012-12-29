<?php

namespace Webnoth\Renderer\Plugin;

/**
 * A renderer plugin which which provides fluents transition between the tiles
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class HeightProvider extends Base implements \Webnoth\Renderer\Plugin
{
    /**
     * the default height if no alias is given
     * @var string
     */
    const DEFAULT_ALIAS = 'flat/flat';
    
    /**
     * terrain aliases: which terrain appears how in the height map
     * @var array
     */
    protected $aliases = array();
    
    /**
     * Pass the aliases to the constructor.
     * 
     * @param array $terrainAliases
     */
    public function __construct(array $terrainAliases)
    {
        $this->aliases = $terrainAliases;
    }
    
    /**
     * Replaces the whole stack
     * 
     * @param array $tileStack
     * @param int   $column
     * @param int   $row
     */
    public function getTileTerrains(array &$tileStack, $column, $row)
    {
        $terrain = $tileStack[0];
        $alias   = $this->getAliasFor($terrain);
        $this->map->setHeightAt($column, $row, $alias);
        $tileStack = array($alias);
    }

    /**
     * Returns the replacement for a terrain
     * 
     * @param string $terrain
     * @return string
     */
    protected function getAliasFor($terrain)
    {
        if (!array_key_exists($terrain, $this->aliases)) {
            return self::DEFAULT_ALIAS;
        }
        
        return $this->aliases[$terrain];
    }
}