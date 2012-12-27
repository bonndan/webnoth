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
        
        $this->assertEquals(2, count($stack), var_export($stack, true));
        $this->assertContains('sand/beach-n', $stack, var_export($stack, true));
        $this->assertContains('sand/beach-sw-nw', $stack, var_export($stack, true));
    }
    
    /**
     * fake a surrounding terrains reult
     * @return array
     */
    protected function createSurroundingTerrainsResult()
    {
        return array(
            'n'  => 'Gg',
            'ne' => 'Ww',
            'se' => 'Ww',
            's'  => 'Ww',
            'sw' => 'Gg',
            'nw' => 'Gg',
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
        $grass->offsetSet('aliasof', 'Gt');
        $grass->offsetSet('symbol_image', 'grass/green');
        
        $grassBase = new \Webnoth\WML\Element\TerrainType();
        $grassBase->offsetSet('string', 'Gt');
        $grassBase->offsetSet('symbol_image', 'xxx');
        $grassBase->offsetSet('hidden', 'yes');
        
        $semiDry = new \Webnoth\WML\Element\TerrainType();
        $semiDry->offsetSet('string', 'Gs');
        $semiDry->offsetSet('symbol_image', 'grass/semi-dry');
        $semiDry->offsetSet('aliasof', 'Gt');
        
        $aa = new \Webnoth\WML\Element\TerrainType();
        $aa->offsetSet('string', 'Aa');
        $aa->offsetSet('symbol_image', 'grass/xxx');
        
        $desert = new \Webnoth\WML\Element\TerrainType();
        $desert->offsetSet('string', 'Ds');
        $desert->offsetSet('symbol_image', 'sand/desert-tile');
        
        $water = new \Webnoth\WML\Element\TerrainType();
        $water->offsetSet('string', 'Ww');
        $water->offsetSet('symbol_image', 'water/coast-tile');
        
        $ocean = new \Webnoth\WML\Element\TerrainType();
        $ocean->offsetSet('string', 'Wo');
        $ocean->offsetSet('symbol_image', 'water/ocean-tile');
        
        $mountains = new \Webnoth\WML\Element\TerrainType();
        $mountains->offsetSet('string', 'Mm');
        $mountains->offsetSet('symbol_image', 'xxx');
        
        $hills = new \Webnoth\WML\Element\TerrainType();
        $hills->offsetSet('string', 'Hh');
        $hills->offsetSet('symbol_image', 'xxx');
        
        $void = new \Webnoth\WML\Element\TerrainType();
        $void->offsetSet('string', 'Xv');
        $void->offsetSet('symbol_image', 'void/void');
        $void->offsetSet('aliasof', 'Xt');
        
        $voidT = new \Webnoth\WML\Element\TerrainType();
        $voidT->offsetSet('string', 'Xt');
        $voidT->offsetSet('symbol_image', 'xxx');
        
        $tropicalWater = new \Webnoth\WML\Element\TerrainType();
        $tropicalWater->offsetSet('string', 'Wwt');
        $tropicalWater->offsetSet('aliasof', 'Ww');
        $tropicalWater->offsetSet('symbol_image', 'water/coast-tropical-tile');
        
        $elements = array(
            $aa->getString()            => $aa,
            $grass->getString()         => $grass,
            $semiDry->getString()            => $semiDry,
            $grassBase->getString()     => $grassBase,
            $water->getString()         => $water,
            $ocean->getString()         => $ocean,
            $tropicalWater->getString() => $tropicalWater,
            $mountains->getString()     => $mountains,
            $desert->getString()        => $desert,
            $hills->getString()         => $hills,
            $void->getString()          => $void,
            $voidT->getString()         => $voidT,
        );
        return new \Webnoth\WML\Collection\TerrainTypes($elements);
    }
}