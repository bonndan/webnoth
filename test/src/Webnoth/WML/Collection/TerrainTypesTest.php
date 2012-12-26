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
        $gg->offsetSet('aliasof', 'Gt');
        
        $gb = new \Webnoth\WML\Element\TerrainType();
        $gb->offsetSet('string', 'Gx');
        $gb->offsetSet('aliasof', 'xx');
        $gb->offsetSet('default_base', 'Gt');
        
        $gt = new \Webnoth\WML\Element\TerrainType();
        $gt->offsetSet('string', 'Gt');
        
        $parentHidden = new \Webnoth\WML\Element\TerrainType();
        $parentHidden->offsetSet('string', 'Ph');
        $parentHidden->offsetSet('aliasof', 'Gh');
        
        $hidden = new \Webnoth\WML\Element\TerrainType();
        $hidden->offsetSet('string', 'Gh');
        $hidden->offsetSet('hidden', 'yes');
        
        $this->collection = new TerrainTypes(
            array(
                'Gg' => $gg,
                'Gx' => $gb,
                'Gt' => $gt,
                'Ph' => $parentHidden,
                'Gh' => $hidden,
            )
        );
    }
    
    public function tearDown()
    {
        $this->collection = null;
        parent::tearDown();
    }
    
    public function testGetBaseTerrainOfTerrainWithCaret()
    {
        $this->assertEquals('Gt', $this->collection->getBaseTerrain('Gg^Fsd'));
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
    
    /**
     * ensures alias is not used of hidden
     */
    public function testGetBaseWhenAliasHidden()
    {
        $this->assertEquals('Ph', $this->collection->getBaseTerrain('Ph'));
    }
    
    public function testGetBaseTerrainException()
    {
        $this->setExpectedException("\RuntimeException");
        $this->collection->getBaseTerrain('Xyz');
    }
}