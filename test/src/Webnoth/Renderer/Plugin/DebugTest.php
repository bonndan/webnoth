<?php
namespace Webnoth\Renderer\Plugin;

require __DIR__ . '/bootstrap.php';

/**
 * DebugTest
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class DebugTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * @var Debug 
     */
    protected $plugin;
    
    public function setUp()
    {
        $this->plugin = new Debug(72);
    }
    
    public function tearDown()
    {
        $this->plugin = null;
        parent::tearDown();
    }
    
    /**
     * Ensures the stack is left intact if not multi terrain occur
     */
    public function testStampIsAddedModified()
    {
        $map = new \Webnoth\WML\Element\Map();
        $map->addRawTileRow(array('Gg'));
        
        $this->plugin->setMap($map);
        $stack = array('Gg', 'Ww');
        $this->plugin->getTileTerrains($stack, 0, 0);
        $this->assertEquals(3, count($stack));
        $this->assertInternalType('resource', $stack[2]);
    }
    
}