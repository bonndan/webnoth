<?php

namespace Webnoth\Renderer\Plugin;

/**
 * A renderer for special terrain (castles etc.)
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class SpecialTerrain extends Base implements \Webnoth\Renderer\Plugin
{
    /**
     * callback filters
     * @var array (terrain => callback method)
     */
    protected $replacements = array(
        'castle/elven/tile-s' => null,
        'castle/elven/tile-sw-nw-n' => null,
        'castle/elven/tile-s-sw' => null,
        '^Ve' => 'village/elven2',
        '^Fet' => 'forest/great-tree',
    );
    
    /**
     * Handles special terrains in the stack.
     * 
     * @param array $tileStack
     * @param int $column
     * @param int $row
     */
    public function getTileTerrains(array &$tileStack, $column, $row)
    {
        $terrain = $this->map->getTerrainAt($column, $row);
        
        foreach ($tileStack as $key => $terrain) {
            if (!is_string($terrain)) {
                continue;
            }
            
            if (array_key_exists($terrain, $this->replacements)) {
                $tileStack[$key] = $this->replacements[$terrain];
            }
        }
    }
}