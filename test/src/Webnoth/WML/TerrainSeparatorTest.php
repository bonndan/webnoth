<?php
namespace Webnoth\WML;

require __DIR__ . '/bootstrap.php';

/**
 * SeparatorTest
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class TerrainSeparatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * @var TerrainSeparator 
     */
    protected $separator;
    
    /**
     * map mock
     * @var \Webnoth\WML\Element\Map 
     */
    protected $map;
    
    public function setUp()
    {
        $this->separator = new TerrainSeparator();
        $this->map = $this->getMockBuilder("\Webnoth\WML\Element\Map")
            ->disableOriginalConstructor()
            ->getMock();
        $this->separator->setMap($this->map);
    }
    
    public function tearDown()
    {
        $this->separator = null;
        parent::tearDown();
    }
    
    /**
     * Ensures the stack is left intact if not multi terrain occur
     */
    public function testStackIsNotModified()
    {
        $this->map->expects($this->once())
            ->method('setTerrainAt')
            ->with(1, 2, 'Gg');
        $this->map->expects($this->once())
            ->method('setOverlayAt')
            ->with(1, 2, null);

        $this->separator->processRawTerrain(1, 2, 'Gg');
    }
    
    /**
     * Ensures a multi terrain is split
     */
    public function testOverlayIsFound()
    {
        $this->map->expects($this->once())
            ->method('setOverlayAt')
            ->with(1, 2, '^Fsd');
        
        $this->separator->processRawTerrain(1, 2, 'Gg^Fsd');
    }
    
    /**
     * Ensures a starting position is saved
     */
    public function testSetStartingPosition()
    {
        $this->map->expects($this->once())
            ->method('setStartingPosition')
            ->with(1, 2, 3);
        
        $this->separator->processRawTerrain(1, 2, '3 Gg');
    }
}