<?php
/**
 * ParseMap
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
 * Map parser which caches its output.
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class ParseMap extends Command
{
    const FILE = 'file';
    const DESTINATION_ARG = 'dest';
    
    protected function configure()
    {
        $this
            ->setName('parse:map')
            ->setDescription('Parse a map .cfg file')
            ->addArgument(
                self::FILE,
                InputArgument::REQUIRED,
                'Which is the file to parse?'
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
        $file = $input->getArgument(self::FILE);
        $output->writeln("Analyzing the contents of $file.");
        
        $parser = new \Webnoth\WML\MapParser(new \Webnoth\WML\Lexer());
        $result = $parser->parse(file_get_contents($file));
        
        $cache = new \Doctrine\Common\Cache\FilesystemCache(APPLICATION_PATH . '/cache');
        $cache->setNamespace('map');
        
        $filename = basename($file, '.map');
        $cache->save($filename, $result);
        
        //$destination = $input->getArgument(self::DESTINATION_ARG);
        $output->writeln('Parsed the map ' . $filename .' successfully: ' . count($result->getTiles()). ' tiles.');
        $output->writeln('Cached the result under: ' . $filename);
    }
}
