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
        $this->factory = new Factory(APPLICATION_PATH . '/data/terrain');
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
    public function testCreateForLayer()
    {
        $layer = $this->getMockBuilder("\Webnoth\WML\Element\Layer")
            ->disableOriginalConstructor()
            ->getMock();
        $layer->expects($this->once())
            ->method('getWidth')
            ->will($this->returnValue(10));
        $layer->expects($this->once())
            ->method('getRowCount')
            ->will($this->returnValue(20));
        
        $resource = Factory::createForLayer($layer);
        $this->assertInstanceOf("\Webnoth\Renderer\Resource", $resource);
        
        $this->assertEquals(Factory::TILE_WIDTH * 0.75 * 10 + Factory::TILE_WIDTH * 0.25, imagesx($resource->getImage()));
        $this->assertEquals(Factory::TILE_HEIGHT * 20 + Factory::TILE_HEIGHT/2, imagesy($resource->getImage()));
    }
    
    public function testCreateFromPng()
    {
        $resource = $this->factory->createFromPng('grass/green');
        $this->assertInstanceOf("\Webnoth\Renderer\Resource", $resource);
    }
    
    public function testCreateFromPngException()
    {
        $this->setExpectedException("\RuntimeException");
        $this->factory->createFromPng('grass/xxx');
    }
}
