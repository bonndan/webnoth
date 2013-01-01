<?php
namespace Webnoth\Renderer;

require __DIR__ . '/bootstrap.php';

/**
 * TerrainTest
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class TerrainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * @var Terrain 
     */
    protected $renderer;
    
    public function setUp()
    {
        $factory = new \Webnoth\Renderer\Resource\Factory(APPLICATION_PATH . '/data/terrain/');
        $this->renderer = new Terrain($this->createTerrainCollection(), $factory);
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
     * Ensures the plugins receive the terrain stack
     */
    public function testPluginHandlesStackOnRender()
    {
        $plugin = $this->createPluginMock();
        $this->renderer->addPlugin($plugin);
        
        $map = \Webnoth\WML\Element\Map::create();
        $map->addRawTileRow(array('Gg', 'Gg'));
        
        $plugin->expects($this->exactly(2))
            ->method('getTileTerrains');
        $this->renderer->render($map->getLayer('terrains'));
    }
    
    /**
     * Ensures custom images can be resolved
     */
    public function testRenderWithCustomTerrainImages()
    {
        $map = \Webnoth\WML\Element\Map::create();
        $map->addRawTileRow(array('sand/beach'));
        
        $this->setExpectedException(null);
        $this->renderer->render($map->getLayer('terrains'));
    }
    
    /**
     * Ensures custom images can be resolved
     */
    public function testRenderWithNotExistingCustomTerrainImage()
    {
        $map = \Webnoth\WML\Element\Map::create();
        $map->addRawTileRow(array('xxx/yyy'));
        
        $this->setExpectedException("\RuntimeException");
        $this->renderer->render($map->getLayer('terrains'));
    }
    
    /**
     * Ensures resouces can be used instead of terrain strings
     */
    public function testResourcesCanBeUsedInsteadOfTerrains()
    {
        $map = \Webnoth\WML\Element\Map::create();
        $map->addRawTileRow(array('Gg'));
        
        $this->renderer->addPlugin(new TestPlugin);
        $this->setExpectedException(null);
        $this->renderer->render($map->getLayer('terrains'));
    }
    
    /**
     * Ensures a resource is returned
     */
    public function testRenderReturnsResource()
    {
        $map = \Webnoth\WML\Element\Map::create();
        $resource = $this->renderer->render($map->getLayer('terrains'));
        $this->assertInstanceOf("\Webnoth\Renderer\Resource", $resource);
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
}


/**
 * Stub for testing
 */
class TestPlugin implements \Webnoth\Renderer\Plugin
{
    public function getTileTerrains(array &$tileStack, $column, $row)
    {
        $tileStack[] = imagecreatetruecolor(72, 72);
    }

    public function setLayer(\Webnoth\WML\Element\Layer $layer)
    {
        
    }

}