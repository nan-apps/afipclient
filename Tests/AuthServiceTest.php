<?php
use PHPUnit\Framework\TestCase;

use AfipServices\WebServices\Auth\AuthService;
use AfipServices\WebServices\Biller\BillerService;
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
			m::mock('AfipServices\WebServices\Auth\LoginTicketRequest'),
			m::mock('AfipServices\WebServices\Auth\LoginTicketResponse')
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
		
		$this->auth->processAccessTicket( m::mock('AfipServices\AccessTicketClient') );		

	}

	
	public function testShouldDoNothingWhenServiceAccessTicketIsNotExpired(){

		$b_mock = m::mock('AfipServices\WebServices\Biller\BillerService');
		$b_mock->shouldReceive([
			'getAccessTicket->isExpired' => false	
		])->once();

		$atl_mock = m::mock('AfipServices\WebServices\Auth\AccessTicketLoader');
		$atl_mock->shouldNotReceive('loadFromStorage');

		$auth = new AuthService(
			m::mock('SoapClient'),
			m::mock('AfipServices\WebServices\Auth\AccessTicketStore'),
			$atl_mock,
			m::mock('AfipServices\WebServices\Auth\LoginTicketRequest'),
			m::mock('AfipServices\WebServices\Auth\LoginTicketResponse')
		);

		$auth->processAccessTicket( $b_mock );

	}

	public function testShouldLookTicketInStorageIfExpired(){

		$biller_mock = m::mock('AfipServices\WebServices\Biller\BillerService');
		$biller_mock->shouldReceive([
			'getAccessTicket->isExpired' => true	
		])->once();

		$at_store_mock = m::mock('AfipServices\WebServices\Auth\AccessTicketStore');

		$at_loader_mock = m::mock('AfipServices\WebServices\Auth\AccessTicketLoader');
		$at_loader_mock->shouldReceive('loadFromStorage')
				 ->once()
				 ->with( $at_store_mock, $biller_mock )
				 ->andReturn( true );

		$auth = new AuthService(
			m::mock('SoapClient'),
			$at_store_mock,
			$at_loader_mock,
			m::mock('AfipServices\WebServices\Auth\LoginTicketRequest'),
			m::mock('AfipServices\WebServices\Auth\LoginTicketResponse')
		);

		$auth->processAccessTicket( $biller_mock );

	}



}