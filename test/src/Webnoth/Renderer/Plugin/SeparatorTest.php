<?php
namespace Webnoth\Renderer\Plugin;

require __DIR__ . '/bootstrap.php';

/**
 * SeparatorTest
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class SeparatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * @var Separator 
     */
    protected $separator;
    
    /**
     * map mock
     * @var \Webnoth\WML\Element\Map 
     */
    protected $map;
    
    public function setUp()
    {
        $this->separator = new Separator();
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
            ->method('setHeightAt');
        $this->map->expects($this->once())
            ->method('setTerrainAt');
        $this->map->expects($this->once())
            ->method('setOverlayAt');
        $stack = array('Gg', 'Ww');
        $this->separator->getTileTerrains($stack, 0, 0);
        $this->assertEquals(array('Gg', 'Ww'), $stack);
    }
    
    /**
     * Ensures a multi terrain is split
     */
    public function testStackIsModified()
    {
        $this->map->expects($this->once())
            ->method('setHeightAt');
        $this->map->expects($this->once())
            ->method('setTerrainAt');
        $this->map->expects($this->once())
            ->method('setOverlayAt');
        
        $stack = array('Gg^Fsd', 'anything', 'else');
        $this->separator->getTileTerrains($stack, 0, 0);
        $this->assertEquals(array('Gg', 'anything', 'else'), $stack);
    }
}