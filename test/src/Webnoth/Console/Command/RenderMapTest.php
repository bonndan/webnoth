<?php
namespace Webnoth\Console\Command;

require __DIR__ . '/bootstrap.php';

/**
 * Test for the render map command
 * 
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 * @package Webnoth
 */
class RenderMapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * @var RenderMap 
     */
    protected $command;
    
    public function setUp()
    {
        $this->command = new RenderMap();
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
      
        $input->expects($this->exactly(2))
            ->method('getArgument')
            ->will(
                $this->onConsecutiveCalls(
                    '2_Tutorial',
                    null
                )
            );
        
        $this->setExpectedException(null);
        $this->command->run($input, $output);
    }
}