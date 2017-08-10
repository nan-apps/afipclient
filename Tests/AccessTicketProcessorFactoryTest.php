<?php

use PHPUnit\Framework\TestCase;
use AfipClient\Factories\AccessTicketProcessorFactory;
use \Mockery as m;

class AccessTicketProcessorFactoryTest extends TestCase {

	public function tearDown(){
 		m::close();
 	}

	public function testCreateShouldReturnAnAuthClient(){

		//when i perform this action
		$at_processor = AccessTicketProcessorFactory::create( 
			[],
			m::mock('AfipClient\Clients\Auth\AuthClient'),
			m::mock('AfipClient\Clients\Auth\AccessTicket'),
			m::mock('AfipClient\Clients\Auth\AccessTicketStore'),
			m::mock('AfipClient\Clients\Auth\AccessTicketLoader'),
			m::mock('AfipClient\Clients\Auth\LoginTicketRequest')
		);

		//the i expect this response
	 	$this->assertInstanceOf( 'AfipClient\Clients\Auth\AccessTicketProcessor', $at_processor );

	}

	/**	 
	 * @expectedException \ArgumentCountError
	 */  
	public function testCreateRequiredDependencies(){
		AccessTicketProcessorFactory::create();
	}

}