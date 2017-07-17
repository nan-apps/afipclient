<?php
use PHPUnit\Framework\TestCase;

use AfipServices\WebServices\Auth\AuthService;
use AfipServices\AccessTicketManager;
use \Mockery as m;

class AuthServiceTest extends TestCase {

	private $auth;

	public function tearDown(){
 		m::close();
 	}

 	public function setUp(){

 		$this->auth = new AuthService(
			m::mock('SoapClient'),
			m::mock('AfipServices\WebServices\Auth\AccessTicketStore'),
			m::mock('AfipServices\WebServices\Auth\AccessTicketLoader'),
			m::mock('AfipServices\WebServices\Auth\LoginTicketRequest')
		);

 	}

	public function testInstance(){

		
	 	$this->assertInstanceOf( 'AfipServices\WebServices\Auth\AuthService', $this->auth );
	}

	/**	 
	 * @expectedException \ArgumentCountError
	 */  	
	public function testInstanceWithNoArguments(){
		new AuthService();
	}	

	public function testShouldBeAccessTicketProvider(){

	 	$this->assertInstanceOf( 'AfipServices\AccessTicketProvider', $this->auth );

	}

	/**	 
	 * @expectedException TypeError
	 */  
	public function testProcessAccessTicketRequiresAWebService(){

		$this->auth->processAccessTicket( '' );

	}

	/**	 
	 * @expectedException AfipServices\WSException
	 */  
	public function testProcessAccessTicketRequiresAnAccessTicketClient(){
		
		$this->auth->processAccessTicket( m::mock('AfipServices\WebServices\Auth\AuthService') );		

	}

	
	public function testProcessAccessTicketRequiresAnAccessTicketClient(){
		
		$this->auth->processAccessTicket( m::mock('AfipServices\WebServices\Auth\AuthService') );		

	}



}