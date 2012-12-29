<?php
namespace Webnoth\Renderer;

require __DIR__ . '/bootstrap.php';

/**
 * ResourceTest
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class ResourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * @var Resource 
     */
    protected $resource;
    
    public function setUp()
    {
        $this->resource = new Resource($this->createImage());
    }
    
    public function tearDown()
    {
        $this->resource = null;
        parent::tearDown();
    }
    
    public function testConstructor()
    {
        $this->assertAttributeInternalType('resource', 'image', $this->resource);
        $this->assertAttributeEquals(72, 'width', $this->resource);
        $this->assertAttributeEquals(72, 'height', $this->resource);
    }
    
    public function testConstructorException()
    {
        $this->setExpectedException("\InvalidArgumentException");
        new Resource('string');
    }
    
    public function testGetImage()
    {
        $this->assertInternalType('resource', $this->resource->getImage());
    }
    
    /**
     * Ensures no errors occur.
     */
    public function testWrite()
    {
        $this->setExpectedException(null);
        $this->resource->write('test', 0, 0);
    }
    
    /**
     * Ensures the add operation works without exception
     */
    public function testAdd()
    {
        $this->setExpectedException(null);
        $added = new Resource($this->createImage(34, 34));
        $this->resource->add($added, 0, 0);
    }
    
    /**
     * creates an empty image
     * 
     * @param int $width
     * @param int $height
     * @return resource
     */
    protected function createImage($width = 72, $height = 72)
    {
        return imagecreatetruecolor($width, $height);
    }
}