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
        $this->map = new Map();
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
        $row = array('Gg', 'Gg');
        $this->map->addRawTileRow($row);
        $this->assertEquals($row, $this->map->getTiles());
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
     * ensures all added tiles are stored
     */
    public function testAddRawTileRowTwice()
    {
        $this->map->addRawTileRow(array('Gg', 'Gg'));
        $this->map->addRawTileRow(array('Re', 'Re'));
        
        $this->assertEquals(4, count($this->map->getTiles()));
        $this->assertEquals(array('Gg', 'Gg', 'Re', 'Re'), $this->map->getTiles());
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
     * Ensures the terrain type can be grabbed by coordinates
     */
    public function testGetTerrainAt()
    {
        $row = array('00', '10', '20', '30');
        $this->map->addRawTileRow($row);
        $row = array('01', '11', '21', '31');
        $this->map->addRawTileRow($row);
        $row = array('02', '12', '22', '32');
        $this->map->addRawTileRow($row);
        $row = array('03', '13', '23', '33');
        $this->map->addRawTileRow($row);
        
        $this->assertEquals('11', $this->map->getTerrainAt(1, 1));
        $this->assertEquals('22', $this->map->getTerrainAt(2, 2));
        $this->assertEquals('32', $this->map->getTerrainAt(3, 2));
        $this->assertEquals('Xv', $this->map->getTerrainAt(4, 2));
    }
    
    /**
     * Ensures off-map coords return a void terrain type
     */
    public function testGetTerrainAtReturnsVoid()
    {
        $row = array('11', '21', '31', '41');
        $this->map->addRawTileRow($row);
        
        $this->assertEquals(TerrainType::VOID, $this->map->getTerrainAt(40, 20));
    }
    
    /**
     * Ensures the surrounding terrains are returned properly
     */
    public function testGetSurroundingTerrainsForEvenCol()
    {
        $row = array('00', '10', '20', '30');
        $this->map->addRawTileRow($row);
        $row = array('01', '11', '21', '31');
        $this->map->addRawTileRow($row);
        $row = array('02', '12', '22', '32');
        $this->map->addRawTileRow($row);
        $row = array('03', '13', '23', '33');
        $this->map->addRawTileRow($row);
        
        $surrounding = $this->map->getSurroundingTerrains(2, 2, $this->createFakeTerrainLookup($this->map));
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
        $row = array('00', '10', '20', '30');
        $this->map->addRawTileRow($row);
        $row = array('01', '11', '21', '31');
        $this->map->addRawTileRow($row);
        $row = array('02', '12', '22', '32');
        $this->map->addRawTileRow($row);
        $row = array('03', '13', '23', '33');
        $this->map->addRawTileRow($row);
        $this->map->getTiles();
        $surrounding = $this->map->getSurroundingTerrains(1, 1, $this->createFakeTerrainLookup($this->map));
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
    
    public function testGetTiles()
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
     * Ensures the overlay tiles are returned properly.
     */
    public function testGetOverlayTiles()
    {
        $row = array('00^Fsd', '10', '20^Fsd', '30');
        $this->map->addRawTileRow($row);
        $row2 = array('01', '11', '21', '31');
        $this->map->addRawTileRow($row2);
        
        $tiles = $this->map->getOverlayTiles();
        $this->assertEquals(8, count($tiles));
        $this->assertEquals('^Fsd', $tiles[0]);
        $this->assertEquals(null, $tiles[1]);
        $this->assertEquals('^Fsd', $tiles[2]);
    }
    
    /**
     * Ensures terrains can be set to the heightmap.
     */
    public function testSetTerrain()
    {
        $this->map->setTerrainAt(1, 1, 'Gg');
        $this->assertAttributeEquals(array(1 => array(1 => 'Gg')), 'terrains', $this->map);
    }
    
    /**
     * Ensures heights can be set to the heightmap.
     */
    public function testSetHeight()
    {
        $this->map->setHeightAt(1, 1, 2);
        $this->assertEquals(2, $this->map->getHeightAt(1, 1));
    }
    
    /**
     * Ensures overlays can be set to the heightmap.
     */
    public function testSetOverlay()
    {
        $this->map->setOverlayAt(2, 1, '^Fsd');
        $this->assertEquals( '^Fsd', $this->map->getOverlayAt(2, 1));
    }
    
    /**
     * Ensures starting postions are stored properly
     */
    public function testSetStartingPostion()
    {
        $this->map->setStartingPosition(12, 13, 3);
        $this->assertAttributeEquals(array(3 => array(13, 12)), 'startingPositions', $this->map);
    }
}