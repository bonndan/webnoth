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
                InputArgument::REQUIRED,
                'Which is the outut file?'
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
        $dest     = $input->getArgument(self::DESTINATION_ARG);
        $cache    = $this->getCache();
        $terrain  = $cache->fetch('terrain');
        $map      = $this->getMap($mapName);
        
        $renderer = new \Webnoth\MapRenderer($terrain);
        $image    = $renderer->render($map);
        
        imagepng($image, $dest);
        
        //$destination = $input->getArgument(self::DESTINATION_ARG);
        $output->writeln('Renderer the map ' . $mapName . ' to ' . $dest);
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
