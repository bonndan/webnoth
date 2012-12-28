<?php
namespace Webnoth\Renderer;

require __DIR__ . '/bootstrap.php';

/**
 * OverlayTest
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class OverlayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * @var Overlay 
     */
    protected $renderer;
    
    public function setUp()
    {
        $this->renderer = new Overlay(APPLICATION_PATH . '/data/terrain/');
        $this->renderer->setTerrainTypes($this->getCache()->fetch('terrain'));
    }
    
    public function tearDown()
    {
        $this->renderer = null;
        parent::tearDown();
    }
    
    /**
     * Ensures custom images can be resolved
     */
    public function testRender()
    {
        $map = $this->createMap();
        $res = $this->renderer->render($map);
        $this->assertInternalType('resource', $res);
    }
    
    /**
     * Ensures resouces can be used instead of terrain strings
     */
    public function testResourcesCanBeUsedInsteadOfTerrains()
    {
        $map = new \Webnoth\WML\Element\Map();
        $map->addRawTileRow(array('Gg'));
        
        $this->renderer->addPlugin(new TestPlugin);
        $this->setExpectedException(null);
        $this->renderer->render($map);
    }
    
    /**
     * creates a map for testing
     * 
     * @return \Webnoth\WML\Element\Map
     */
    protected function createMap()
    {
        $map = new \Webnoth\WML\Element\Map();
        $map->addRawTileRow(array('Gg', 'Gg^Fds'));
        
        return $map;
    }
    
    /**
     * creates a cache instance
     * 
     * @return \Doctrine\Common\Cache\FilesystemCache
     */
    protected function getCache()
    {
        $cache = new \Doctrine\Common\Cache\FilesystemCache(APPLICATION_PATH . '/cache');
        return $cache;
    }
}
