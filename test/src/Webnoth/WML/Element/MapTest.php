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
        $this->assertEquals(3, $positions[1]);
        $this->assertEquals(2, $positions[3]);
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
        $row = array('11', '21', '31', '41');
        $this->map->addRawTileRow($row);
        
        $row = array('12', '22', '32', '42');
        $this->map->addRawTileRow($row);
        
        $this->assertEquals('11', $this->map->getTerrainAt(1, 1));
        $this->assertEquals('22', $this->map->getTerrainAt(2, 2));
        $this->assertEquals('32', $this->map->getTerrainAt(3, 2));
        $this->assertEquals('42', $this->map->getTerrainAt(4, 2));
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
        $row = array('11', '21', '31', '41');
        $this->map->addRawTileRow($row);
        $row = array('12', '22', '32', '42');
        $this->map->addRawTileRow($row);
        $row = array('13', '23', '33', '43');
        $this->map->addRawTileRow($row);
        
        $surrounding = $this->map->getSurroundingTerrains(2, 2);
        $this->assertInternalType('array', $surrounding);
        $expected = array(
            'ne' => '31',
            'se' => '32',
            's'  => '23',
            'sw' => '12',
            'nw' => '11',
            'n'  => '21'
        );
        $this->assertEquals($expected, $surrounding);
    }
    
    /**
     * Ensures the surrounding terrains are returned properly
     */
    public function testGetSurroundingTerrainsForOddCol()
    {
        $row = array('11', '21', '31', '41');
        $this->map->addRawTileRow($row);
        $row = array('12', '22', '32', '42');
        $this->map->addRawTileRow($row);
        $row = array('13', '23', '33', '43');
        $this->map->addRawTileRow($row);
        
        $surrounding = $this->map->getSurroundingTerrains(3, 2);
        $this->assertInternalType('array', $surrounding);
        $expected = array(
            'ne' => '42',
            'se' => '43',
            's'  => '33',
            'sw' => '23',
            'nw' => '22',
            'n'  => '31'
        );
        $this->assertEquals($expected, $surrounding);
    }
}