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
    protected $filters = array(
        'castle/elven/tile-s' => 'remove',
        'castle/elven/tile-sw-nw-n' => 'remove',
        'castle/elven/tile-s-sw' => 'remove',
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
            if (!is_string($terrain) || !array_key_exists($terrain, $this->filters)) {
                continue;
            }
            $callback = array($this, $this->filters[$terrain]);
            $tileStack[$key] = call_user_func_array($callback, array($terrain, $column, $row));
        }
    }
    
    /**
     * Nullifies the entry.
     * 
     * @param string $terrain
     * @param int    $column
     * @param int    $row
     */
    protected function remove($terrain, $column, $row)
    {
        return null;
    }
    
    
}