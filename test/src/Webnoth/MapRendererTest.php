<?php
namespace Webnoth;

require __DIR__ . '/bootstrap.php';

/**
 * MapRendererTest
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class MapRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system  under test
     * @var MapRenderer 
     */
    protected $renderer;
    
    public function setUp()
    {
        $this->renderer = new MapRenderer($this->createTerrainCollection());
    }
    
    public function tearDown()
    {
        $this->renderer = null;
        parent::tearDown();
    }
    
    /**
     * Ensures a plugin can be added
     */
    public function testAddPlugin()
    {
        $plugin = $this->createPluginMock();
        $this->renderer->addPlugin($plugin);
        $this->assertAttributeContains($plugin, 'plugins', $this->renderer);
    }
    
    /**
     * Ensures the plugins receive a reference to the map
     */
    public function testPluginReceivesMapOnRender()
    {
        $plugin = $this->createPluginMock();
        $this->renderer->addPlugin($plugin);
        
        $map = $this->createMap();
        $plugin->expects($this->once())
            ->method('setMap')
            ->with($map);
        $this->renderer->render($map);
    }
    
    /**
     * Ensures the plugins receive the terrain stack
     */
    public function testPluginRHandlesStackOnRender()
    {
        $plugin = $this->createPluginMock();
        $this->renderer->addPlugin($plugin);
        
        $map = $this->createMap();
        $plugin->expects($this->exactly(2))
            ->method('getTileTerrains');
        $this->renderer->render($map);
    }
    
    /**
     * Ensures a resource is returned
     */
    public function testRenderReturnsResource()
    {
        $map = $this->createMap();
        $resource = $this->renderer->render($map);
        $this->assertInternalType('resource', $resource);
    }
    
    /**
     * @return \Webnoth\Renderer\Plugin
     */
    protected function createPluginMock()
    {
        return $this->getMock("\Webnoth\Renderer\Plugin");
    }
    
    /**
     * creates a fake terrain collection
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    protected function createTerrainCollection()
    {
        $terrain = new \Webnoth\WML\Element\TerrainType();
        $terrain->offsetSet('string', 'Gg');
        $terrain->offsetSet('symbol_image', 'foreground');
        $elements = array(
            $terrain->getString() => $terrain
        );
        return new \Webnoth\WML\Collection\TerrainTypes($elements);
    }
    
    /**
     * creates a map for testing
     * 
     * @return \Webnoth\WML\Element\Map
     */
    protected function createMap()
    {
        $map = new \Webnoth\WML\Element\Map();
        $map->addRawTileRow(array('Gg', 'Gg'));
        
        return $map;
    }
}