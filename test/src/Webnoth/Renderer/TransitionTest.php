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
     * @var Transition 
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

        $this->transition = Transition::create($this->terrainType, array('some/image' => 3));
    }

    public function tearDown()
    {
        $this->transition = null;
        parent::tearDown();
    }

    public function testCreate()
    {
        $this->assertInstanceOf("Webnoth\Renderer\Transition", $this->transition);
        $this->assertAttributeEquals(array('some/image' => 3), 'imageBases', $this->transition);
    }

    /**
     * 
     */
    public function testGetTransitionImagesMerged()
    {
        $res = $this->transition->getTransitionImages($this->createSurroundingTerrains());
        $this->assertInternalType('array', $res);
        $this->assertContains('some/image-n', $res);
        $this->assertContains('some/image-sw-nw', $res);
    }
    
    /**
     * 
     */
    public function testGetTransitionImagesMergedWithMaxExceeded()
    {
        $this->transition = Transition::create($this->terrainType, array('some/image' => 2));
        
        $data = array(
            'n' => 'Ww',
            'ne' => 'Ww',
            'se' => 'Ww',
            's' => 'Gg',
            'sw' => 'Gg',
            'nw' => 'Gg',
        );
        
        
        $res = $this->transition->getTransitionImages($this->createSurroundingTerrains($data));
        $this->assertInternalType('array', $res);
        $this->assertContains('some/image-s-sw', $res);
        $this->assertContains('some/image-nw', $res);
    }

    /**
     * 
     */
    public function testGetTransitionImagesSeparated()
    {
        $this->transition = Transition::create($this->terrainType, array('some/image' => 1));
        $res = $this->transition->getTransitionImages($this->createSurroundingTerrains());
        $this->assertInternalType('array', $res);
        $this->assertContains('some/image-n', $res);
        $this->assertContains('some/image-sw', $res);
        $this->assertContains('some/image-nw', $res);
    }

    /**
     * Creates a surrounding terrains collection
     * 
     * @param array $data
     * @return \Webnoth\WML\Collection\TerrainTypes
     */
    protected function createSurroundingTerrains(array $data = null)
    {
        if ($data === null) {
            $data = array(
                'n' => 'Gg',
                'ne' => 'Ww',
                'se' => 'Ww',
                's' => 'Ww',
                'sw' => 'Gg',
                'nw' => 'Gg',
            );
        }

        $collection = new \Webnoth\WML\Collection\TerrainTypes();
        foreach ($data as $key => $value) {
            $collection->set($key, new \Webnoth\WML\Element\TerrainType($value));
        }
        return $collection;
    }

}