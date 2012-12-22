<?php

namespace Webnoth\WML;

/**
 * Parser for Wesnoth maps (old format)
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @link http://wiki.wesnoth.org/BuildingMaps
 */
class MapParser extends Parser
{
    /**
     * expects data which is not enclosed in map tags
     * 
     * @param string $input
     * @return \Webnoth\WML\Element\Map one map element
     * @throws RuntimeException
     */
    public function parse($input)
    {
        $header = '';
        $rows  = array();
        $headerEnded = false;
        $lines = explode(PHP_EOL, $input);
        foreach ($lines as $line) {
            if (trim($line) == '') {
                $headerEnded = true;
                continue;
            }
            
            if ($headerEnded) {
                $rows[] = array_map('trim', explode(',', $line));
            } else {
                $header .= $line . PHP_EOL;
            }
        }
        //the data must be enclosed by element tags
        $input = "[map]" . PHP_EOL . $header . "[/map]";
        $result = parent::parse($input);
        $map = $result->first();
        if (!$map instanceof \Webnoth\WML\Element\Map) {
            throw new RuntimeException('Could not create a map instance.');
        }
        
        /* @var $map \Webnoth\WML\Element\Map */
        foreach ($rows as $row) {
            $map->addRawTileRow($row);
        }
        
        return $map;
    }
}