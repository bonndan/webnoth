<?php
namespace Webnoth\Renderer;

require __DIR__ . '/bootstrap.php';

/**
 * Tests the transition class
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class TransitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system  under test
     * @var ParseTerrain 
     */
    protected $transition;
    
    /**
     * terrain the transition is responsible for
     * @var ParseTerrain 
     */
    protected $terrainType;
    
    public function setUp()
    {
        $this->terrainType = new \Webnoth\WML\Element\TerrainType();
        $this->terrainType->offsetSet('string', 'Gg');
        
        $this->transition = new Transition($this->terrainType);
    }
    
    public function tearDown()
    {
        $this->transition = null;
        parent::tearDown();
    }
    
    public function testCreate()
    {
        $res = Transition::create($this->terrainType, array('some/image'));
        $this->assertInstanceOf("Webnoth\Renderer\Transition", $res);
        $this->assertAttributeEquals(array('some/image'), 'imageBases', $res);
    }
}