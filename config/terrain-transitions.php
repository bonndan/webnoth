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
        Transition::create($terrainTypes->get('Re'), array('flat/dirt2' => 2)),
    ),
    
    //semi dry grass
    'Gs' => array(
        Transition::create($terrainTypes->get('Re'), array('flat/dirt2' => 2)),
    ),
    
    //hills
    'Hh' => array(
        Transition::create($terrainTypes->get('Gs'), array('grass/semi-dry' => 3)), 
    ),
    
    //dirt
    'Re' => array(
        Transition::create($terrainTypes->get('Gs'), array('flat/dirt' => 4)), 
        Transition::create($terrainTypes->get('Ww'), array('flat/dirt' => 4)), 
    ),
    
    //water
    'Ww' => array(
        Transition::create($terrainTypes->get('Gg'), array('flat/bank-to-ice' => 2, 'grass/green' => 2)), 
        Transition::create($terrainTypes->get('Cv'), array('flat/bank-to-ice' => 2, 'grass/green' => 2)), 
        Transition::create($terrainTypes->get('Gs'), array('flat/bank-to-ice' => 2, 'grass/semi-dry-abrupt' => 1)), 
        Transition::create($terrainTypes->get('Hh'), array('flat/bank-to-ice' => 2, 'grass/semi-dry-abrupt' => 1)), 
        Transition::create($terrainTypes->get('Re'), array('flat/bank-to-ice' => 2)), 
        Transition::create($terrainTypes->get('Wwf'), array('water/ford' => 1)), 
        Transition::create($terrainTypes->get('Wo'), array('water/ocean-long-A01' => 1)), 
    ),
    
    //ford
    'Wwf' => array(
        Transition::create($terrainTypes->get('Cv'), array('flat/bank-to-ice' => 2, 'grass/green' => 2)), 
        Transition::create($terrainTypes->get('Gs'), array('grass/semi-dry-abrupt' => 1)),
        Transition::create($terrainTypes->get('Gg'), array('grass/green' => 3)), 
    ),
    
    //ford
    'Wo' => array(
        Transition::create($terrainTypes->get('Ww'), array('water/ocean-long-A15' => 1)), 
    ),
);
