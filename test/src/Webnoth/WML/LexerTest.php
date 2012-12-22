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
        return file_get_contents(__DIR__ . '/data/test.cfg');
    }
}