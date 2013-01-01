<?php
namespace Webnoth\WML\Element;

require __DIR__ . '/bootstrap.php';

/**
 * MapTest
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class MapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * @var Map
     */
    private $map;
    
    public function setUp()
    {
        parent::setUp();
        $this->map = new Map(
            $this->createLayer(),
            $this->createLayer(),
            $this->createLayer()
        );
        $this->map->setTerrainSeparator(new \Webnoth\WML\TerrainSeparator($this->map, array()));
    }
    
    public function tearDown()
    {
        $this->map = null;
        parent::tearDown();
    }
    
    /**
     * 
     */
    public function testAddRawTileRow()
    {
        $this->map->addRawTileRow(array('Gg', 'Gg'));
        $this->map->addRawTileRow(array('Re', 'Re'));
        
        $this->assertEquals(2, $this->map->getWidth());
        $this->assertEquals(2, $this->map->getHeight());
    }
    
    /**
     * Ensures the correct width is returned
     */
    public function testGetWidth()
    {
        $row = array('Gg', 'Gg');
        $this->map->addRawTileRow($row);
        $this->assertEquals(2, $this->map->getWidth());
    }
    
    /**
     * Ensures the correct width is returned
     */
    public function testAddStartingPositions()
    {
        $row = array('Gg', '3 Ke', 'Gg', '2 Gg');
        $this->map->addRawTileRow($row);
        $this->assertEquals(2, count($this->map->getStartingPositions()));
        
        $positions = $this->map->getStartingPositions();
        $this->assertEquals(array(0,1), $positions[3]);
        $this->assertEquals(array(0,3), $positions[2]);
    }
    
    /**
     * ensures all consecutive calls must have same width
     */
    public function testAddRawTileRowException()
    {
        $this->map->addRawTileRow(array('Gg', 'Gg'));
        $this->setExpectedException("\RuntimeException");
        $this->map->addRawTileRow(array('Gg', 'Gg', 'Gg'));
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
     * @param \Webnoth\WML\Element\Map $map
     * @return \Webnoth\WML\Collection\TerrainTypes
     */
    protected function createFakeTerrainLookup(Map $map)
    {
        $collection = new \Webnoth\WML\Collection\TerrainTypes();
        foreach ($map->getTiles() as $terrain) {
            $collection->set($terrain, new \Webnoth\WML\Element\TerrainType($terrain));
        }
        
        return $collection;
    }
    
    /**
     * Ensures the number of rows is returned
     */
    public function testGetHeight()
    {
        $row = array('00', '10', '20', '30');
        $this->map->addRawTileRow($row);
        $row = array('01', '11', '21', '31');
        $this->map->addRawTileRow($row);
        
        $this->assertEquals(2, $this->map->getHeight());
    }
    
    public function _testGetTiles()
    {
        $row = array('00', '10', '20', '30');
        $this->map->addRawTileRow($row);
        $row2 = array('01', '11', '21', '31');
        $this->map->addRawTileRow($row2);
        
        $tiles = $this->map->getTiles();
        $this->assertEquals(8, count($tiles));
        $this->assertEquals(array_merge($row,$row2), $tiles);
    }
    
    /**
     * Ensures terrains can be set to the heightmap.
     */
    public function testSetTerrain()
    {
        $this->map->setTerrainAt(1, 1, 'Gg');
        $this->assertEquals('Gg', $this->map->getLayer(Map::LAYER_TERRAIN)->getTerrainAt(1, 1));
    }
    
    /**
     * Ensures heights can be set to the heightmap.
     */
    public function testSetHeight()
    {
        $this->map->setHeightAt(1, 2, 3);
        $this->assertEquals(3, $this->map->getLayer(Map::LAYER_HEIGHTS)->getTerrainAt(1, 2));
    }
    
    /**
     * Ensures overlays can be set to the heightmap.
     */
    public function testSetOverlay()
    {
        $this->map->setOverlayAt(2, 1, '^Fsd');
        $this->assertEquals('^Fsd', $this->map->getLayer(Map::LAYER_OVERLAYS)->getTerrainAt(2, 1));
    }
    
    /**
     * Ensures starting postions are stored properly
     */
    public function testSetStartingPostion()
    {
        $this->map->setStartingPosition(12, 13, 3);
        $this->assertAttributeEquals(array(3 => array(13, 12)), 'startingPositions', $this->map);
    }
    
    /**
     * Ensures an exception is throw if the layer id is wrong
     */
    public function testGetLayerException()
    {
        $this->setExpectedException("\InvalidArgumentException");
        $this->map->getLayer('test');
    }

    public function testCreate()
    {
        $map = Map::create();
        $this->assertInstanceOf("\Webnoth\WML\Element\Map", $map);
    }

    /**
     * Creates a fake layer
     * 
     * @return \Webnoth\WML\Element\Layer
     */
    protected function createLayer()
    {
        return new Layer();
    }
}