<?php
namespace Webnoth\Renderer\Plugin;

require __DIR__ . '/bootstrap.php';

/**
 * HeightProviderTest
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class HeightProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * @var HeightProvider 
     */
    protected $plugin;
    
    public function setUp()
    {
        $this->plugin = new HeightProvider(include APPLICATION_PATH . '/config/terrain-heightaliases.php');
        $map = new \Webnoth\WML\Element\Map();
        $map->addRawTileRow(array('Gg'));
        $this->plugin->setMap($map);
    }
    
    public function tearDown()
    {
        $this->plugin = null;
        parent::tearDown();
    }
    
    public function testConstructor()
    {
        $this->assertAttributeNotEmpty('aliases', $this->plugin);
    }
    
    public function testGetTileTerrainsClearsAll()
    {
        $stack = array('ab', 'cd');
        $this->plugin->getTileTerrains($stack, 0, 0);
        $this->assertEquals(1, count($stack));
    }
    
    public function testGetTileTerrainsReturnsDefault()
    {
        $stack = array('ab', 'cd');
        $this->plugin->getTileTerrains($stack, 0, 0);
        $this->assertEquals(HeightProvider::DEFAULT_ALIAS, $stack[0]);
    }
    
    public function testGetTileTerrains()
    {
        $stack = array('Hh');
        $this->plugin->getTileTerrains($stack, 0, 0);
        $this->assertEquals('hills/hills', $stack[0]);
    }
}