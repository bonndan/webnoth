<?php
/**
 * preliminary terrain transition rules
 * @var array currentTerrain => checkAgainstTerrain[]
 */
return array(
    //grassland
    'Gt' => array(
        'Aa',
        array('Ss', false, false),
        array('Hh', false, false),
        'Mm',
        'Gs',
        'Ql',
        'Rr',
        'Ds',
        'Ww',
    ),
    //medium shallow water
    'Ww' => array(
        //'Mm',
        array('Wo', false, false),
        array('Gt', false, true),
        //'Aa',
        //'Ds',
        //array('Hh', false, false),
    /* 'Rr',
      'Ss',
      'Ql',
      'Ai' */
    ),
    //hills
    'Hh' => array(
        'Aa',
        'Ds',
        'Ql',
        'Gg',
    ),
    //ocean
    'Wo' => array(
        array('Gg', false, false),
        array('Hh', false, false),
        'Gs',
        'Ds',
        'Ss',
        'Ai',
        array('Ww', false, false),
    ),
    //regular dirt
    'Re' => array(
        array('Gg', false, false),
        array('Ww', 'Gg', false),
        array('Wo', 'Gg', false),
        'Rr'
    ),
    //road
    'Rr' => array(
        'Ds'
    ),
    //forest
    '^Fp' => array(
        'Hh',
        array('Ww', 'Gg'), //coast->grass
        'Ss'
    ),
    //Mountains
    'Mm' => array(
        'Ql'
    )
);
