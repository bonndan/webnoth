<?php
/**
 * RenderMap
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
namespace Webnoth\Console\Command;

use Symfony\Component\Console\Command\Command;
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
class RenderMap extends Command
{
    const MAP = 'file';
    const DESTINATION_ARG = 'dest';
    
    protected function configure()
    {
        $this
            ->setName('render:map')
            ->setDescription('Render a map')
            ->addArgument(
                self::MAP,
                InputArgument::REQUIRED,
                'Which is the file to render?'
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
        $mapName  = $input->getArgument(self::MAP);
        
        $cache    = $this->getCache();
        $terrain  = $cache->fetch('terrain');
        $map      = $this->getMap($mapName);
        
        $renderer = new \Webnoth\MapRenderer($terrain);
        
        $renderer->addPlugin(new \Webnoth\Renderer\Plugin\Transitions($terrain));
        $renderer->addPlugin(new \Webnoth\Renderer\Plugin\Debug(\Webnoth\MapRenderer::TILE_HEIGHT));
        //$renderer->addPlugin(new \Webnoth\Renderer\Plugin\SpecialTerrain());
        
        $image    = $renderer->render($map);
        $dest     = $input->getArgument(self::DESTINATION_ARG);
        if ($dest == null) {
            $dest = APPLICATION_PATH . '/cache/' . $mapName . '.png';
        }
        $output->writeln('Render the map ' . $mapName . ' to ' . $dest);
        imagepng($image, $dest);
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
     * creates a cache instance
     * 
     * @return \Doctrine\Common\Cache\FilesystemCache
     */
    protected function getCache()
    {
        $cache = new \Doctrine\Common\Cache\FilesystemCache(APPLICATION_PATH . '/cache');
        return $cache;
    }
}
