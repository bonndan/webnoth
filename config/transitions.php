<?php
/**
 * preliminary terrain transition rules
 */
use Webnoth\Renderer\Transition;

/* @var $terrainTypes \Webnoth\WML\Collection\TerrainTypes */

return array(
    //grassland
    'Gg' => array(
        Transition::create($terrainTypes->get('Gs'), array('grass/semi-dry' => 3)), 
    ),
    'Ww' => array(
        Transition::create($terrainTypes->get('Gg'), array('flat/bank-to-ice' => 2, 'grass/green' => 2)), 
        Transition::create($terrainTypes->get('Gs'), array('flat/bank-to-ice' => 2, 'grass/semi-dry-abrupt' => 2)), 
        Transition::create($terrainTypes->get('Wwf'), array('water/ford' => 1)), 
    ),
);
