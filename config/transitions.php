<?php
/**
 * preliminary terrain transition rules
 * @var array currentTerrain => checkAgainstTerrain[]
 */
return array(
    //grassland
    'Gt' => array(
        'Aa',
        array('Ss', null, false),
        array('Hh', null, false),
        'Mm',
        'Gs',
        'Ql',
        'Rr',
        'Ds',
        array('Ww', 'sand/beach', true),
        array('Xt', 'void/void', false),
    ),
    //medium shallow water
    'Ww' => array(
        //'Mm',
        array('Wo', 'water/ocean-A01', true),
        array('Gt', 'sand/beach', true),
        //'Aa',
        //'Ds',
        //array('Hh', false, false),
    /* 'Rr',
      'Ss',
      'Ql',
      'Ai' */
        array('Xt', 'void/void', false),
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
        array('Gt', 'sand/beach', false),
        array('Hh', null, false),
        'Gs',
        'Ds',
        'Ss',
        'Ai',
        array('Ww', 'water/ocean-A02', false),
        array('Xt', 'void/void', false),
    ),
    //regular dirt
    'Re' => array(
        array('Gt', null, false),
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
