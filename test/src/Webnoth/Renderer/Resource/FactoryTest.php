<?php
namespace Webnoth\Renderer\Resource;

require __DIR__ . '/bootstrap.php';

/**
 * Tests the resource factory
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * @var Factory 
     */
    protected $factory;
    
    public function setUp()
    {
        $this->factory = new Factory();
        $this->factory->setImagePath(APPLICATION_PATH . '/data/terrain');
    }
    
    public function tearDown()
    {
        $this->factory = null;
        parent::tearDown();
    }
    
    /**
     * Ensures the image path is set properly.
     */
    public function testSetImagePath()
    {
        $this->assertAttributeEquals(APPLICATION_PATH . '/data/terrain', 'imagePath', $this->factory);
    }
    
    /**
     * Ensures the image path is validated
     */
    public function testSetImagePathException()
    {
        $this->setExpectedException("\InvalidArgumentException");
        $this->factory->setImagePath('nonsense');
    }
    
    /**
     * Ensures a resource is created for a map properly
     */
    public function testCreateForMap()
    {
        $map = $this->getMockBuilder("\Webnoth\WML\Element\Map")
            ->disableOriginalConstructor()
            ->getMock();
        $map->expects($this->once())
            ->method('getWidth')
            ->will($this->returnValue(10));
        $map->expects($this->once())
            ->method('getHeight')
            ->will($this->returnValue(20));
        
        $resource = Factory::createForMap($map);
        $this->assertInstanceOf("\Webnoth\Renderer\Resource", $resource);
        
        $this->assertEquals(Factory::TILE_WIDTH * 0.75 * 10 + Factory::TILE_WIDTH * 0.25, imagesx($resource->getImage()));
        $this->assertEquals(Factory::TILE_HEIGHT * 20 + Factory::TILE_HEIGHT/2, imagesy($resource->getImage()));
    }
}
