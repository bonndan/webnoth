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
    
}
