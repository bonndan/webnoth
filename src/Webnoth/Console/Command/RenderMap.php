<?php
/**
 * RenderMap
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
namespace Webnoth\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Map renderer
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class RenderMap extends WebnothCommand
{
    /**
     * map argument (file name without suffix, must be cached)
     * @var string
     */
    const MAP = 'file';
    
    /**
     * destination file
     * @var string
     */
    const DESTINATION_ARG = 'dest';
    
    protected function configure()
    {
        $this
            ->setName('render:map')
            ->setDescription('Render a map')
            ->addArgument(
                self::MAP,
                InputArgument::REQUIRED,
                'Which is the (parsed) map to render?'
            )
            ->addArgument(
                self::DESTINATION_ARG,
                InputArgument::OPTIONAL,
                'Which is the output file?'
            )
        ;
    }

    /**
     * executes the parser
     * 
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mapName      = $input->getArgument(self::MAP);
        $cache        = $this->getCache();
        $terrainTypes = $this->getTerrainTypes();
        $map          = $this->getMap($mapName);
        $factory      = new \Webnoth\Renderer\Resource\Factory(APPLICATION_PATH . '/data/terrain');
        $renderer     = new \Webnoth\Renderer\Terrain($terrainTypes, $factory);
        
        //transition plugin
        $transitionPlugin = new \Webnoth\Renderer\Plugin\Transitions(
            $terrainTypes,
            include APPLICATION_PATH . '/config/terrain-transitions.php'
        );
        $renderer->addPlugin($transitionPlugin);
        
        //debug plugin
        $debugPlugin = new \Webnoth\Renderer\Plugin\Debug(\Webnoth\Renderer\Base::TILE_HEIGHT);
        $renderer->addPlugin($debugPlugin);
        
        $image    = $renderer->render($map->getLayer('terrains'));
        $dest     = $input->getArgument(self::DESTINATION_ARG);
        if ($dest == null) {
            $dest = $cache->getDirectory() . DIRECTORY_SEPARATOR . $mapName . '.png';
        }
        $output->writeln('Render the map ' . $mapName . ' to ' . $dest);
        imagepng($image->getImage(), $dest);
        
        /*
         * overlay
         */
        $renderer = new \Webnoth\Renderer\Overlay($terrainTypes, $factory);
        $renderer->addPlugin(new \Webnoth\Renderer\Plugin\SpecialTerrain($factory));
        $image    = $renderer->render($map->getLayer('overlays'));
        $dest     = $cache->getDirectory() . DIRECTORY_SEPARATOR . $mapName . '.overlays.png';
        $output->writeln('Render the overlay map ' . $mapName . ' to ' . $dest);
        imagepng($image->getImage(), $dest);
        
        /*
         * height map
         */
        $terrainTypes = include APPLICATION_PATH . '/config/height-terrains.php';
        $factory->setImagePath(APPLICATION_PATH . '/data/heights');
        $renderer    = new \Webnoth\Renderer\Heightmap($terrainTypes, $factory);
        $transitionPlugin = new \Webnoth\Renderer\Plugin\Transitions(
            $terrainTypes,
            include APPLICATION_PATH . '/config/height-transitions.php'
        );
        $renderer->addPlugin($transitionPlugin);
        $image    = $renderer->render($map->getLayer('heights'));
        $dest = $cache->getDirectory() . DIRECTORY_SEPARATOR . $mapName . '.heightmap.png';
        $output->writeln('Render the heightmap ' . $mapName . ' to ' . $dest);
        imagepng($image->getImage(), $dest);
    }
    
    /**
     * Fetches a map from the cache
     * 
     * @param string $mapName
     * @return \Webnoth\WML\Element\Map
     * @throws \RuntimeException
     */
    protected function getMap($mapName)
    {
        $cache    = $this->getCache();
        $cache->setNamespace('map');
        $map      = $cache->fetch($mapName);
        
        if (!$map instanceof \Webnoth\WML\Element\Map) {
            throw new \RuntimeException('Could not load the map from cache: ' . $mapName);
        }
        
        return $map;
    }
    
    /**
     * Returns the parsed terrain types from the cache.
     * 
     * @return TerrainTypes
     * @throws \RuntimeException
     */
    protected function getTerrainTypes()
    {
        $cache        = $this->getCache();
        $cache->setNamespace('');
        $terrainTypes = $cache->fetch('terrain');
        
        if ($terrainTypes === FALSE) {
            throw new \RuntimeException('Could not fetch the terrain types.');
        }
        
        return $terrainTypes;
    }
}
