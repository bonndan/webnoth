<?php
namespace Webnoth\Renderer\Plugin;

require __DIR__ . '/bootstrap.php';

/**
 * SpecialTerrainTest
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class SpecialTerrainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * @var Debug 
     */
    protected $plugin;
    
    public function setUp()
    {
        $factory = new \Webnoth\Renderer\Resource\Factory(APPLICATION_PATH . '/data/terrain');
        $this->plugin = new SpecialTerrain($factory);
    }
    
    public function tearDown()
    {
        $this->plugin = null;
        parent::tearDown();
    }
    
    /**
     * Ensures the stack is left intact if not multi terrain occur
     */
    public function testGreatTree()
    {
        $stack = array('^Fet');
        $this->plugin->getTileTerrains($stack, 1, 1);
        
        $this->assertInstanceOf("\Webnoth\Renderer\Resource", $stack[0]);
    }
    
}