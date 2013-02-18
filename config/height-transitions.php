<?php
/**
 * preliminary terrain transition rules
 */
use Webnoth\Renderer\Transition;

/* @var $terrainTypes \Webnoth\WML\Collection\TerrainTypes */
return array(
    //water to flat (grassland etc.)
    'water' => array(
        Transition::create($terrainTypes->get('flat'), array('flat/flat' => 3)),
        Transition::create($terrainTypes->get('hills'), array('flat/flat' => 3)),
    ),
    'hills' => array(
        Transition::create($terrainTypes->get('flat'), array('flat/flat' => 3)),
        Transition::create($terrainTypes->get('water'), array('flat/flat' => 3)),
    ),
);
