<?php
namespace Webnoth\WML;

require __DIR__ . '/bootstrap.php';

/**
 * LexerTest
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class LexerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system  under test
     * @var Lexer 
     */
    protected $lexer;
    
    public function setUp()
    {
        $this->lexer = new Lexer();
    }
    
    public function tearDown()
    {
        $this->lexer = null;
        parent::tearDown();
    }
    
    /**
     * ensures the lexer finds the opening tokens
     */
    public function testFindsElementOpeners()
    {
        $this->lexer->setInput($this->getInput());
        $elementOpeners = $this->getTokensOfType(Lexer::T_OPEN_ELEMENT);
        $this->assertEquals(2, count($elementOpeners));
    }
    
    /**
     * ensures the lexer finds the closing tokens
     */
    public function testFindsElementClosers()
    {
        $this->lexer->setInput($this->getInput());
        $elementOpeners = $this->getTokensOfType(Lexer::T_CLOSE_ELEMENT);
        $this->assertEquals(2, count($elementOpeners));
    }
    
    /**
     * ensures the attributes are found
     */
    public function testFindsAttributes()
    {
        $this->lexer->setInput($this->getInput());
        $attrs = $this->getTokensOfType(Lexer::T_VALUE);
        $this->assertEquals('symbol_image', $attrs[0]['value']);
        $this->assertEquals('water/ocean-grey-tile', $attrs[1]['value']);
        
        $this->assertEquals('id', $attrs[2]['value']);
        $this->assertEquals('deep_water_gray', $attrs[3]['value']);
        
        $this->assertEquals('editor_name', $attrs[4]['value']);
        $this->assertEquals('"Gray Deep Water"', $attrs[5]['value']);
        
        $this->assertEquals('string', $attrs[6]['value']);
        $this->assertEquals('Wog', $attrs[7]['value']);
        
        $this->assertEquals('symbol_image', $attrs[14]['value']);
        $this->assertEquals('water/ocean-tile', $attrs[15]['value']);
    }
    
    /**
     * returns the found tokens which match a type
     * 
     * @param string $type
     * @return array
     */
    protected function getTokensOfType($type)
    {
        $matches = array();
        while($token = $this->lexer->peek()) {
            if ($token['type'] == $type) {
                $matches[] = $token;
            }
        }
        return $matches;
    }


    /**
     * returns input for testing
     * 
     * @return string
     */
    protected function getInput()
    {
        return '#textdomain wesnoth-lib
# Terrain configuration file. Defines how the terrain _work_ in the game. How
# the terrains _look_ is defined in terrain_graphics.cfg .

# NOTE: terrain ids are used implicitly by the in-game help:
# each "[terrain_type] id=some_id" corresponds to "[section] id=terrain_some_id"
# or "[topic] id=terrain_some_id" identifying its description in [help]

# NOTE: this list is sorted to group things comprehensibly in the editor

#
#    ## Water ##
#

[terrain_type]
    symbol_image=water/ocean-grey-tile #some comment
    id=deep_water_gray
    editor_name= _ "Gray Deep Water"
    string=Wog
    aliasof=Wo
    submerge=0.5
    editor_group=water
[/terrain_type]

[terrain_type]
    symbol_image=water/ocean-tile
    id=deep_water
    name= _ "Deep Water"
    editor_name= _ "Medium Deep Water"
    string=Wo
    submerge=0.5
    editor_group=water
[/terrain_type]
            ';
    }
}