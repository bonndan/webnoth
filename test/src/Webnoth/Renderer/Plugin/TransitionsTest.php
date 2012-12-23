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
        $terrainTypes = $this->createTerrainCollection();
        $this->plugin = new Transitions($terrainTypes);
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
     * Ensures merges transitions are returned as one tile
     */
    public function testGetMergedPartialTerrainTransitions()
    {
        $surrounding = $this->createSurroundingTerrainsResult();
        $result = $this->plugin->getTerrainTransitions($surrounding, 'Ww');
        $this->assertInternalType('array', $result);
        $this->assertEquals(1, count($result));
        $this->assertContains('water/coast-tile_sw_nw_n', $result, var_export($result, true));
    }
    
    /**
     * Ensures separated transitions are returned as separate tiles
     */
    public function testGetSeparatePartialTerrainTransitions()
    {
        $surrounding = $this->createSurroundingTerrainsResult();
        $result = $this->plugin->getTerrainTransitions($surrounding, 'Ww', false);
        $this->assertInternalType('array', $result);
        $this->assertEquals(3, count($result));
        $this->assertContains('water/coast-tile_sw', $result);
        $this->assertContains('water/coast-tile_nw', $result);
        $this->assertContains('water/coast-tile_n', $result);
    }
    
    /**
     * Ensures no transitions necessary
     */
    public function testNoTransitions()
    {
        $surrounding = array(
            'ne' => 'Ww',
            'se' => 'Wwt',
            's'  => 'Ww',
            'sw' => 'Wwt',
            'nw' => 'Ww',
            'n'  => 'Ww'
        );
        $result = $this->plugin->getTerrainTransitions($surrounding, 'Ww', false);
        $this->assertInternalType('array', $result);
        $this->assertEmpty($result, var_export($result, true));
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
        
        $this->assertEquals(1, count($stack));
        $this->assertContains('water/coast-tile_sw_nw_n', $stack, var_export($stack, true));
    }
    
    /**
     * fake a surrounding terrains reult
     * @return array
     */
    protected function createSurroundingTerrainsResult()
    {
        return array(
            'ne' => 'Ww',
            'se' => 'Ww',
            's'  => 'Ww',
            'sw' => 'Gg',
            'nw' => 'Gg',
            'n'  => 'Gg'
        );
    }
    
    /**
     * creates a fake terrain collection
     * @return \Webnoth\WML\Collection\TerrainTypes
     */
    protected function createTerrainCollection()
    {
        $grass = new \Webnoth\WML\Element\TerrainType();
        $grass->offsetSet('string', 'Gg');
        $grass->offsetSet('alias_of', 'Gt');
        $grass->offsetSet('symbol_image', 'grass/green');
        
        $grassBase = new \Webnoth\WML\Element\TerrainType();
        $grassBase->offsetSet('string', 'Gt');
        $grassBase->offsetSet('symbol_image', 'void/void');
        
        $water = new \Webnoth\WML\Element\TerrainType();
        $water->offsetSet('string', 'Ww');
        $water->offsetSet('symbol_image', 'water/coast-tile');
        
        $tropicalWater = new \Webnoth\WML\Element\TerrainType();
        $tropicalWater->offsetSet('string', 'Wwt');
        $tropicalWater->offsetSet('alias_of', 'Ww');
        $tropicalWater->offsetSet('symbol_image', 'water/coast-tropical-tile');
        
        $elements = array(
            $grass->getString()         => $grass,
            $grassBase->getString()     => $grassBase,
            $water->getString()         => $water,
            $tropicalWater->getString() => $tropicalWater,
        );
        return new \Webnoth\WML\Collection\TerrainTypes($elements);
    }
}