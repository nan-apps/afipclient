<?php
use PHPUnit\Framework\TestCase;

use AfipServices\WebServices\Auth;
use AfipServices\AccessTicketManager;

class AuthTest extends TestCase {

	private $auth;

	public function setUp(){
		$this->auth = new Auth();
	}

	public function testInstance(){


	 	$this->assertInstanceOf( 'AfipServices\WebServices\Auth', $this->auth );

	}

	public function testShouldBeAccessTicketManager(){


	 	$this->assertInstanceOf( 'AfipServices\AccessTicketManager', $this->auth );

	}

	/**	 
	 * @expectedException TypeError
	 */  
	public function testProcessAccessTicketRequiresAWebService(){

		$this->auth->processAccessTicket( new self() );

	}

	/**	 
	 * @expectedException AfipServices\WSException
	 */  
	public function testProcessAccessTicketRequiresAnAccessTicketClient(){

		$this->auth->processAccessTicket( $this->auth );		

	}



}