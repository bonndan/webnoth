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
    
    public function setUp()
    {
        $this->separator = new Separator();
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
        $stack = array('Gg', 'Ww');
        $this->separator->getTileTerrains($stack);
        $this->assertEquals(array('Gg', 'Ww'), $stack);
    }
    
    /**
     * Ensures a multi terrain is split
     */
    public function testStackIsModified()
    {
        $stack = array('Gg^Fsd', 'Ww', 'Ww^Fsd');
        $this->separator->getTileTerrains($stack);
        $this->assertEquals(array('Gg', '^Fsd', 'Ww', 'Ww', '^Fsd'), $stack);
    }
}