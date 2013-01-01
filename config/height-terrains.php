<?php
/**
 * Height map terrains
 */
$void = new \Webnoth\WML\Element\TerrainType('void');
$void->offsetSet('symbol_image', 'void/black');

$water = new \Webnoth\WML\Element\TerrainType('water');
$water->offsetSet('symbol_image', 'water/water');

$flat = new \Webnoth\WML\Element\TerrainType('flat');
$flat->offsetSet('symbol_image', 'flat/flat');

$hills = new \Webnoth\WML\Element\TerrainType('hills');
$hills->offsetSet('symbol_image', 'hills/hills');

$mountains = new \Webnoth\WML\Element\TerrainType('mountains');
$mountains->offsetSet('symbol_image', 'mountains/white');

$elements = array(
    $void->getString()      => $void,
    $water->getString()     => $water,
    $flat->getString()      => $flat,
    $hills->getString()     => $hills,
    $mountains->getString() => $mountains
);

$terrainTypes = new \Webnoth\WML\Collection\TerrainTypes($elements);

return $terrainTypes;