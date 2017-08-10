<?php
use PHPUnit\Framework\TestCase;

use AfipClient\Clients\Auth\AccessTicketProcessor;
use AfipClient\Clients\Biller\BillerClient;
use AfipClient\Clients\Auth\AccessTicketManager;
use \Mockery as m;

class AccessTicketProcessorTest extends TestCase {

	private $at_processor;

	public function tearDown(){
 		m::close();
 	}

 	public function setUp(){

 		$this->at_processor = new AccessTicketProcessor(
			m::mock('AfipClient\Clients\Auth\AuthClient'),
			m::mock('AfipClient\Clients\Auth\AccessTicket'),
			m::mock('AfipClient\Clients\Auth\AccessTicketStore'),
			m::mock('AfipClient\Clients\Auth\AccessTicketLoader'),
			m::mock('AfipClient\Clients\Auth\LoginTicketRequest'),
			m::mock('AfipClient\Clients\Auth\LoginTicketResponse')
		);

 	}

	public function testInstance(){

		
	 	$this->assertInstanceOf( 'AfipClient\Clients\Auth\AccessTicketProcessor', $this->at_processor );
	}

	/**	 
	 * @expectedException \ArgumentCountError
	 */  	
	public function testInstanceWithNoArguments(){
		new AccessTicketProcessor();
	}	

	public function testShouldBeAuthParamsProvider(){

	 	$this->assertInstanceOf( 'AfipClient\AuthParamsProvider', $this->at_processor );

	}

	/**	 
	 * @expectedException TypeError
	 */  
	public function testGetAuthParamsShouldReceiveAClient(){

		$this->at_processor->getAuthParams( '' );

	}
	
	public function testGetAuthParams(){

		$b_mock = m::mock('AfipClient\Clients\Biller\BillerClient');	
		$b_mock->shouldReceive(['getClientName' => 'client_name']);

		$at_mock = m::mock('AfipClient\Clients\Auth\AccessTicket');
		$at_mock->shouldReceive(['getTaxId' => 'tax_id'])->once();
		
		$atl_mock = m::mock('AfipClient\Clients\Auth\AccessTicketLoader');

		$at_mock->shouldReceive(['isEmpty' => false])->once();		
		
		$atl_mock->shouldNotReceive('loadFromStorage');

		$at_mock->shouldReceive(['isExpired' => false])->once();
		
		$atl_mock->shouldNotReceive('load');
		
		$at_mock->shouldReceive(['isEmptyOrExpired' => false])->once();
		$this->_authParamsTest( $at_mock );

		$at_processor = new AccessTicketProcessor(
			m::mock('AfipClient\Clients\Auth\AuthClient'),
			$at_mock,
			m::mock('AfipClient\Clients\Auth\AccessTicketStore'),
			$atl_mock,
			m::mock('AfipClient\Clients\Auth\LoginTicketRequest'),
			m::mock('AfipClient\Clients\Auth\LoginTicketResponse')
		);

		$this->assertEquals( $at_processor->getAuthParams( $b_mock ), [
			'Token' => 'token',
		   	'Sign' => 'sign',
			'Cuit' => 'tax_id'
		]);

	}

		
	public function testGetAuthParamsLoadingFromStorage(){

		$b_mock = m::mock('AfipClient\Clients\Biller\BillerClient');
		$b_mock->shouldReceive(['getClientName' => 'client_name']);

		$at_mock = m::mock('AfipClient\Clients\Auth\AccessTicket');
		$at_mock->shouldReceive(['getTaxId' => 'tax_id'])->once();

		$store_mock = m::mock('AfipClient\Clients\Auth\AccessTicketStore');		

		$at_mock->shouldReceive(['isEmpty' => true])->once();

		$atl_mock = m::mock('AfipClient\Clients\Auth\AccessTicketLoader');
		$atl_mock->shouldReceive('loadFromStorage')->with(
			'client_name_tax_id', $store_mock, $at_mock
		);
		
		$at_mock->shouldReceive(['isExpired' => false])->once();
		$at_mock->shouldReceive(['isEmptyOrExpired' => false])->once();
		
		$this->_authParamsTest( $at_mock );

		$at_processor = new AccessTicketProcessor(
			m::mock('AfipClient\Clients\Auth\AuthClient'),
			$at_mock,
			$store_mock,
			$atl_mock,
			m::mock('AfipClient\Clients\Auth\LoginTicketRequest'),
			m::mock('AfipClient\Clients\Auth\LoginTicketResponse')
		);

		$this->assertEquals( $at_processor->getAuthParams( $b_mock ), [
			'Token' => 'token',
		   	'Sign' => 'sign',
			'Cuit' => 'tax_id'
		]);

	}

	public function testGetAuthParamsLoadingFromSoapApi(){

		$b_mock = m::mock('AfipClient\Clients\Biller\BillerClient');
		$b_mock->shouldReceive(['getClientName' => 'client_name']);

		$at_mock = m::mock('AfipClient\Clients\Auth\AccessTicket');
		$at_mock->shouldReceive(['getTaxId' => 'tax_id'])->once();
		$at_mock->shouldReceive(['isEmpty' => false])->once();

		$atl_mock = m::mock('AfipClient\Clients\Auth\AccessTicketLoader');
		$atl_mock->shouldNotReceive('loadFromStorage');
		
		$at_mock->shouldReceive(['isExpired' => true])->once();

		$ltr_req_mock = m::mock('AfipClient\Clients\Auth\LoginTicketRequest');
		$ltr_req_mock->shouldReceive(['getCms' => 'ltr_cms'])
				 ->with( $b_mock )
				 ->once();

		$a_mock = m::mock('AfipClient\Clients\Auth\AuthClient');
		$a_mock->shouldReceive(['sendCms' => 'response'])
			   ->with('ltr_cms')
			   ->once();

		$ltr_rsp_mock = m::mock('AfipClient\Clients\Auth\LoginTicketResponse');			   
		$ltr_rsp_mock->shouldReceive(['getAccessTicketData' => 'access_ticket_data'])
				     ->with( 'response' )
				     ->once();

     	$store_mock = m::mock('AfipClient\Clients\Auth\AccessTicketStore');	
     	$store_mock->shouldReceive('saveDataToStorage')
     			   ->with('client_name_tax_id', 'access_ticket_data')
     			   ->once();

     	$atl_mock->shouldReceive('load')
     			 ->with( $at_mock, 'access_ticket_data' )
     			 ->once();		   

		$at_mock->shouldReceive(['isEmptyOrExpired' => false])->once();
		
		$this->_authParamsTest( $at_mock );

		$at_processor = new AccessTicketProcessor(
			$a_mock,
			$at_mock,
			$store_mock,
			$atl_mock,
			$ltr_req_mock,
			$ltr_rsp_mock
		);

		$this->assertEquals( $at_processor->getAuthParams( $b_mock ), [
			'Token' => 'token',
		   	'Sign' => 'sign',
			'Cuit' => 'tax_id'
		]);

	}



	private function _authParamsTest( $at_mock ){
		$at_mock->shouldReceive(['getToken' => 'token'])->once();
		$at_mock->shouldReceive(['getSign' => 'sign'])->once();
		$at_mock->shouldReceive(['getTaxId' => 'tax_id'])->once();	
	}



}