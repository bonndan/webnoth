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
}