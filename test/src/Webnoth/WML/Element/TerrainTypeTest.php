<?php
namespace Webnoth\WML\Element;

require __DIR__ . '/bootstrap.php';

/**
 * TerrainTypeTest
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class TerrainTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * @var TerrainType
     */
    private $terrain;
    
    public function setUp()
    {
        parent::setUp();
        $this->terrain = new TerrainType();
    }
    
    public function tearDown()
    {
        $this->terrain = null;
        parent::tearDown();
    }
    
    /**
     * 
     */
    public function testGetString()
    {
        $this->terrain->offsetSet('string', '^Fds');
        $this->assertEquals('^Fds', $this->terrain->getString());
    }
    
    /**
     * 
     */
    public function testIsNotHidden()
    {
        $this->assertFalse($this->terrain->isHidden());
    }
    
    /**
     * 
     */
    public function testIsHidden()
    {
        $this->terrain->offsetSet('hidden', 'yes');
        $this->assertTrue($this->terrain->isHidden());
    }
    
    /**
     * 
     */
    public function testGetSymbolImage()
    {
        $this->terrain->offsetSet('symbol_image', 'water/ocean-grey-tile');
        $this->assertEquals('water/ocean-grey-tile', $this->terrain->getSymbolImage());
    }
    
    /**
     * 
     */
    public function testGetSymbolImageFromEditorImage()
    {
        $this->terrain->offsetSet('editor_image', 'water/editor-image');
        $this->terrain->offsetSet('symbol_image', 'water/ocean-grey-tile');
        $this->assertEquals('water/editor-image', $this->terrain->getSymbolImage());
    }
    
    public function testGetBaseTerrainWithoutParent()
    {
        $this->terrain->offsetSet('string', 'Gg');
        $this->assertEquals('Gg', $this->terrain->getBaseTerrain());
    }
    
    public function testGetBaseTerrainWithDefaultBase()
    {
        $this->terrain->offsetSet('string', 'Gg');
        $this->terrain->offsetSet('default_base', 'Gt');
        $this->assertEquals('Gt', $this->terrain->getBaseTerrain());
    }
    
    public function testGetBaseTerrainWithAlias()
    {
        $this->terrain->offsetSet('string', 'Gg');
        $this->terrain->offsetSet(TerrainType::ATTR_ALIASOF, 'Gt');
        $this->assertEquals('Gt', $this->terrain->getBaseTerrain());
    }
}