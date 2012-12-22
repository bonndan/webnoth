<?php
namespace Webnoth\WML;

require __DIR__ . '/bootstrap.php';

/**
 * MapParserTest
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class MapParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system  under test
     * @var MapParser 
     */
    protected $parser;
    
    public function setUp()
    {
        $this->parser = new MapParser(new Lexer);
    }
    
    public function tearDown()
    {
        $this->parser = null;
        parent::tearDown();
    }
    
    /**
     * ensure a single map object is returned
     */
    public function testParserReturnsMapInstance()
    {
        $res = $this->parser->parse($this->getInput());
        $this->assertInstanceOf("\Webnoth\WML\Element\Map", $res);
    }
    
    /**
     * ensures the file is read as 8 tiles wide map
     */
    public function testHasCorrectWidth()
    {
        $res = $this->parser->parse($this->getInput());
        $this->assertEquals(8, $res->getWidth());
    }
    
    /**
     * Ensures all tiles are contained
     */
    public function testHasAllTiles()
    {
        $res = $this->parser->parse($this->getInput());
        $this->assertEquals(32, count($res->getTiles()));
    }
    
    /**
     * returns the map data
     * @return string
     */
    protected function getInput()
    {
        return file_get_contents(__DIR__ . '/data/oldmap.cfg');
    }
}