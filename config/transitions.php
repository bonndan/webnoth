<?php
/**
 * preliminary terrain transition rules
 */
use Webnoth\Renderer\Transition;

/* @var $terrainTypes \Webnoth\WML\Collection\TerrainTypes */

return array(
    //grassland
    'Gg' => array(
        Transition::create($terrainTypes->get('Wo'), array('sand/beach' => 3)), 
        Transition::create($terrainTypes->get('Xt'), array('void/void' => 1)), 
    ),
    'Ww' => array(
        Transition::create($terrainTypes->get('Gt'), array('sand/beach' => 3)), 
    ),
);
