<?php
use PHPUnit\Framework\TestCase;

use AfipClient\Clients\Auth\AuthClient;
use AfipClient\Clients\Biller\BillerClient;
use AfipClient\AccessTicketManager;
use \Mockery as m;

class AuthClientTest extends TestCase {

	private $auth;

	public function tearDown(){
 		m::close();
 	}

 	public function setUp(){

 		$this->auth = new AuthClient(
			m::mock('SoapClient'),
			m::mock('AfipClient\Clients\Auth\AccessTicketStore'),
			m::mock('AfipClient\Clients\Auth\AccessTicketLoader'),
			m::mock('AfipClient\Clients\Auth\LoginTicketRequest'),
			m::mock('AfipClient\Clients\Auth\LoginTicketResponse')
		);

 	}

	public function testInstance(){

		
	 	$this->assertInstanceOf( 'AfipClient\Clients\Auth\AuthClient', $this->auth );
	}

	/**	 
	 * @expectedException \ArgumentCountError
	 */  	
	public function testInstanceWithNoArguments(){
		new AuthClient();
	}	

	public function testShouldBeAccessTicketProvider(){

	 	$this->assertInstanceOf( 'AfipClient\AccessTicketProvider', $this->auth );

	}

	/**	 
	 * @expectedException TypeError
	 */  
	public function testProcessAccessTicketRequiresAClient(){

		$this->auth->processClientAccessTicket( '' );

	}
	
	public function testShouldDoNothingWhenClientAccessTicketIsNotExpired(){

		$b_mock = m::mock('AfipClient\Clients\Biller\BillerClient');
		$b_mock->shouldReceive([
			'getAccessTicket->isExpired' => false	
		])->once();

		$atl_mock = m::mock('AfipClient\Clients\Auth\AccessTicketLoader');
		$atl_mock->shouldNotReceive('loadFromStorage');

		$auth = new AuthClient(
			m::mock('SoapClient'),
			m::mock('AfipClient\Clients\Auth\AccessTicketStore'),
			$atl_mock,
			m::mock('AfipClient\Clients\Auth\LoginTicketRequest'),
			m::mock('AfipClient\Clients\Auth\LoginTicketResponse')
		);

		$auth->processClientAccessTicket( $b_mock );

	}

	public function testShouldLookTicketInStorageIfExpired(){

		$biller_mock = m::mock('AfipClient\Clients\Biller\BillerClient');
		$biller_mock->shouldReceive([
			'getAccessTicket->isExpired' => true	
		])->once();

		$at_store_mock = m::mock('AfipClient\Clients\Auth\AccessTicketStore');

		$at_loader_mock = m::mock('AfipClient\Clients\Auth\AccessTicketLoader');
		$at_loader_mock->shouldReceive('loadFromStorage')
				 ->once()
				 ->with( $at_store_mock, $biller_mock )
				 ->andReturn( true );

		$auth = new AuthClient(
			m::mock('SoapClient'),
			$at_store_mock,
			$at_loader_mock,
			m::mock('AfipClient\Clients\Auth\LoginTicketRequest'),
			m::mock('AfipClient\Clients\Auth\LoginTicketResponse')
		);

		$auth->processClientAccessTicket( $biller_mock );

	}

	/*public function testShouldGetTicketFromSoapApi(){

		$biller_mock = m::mock('AfipClient\Clients\Biller\BillerClient');
		$biller_mock->shouldReceive([
			'getAccessTicket->isExpired' => true	
		])->once();

		$at_store_mock = m::mock('AfipClient\Clients\Auth\AccessTicketStore');
		$at_store_mock->shouldReceive('saveDataToStorage')
				 ->once()
				 ->with( $biller_mock, '' );

		$at_loader_mock = m::mock('AfipClient\Clients\Auth\AccessTicketLoader');
		$at_loader_mock->shouldReceive('loadFromStorage')
				 ->once()
				 ->with( $at_store_mock, $biller_mock )
				 ->andReturn( false );
		$at_loader_mock->shouldReceive('load')
				 ->once()
				 ->with( $biller_mock, '' )
				 ->andReturn( false );		 

		$lt_request_mock = m::mock('AfipClient\Clients\Auth\LoginTicketRequest');
		$lt_request_mock->shouldReceive('getRequestDataCms')
				         ->once()
				         ->with( $biller_mock );				 

		$soap_mock = m::mock('SoapClient');
		$soap_mock->shouldReceive('loginCms')
				         ->once();				 				         

		$lt_response_mock = m::mock('AfipClient\Clients\Auth\LoginTicketResponse');
		$lt_response_mock->shouldReceive('getAccessTicketData')
				         ->once();				 				         

		$auth = new AuthClient(
			$soap_mock,
			$at_store_mock,
			$at_loader_mock,
			$lt_request_mock,
			$lt_response_mock
		);

		$auth->processClientAccessTicket( $biller_mock );

	}*/



}