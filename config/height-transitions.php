<?php
/**
 * preliminary terrain transition rules
 */
use Webnoth\Renderer\Transition;

/* @var $terrainTypes \Webnoth\WML\Collection\TerrainTypes */
return array(
    //grassland
    'water' => array(
        Transition::create($terrainTypes->get('flat'), array('flat/flat' => 3)),
    ),
);
