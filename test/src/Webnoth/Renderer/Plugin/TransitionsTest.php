<?php
namespace Webnoth\Renderer\Plugin;

require __DIR__ . '/bootstrap.php';

/**
 * Tests the transitions plugin
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class TransitionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * @var Transitions 
     */
    protected $plugin;
    
    public function setUp()
    {
        $terrainTypes = $this->getTerrainCollection();
        $this->plugin = new Transitions(
            $terrainTypes,
            include APPLICATION_PATH . '/config/terrain-transitions.php'
        );
    }
    
    public function tearDown()
    {
        $this->plugin = null;
        parent::tearDown();
    }
    
    /**
     * Ensure the map is injected
     */
    public function testSetMap()
    {
        $map = new \Webnoth\WML\Element\Map();
        $this->plugin->setMap($map);
        $this->assertAttributeEquals($map, 'map', $this->plugin);
    }
    
    /**
     * 
     */
    public function testGetTileTerrains()
    {
        $map = $this->getMock("\Webnoth\WML\Element\Map");
        $map->expects($this->once())
            ->method('getSurroundingTerrains')
            ->with(2, 2)
            ->will($this->returnValue($this->createSurroundingTerrainsResult()));
        $map->expects($this->once())
            ->method('getTerrainAt')
            ->with(2, 2)
            ->will($this->returnValue('Ww'));
        
        $this->plugin->setMap($map);
        
        $stack = array();
        $this->plugin->getTileTerrains($stack, 2, 2);
        
        $this->assertGreaterThan(0, count($stack), var_export($stack, true));
        $this->assertContains('flat/bank-to-ice-n', $stack, var_export($stack, true));
        $this->assertContains('flat/bank-to-ice-sw-nw', $stack, var_export($stack, true));
    }
    
    /**
     * a specific water to semi-dry grass test
     */
    public function testWwGsTransition()
    {
        $directions = array(
                'n'  => 'Gs',
                'ne' => 'Gs',
                'se' => 'Gs',
                's'  => 'Ww',
                'sw' => 'Ww',
                'nw' => 'Gs',
            );
        
        $map = $this->getMock("\Webnoth\WML\Element\Map");
        $map->expects($this->once())
            ->method('getSurroundingTerrains')
            ->with(2, 2)
            ->will($this->returnValue($this->createSurroundingTerrainsResult($directions)));
        $map->expects($this->once())
            ->method('getTerrainAt')
            ->with(2, 2)
            ->will($this->returnValue('Ww'));
        
        $this->plugin->setMap($map);
        
        $stack = array();
        $this->plugin->getTileTerrains($stack, 2, 2);
        
        $this->assertGreaterThan(0, count($stack), var_export($stack, true));
        $this->assertContains('flat/bank-to-ice-n-ne', $stack, var_export($stack, true));
        $this->assertContains('flat/bank-to-ice-se', $stack, var_export($stack, true));
        $this->assertContains('flat/bank-to-ice-nw', $stack, var_export($stack, true));
    }
    
    /**
     * fake a surrounding terrains reult
     * @return array
     */
    protected function createSurroundingTerrainsResult(array $directions = null)
    {
        $terrains = $this->getTerrainCollection();
        
        if ($directions === null) {
            $directions = array(
                'n'  => 'Gg',
                'ne' => 'Ww',
                'se' => 'Ww',
                's'  => 'Ww',
                'sw' => 'Gg',
                'nw' => 'Gg',
            );
        }
        
        $data = array();
        foreach ($directions as $dir => $terrain) {
            $data[$dir] = $terrains->get($terrain);
        }
        return new \Webnoth\WML\Collection\TerrainTypes($data);
    }
    
    /**
     * creates a fake terrain collection
     * @return \Webnoth\WML\Collection\TerrainTypes
     */
    protected function getTerrainCollection()
    {
        $cache = new \Doctrine\Common\Cache\FilesystemCache(APPLICATION_PATH . '/cache');
        return $cache->fetch('terrain');
    }
}