<?php
namespace Webnoth\Console\Command;

require __DIR__ . '/bootstrap.php';

/**
 * Test for the parse map command
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class ParseMapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * @var ParseMap 
     */
    protected $command;
    
    public function setUp()
    {
        $this->command = new ParseMap();
    }
    
    public function tearDown()
    {
        $this->command = null;
        parent::tearDown();
    }
    
    public function testRunsWithoutException()
    {
        $input  = $this->getMock("\Symfony\Component\Console\Input\InputInterface");
        $output = $this->getMock("\Symfony\Component\Console\Output\OutputInterface");
      
        $input->expects($this->once())
            ->method('getArgument')
            ->will($this->returnValue(APPLICATION_PATH . '/data/2_Tutorial.map'));
        
        $this->setExpectedException(null);
        $this->command->run($input, $output);
    }
}