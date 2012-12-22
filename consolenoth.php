<?php

/**
 * console app
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
require_once dirname(__FILE__) . '/vendor/autoload.php';


use Symfony\Component\Console\Application;

define ('APPLICATION_PATH', __DIR__);

$application = new Application();
$application->add(new Webnoth\Console\Command\ParseTerrain());
$application->add(new Webnoth\Console\Command\ParseMap());
$application->add(new Webnoth\Console\Command\RenderMap());
$application->run();