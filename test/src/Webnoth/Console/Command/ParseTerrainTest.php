<?php
namespace Webnoth\Console\Command;

require __DIR__ . '/bootstrap.php';

/**
 * ParseTerrainTest
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class ParseTerrainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system  under test
     * @var ParseTerrain 
     */
    protected $command;
    
    public function setUp()
    {
        $this->command = new ParseTerrain();
    }
    
    public function tearDown()
    {
        $this->command = null;
        parent::tearDown();
    }
    
    public function testReturnsAnIndexedCollection()
    {
        $input  = $this->getMock("\Symfony\Component\Console\Input\InputInterface");
        $output = $this->getMock("\Symfony\Component\Console\Output\OutputInterface");
      
        $input->expects($this->once())
            ->method('getArgument')
            ->will($this->returnValue(APPLICATION_PATH . '/data/terrain.cfg'));
        
        $this->command->run($input, $output);
        
        $cache = new \Doctrine\Common\Cache\FilesystemCache(CACHE_PATH);
        $collection = $cache->fetch('terrain');
        $this->assertInstanceOf("\Doctrine\Common\Collections\ArrayCollection", $collection);
        $this->assertInstanceOf("\Webnoth\WML\Collection\TerrainTypes", $collection);
        $this->assertTrue($collection->offsetExists('Ww'));
    }
}