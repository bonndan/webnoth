<?php
namespace Webnoth\WML;

require __DIR__ . '/bootstrap.php';

/**
 * ParserTest
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * @var Parser 
     */
    protected $parser;
    
    public function setUp()
    {
        $this->parser = new Parser(new Lexer());
    }
    
    public function tearDown()
    {
        $this->parser = null;
        parent::tearDown();
    }
    
    public function testParserReturnsEmptyCollection()
    {
        $result = $this->parser->parse('');
        $this->assertInstanceOf("\Doctrine\Common\Collections\ArrayCollection", $result);
    }
    
    public function testParserReturnsCollectionWithAllElements()
    {
        $result = $this->parser->parse($this->getInput());
        $this->assertInstanceOf("\Doctrine\Common\Collections\ArrayCollection", $result);
        $this->assertEquals(2, $result->count());
    }
    
    /**
     * ensures the collection contains WML elements only
     */
    public function testParserReturnsCollectionOfWMLElements()
    {
        $result = $this->parser->parse($this->getInput());
        foreach ($result as $element) {
            $this->assertInstanceOf("\Webnoth\WML\Element", $element);
        }
    }
    
    /**
     * ensures the collection contains WML TerrainType elements only
     */
    public function testParserReturnsCollectionOfCorrespondingClasses()
    {
        $result = $this->parser->parse($this->getInput());
        foreach ($result as $element) {
            $this->assertInstanceOf("\Webnoth\WML\Element\TerrainType", $element);
        }
    }
    
    /**
     * ensures the attributes are assigned properly on the elements
     */
    public function testParsedElementsHaveAllAttributes()
    {
        $result = $this->parser->parse($this->getInput());
        $element = $result->first();
        $this->assertInstanceOf("\Webnoth\WML\Element", $element);
        
        /* @var $element Element */
        $this->assertTrue($element->offsetExists('symbol_image'));
        $this->assertEquals('water/ocean-grey-tile', $element['symbol_image']);
        $this->assertTrue($element->offsetExists('id'));
        $this->assertEquals('deep_water_gray', $element['id']);
        $this->assertTrue($element->offsetExists('editor_name'));
        $this->assertEquals('"Gray Deep Water"', $element['editor_name']);
        $this->assertTrue($element->offsetExists('string'));
        $this->assertEquals('Wog', $element['string']);
        $this->assertTrue($element->offsetExists('aliasof'));
        $this->assertEquals('Wo', $element['aliasof']);
        $this->assertTrue($element->offsetExists('submerge'));
        $this->assertEquals('0.5', $element['submerge']);
        $this->assertTrue($element->offsetExists('editor_group'));
        $this->assertEquals('water', $element['editor_group']);
    }
    
    protected function getInput()
    {
        return file_get_contents(__DIR__ . '/data/test.cfg');
    }
}