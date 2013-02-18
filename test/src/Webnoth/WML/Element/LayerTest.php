<?php
namespace Webnoth\WML\Element;

require __DIR__ . '/bootstrap.php';

/**
 * LayerTest
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class LayerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * @var Layer
     */
    private $layer;
    
    public function setUp()
    {
        parent::setUp();
        $this->layer = new Layer($this->getCache()->fetch('terrain'));
    }
    
    public function tearDown()
    {
        $this->layer = null;
        parent::tearDown();
    }
    
    /**
     * Ensures the terrain type can be grabbed by coordinates
     */
    public function testGetTerrainAt()
    {
        $this->layer->setTerrainAt(1, 1, '11');
        $this->assertEquals('11', $this->layer->getTerrainAt(1, 1));
        $this->layer->setTerrainAt(1, 2, '12');
        $this->assertEquals('12', $this->layer->getTerrainAt(1, 2));
    }
    
    /**
     * Ensures off-map coords return null
     */
    public function testGetTerrainAtReturnsVoid()
    {
        $this->assertNull($this->layer->getTerrainAt(1, 2));
    }
    
    /**
     * Ensures the surrounding terrains are returned properly
     */
    public function testGetSurroundingTerrainsForEvenCol()
    {
        $this->prepareLayerForSurroundingTilesTest();
        $surrounding = $this->layer->getSurroundingTerrains(2, 2);
        $this->assertInstanceOf("\Webnoth\WML\Collection\TerrainTypes", $surrounding);
        $expected = array(
            'ne' => '31',
            'se' => '32',
            's'  => '23',
            'sw' => '12',
            'nw' => '11',
            'n'  => '21'
        );
        $this->assertCollectionEquals($expected, $surrounding);
    }
    
    /**
     * Ensures the surrounding terrains are returned properly
     */
    public function testGetSurroundingTerrainsForOddCol()
    {
        $this->prepareLayerForSurroundingTilesTest();
        $surrounding = $this->layer->getSurroundingTerrains(1, 1);
        
        $this->assertInstanceOf("\Webnoth\WML\Collection\TerrainTypes", $surrounding);
        $expected = array(
            'ne' => '21',
            'se' => '22',
            's'  => '12',
            'sw' => '02',
            'nw' => '01',
            'n'  => '10'
        );
        $this->assertCollectionEquals($expected, $surrounding);
    }
    
    /**
     * Sets required tiles.
     */
    protected function prepareLayerForSurroundingTilesTest()
    {
        $this->layer->setTerrainAt(0, 0, '00');
        $this->layer->setTerrainAt(1, 0, '10');
        $this->layer->setTerrainAt(2, 0, '20');
        $this->layer->setTerrainAt(3, 0, '30');
        
        $this->layer->setTerrainAt(0, 1, '01');
        $this->layer->setTerrainAt(1, 1, '11');
        $this->layer->setTerrainAt(2, 1, '21');
        $this->layer->setTerrainAt(3, 1, '31');
        
        $this->layer->setTerrainAt(0, 2, '02');
        $this->layer->setTerrainAt(1, 2, '12');
        $this->layer->setTerrainAt(2, 2, '22');
        $this->layer->setTerrainAt(3, 2, '32');
        
        $this->layer->setTerrainAt(0, 3, '03');
        $this->layer->setTerrainAt(1, 3, '13');
        $this->layer->setTerrainAt(2, 3, '23');
        $this->layer->setTerrainAt(3, 3, '33');
        
        $terrainTypes = $this->createFakeTerrainLookup($this->layer);
        $this->layer->setTerrainTypes($terrainTypes);
    }
    
    /**
     * assert a terraintype collection matches the given strings
     * @param type $expected
     * @param \Webnoth\WML\Collection\TerrainTypes $collection
     */
    protected function assertCollectionEquals($expected, \Webnoth\WML\Collection\TerrainTypes $collection)
    {
        $collection = $collection->toArray();
        foreach ($expected as $key => $value) {
            $this->assertArrayHasKey($key, $collection);
            $this->assertInstanceOf("\Webnoth\WML\Element\TerrainType", $collection[$key]);
            $this->assertEquals($value, $collection[$key]->getString());
        }
    }
    
    /**
     * creates a fake terrain lookup collection
     * 
     * @param Layer $map
     * @return \Webnoth\WML\Collection\TerrainTypes
     */
    protected function createFakeTerrainLookup(Layer $map)
    {
        $collection = new \Webnoth\WML\Collection\TerrainTypes();
        foreach ($map->getTiles() as $terrain) {
            $collection->set($terrain, new \Webnoth\WML\Element\TerrainType($terrain));
        }
        
        return $collection;
    }
    
    public function testGetTiles()
    {
        $this->prepareLayerForSurroundingTilesTest();
        
        $tiles = $this->layer->getTiles();
        $this->assertEquals(16, count($tiles));
    }
    
    /**
     * Ensures terrains can be set to the heightmap.
     */
    public function testSetTerrain()
    {
        $this->layer->setTerrainAt(1, 1, 'Gg');
        $this->assertAttributeEquals(array(1 => array(1 => 'Gg')), 'terrains', $this->layer);
    }
    
    /**
     * creates a cache instance
     * 
     * @return \Doctrine\Common\Cache\FilesystemCache
     */
    protected function getCache()
    {
        $cache = new \Doctrine\Common\Cache\FilesystemCache(CACHE_PATH);
        return $cache;
    }
}