<?php
namespace Webnoth\WML\Collection;

require __DIR__ . '/bootstrap.php';

/**
 * TerrainTypesTest
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class TerrainTypesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system  under test
     * @var TerrainTypes 
     */
    protected $collection;
    
    public function setUp()
    {
        $gg = new \Webnoth\WML\Element\TerrainType();
        $gg->offsetSet('string', 'Gg');
        $gg->offsetSet('alias_of', 'Gt');
        
        $gb = new \Webnoth\WML\Element\TerrainType();
        $gb->offsetSet('string', 'Gx');
        $gb->offsetSet('alias_of', 'xx');
        $gb->offsetSet('default_base', 'Gt');
        
        $gt = new \Webnoth\WML\Element\TerrainType();
        $gt->offsetSet('string', 'Gt');
        
        $this->collection = new TerrainTypes(
            array(
                'Gg' => $gg,
                'Gx' => $gb,
                'Gt' => $gt
            )
        );
    }
    
    public function tearDown()
    {
        $this->collection = null;
        parent::tearDown();
    }
    
    public function testGetBaseTerrainOfBaseTerrain()
    {
        $this->assertEquals('Gt', $this->collection->getBaseTerrain('Gt'));
    }
    
    public function testGetBaseTerrainByAlias()
    {
        $this->assertEquals('Gt', $this->collection->getBaseTerrain('Gg'));
    }
    
    public function testGetBaseTerrainByDefaultBase()
    {
        $this->assertEquals('Gt', $this->collection->getBaseTerrain('Gx'));
    }
    
    public function testGetBaseTerrainException()
    {
        $this->setExpectedException("\RuntimeException");
        $this->collection->getBaseTerrain('Xyz');
    }
}