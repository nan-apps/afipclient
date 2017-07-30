<?php
use PHPUnit\Framework\TestCase;

use AfipClient\Clients\Biller\BillerClient;
use AfipClient\AccessTicketManager;
use AfipClient\AccessTicket;

use \Mockery as m;

class BillerClientTest extends TestCase {

	private $biller;

	public function tearDown(){
 		m::close();
 	}

 	public function setUp(){

 		$this->biller = new BillerClient(
			m::mock('SoapClient'),
			m::mock('AfipClient\AuthParamsProvider')
		);

 	}
	
	public function testInstance(){

	 	$this->assertInstanceOf( 'AfipClient\Clients\Biller\BillerClient', $this->biller );

	}

	/**	 
	 * @expectedException \ArgumentCountError
	 */  	
	public function testInstanceWithNoArguments(){
		new BillerClient();
	}	


	/**	 
	 * @expectedException AfipClient\WSException
	 */  
	public function testAccessTicketShouldHaveTaxId(){

		$at_mock = m::mock('AfipClient\AccessTicket');
		$at_mock->shouldReceive('getTaxId')
			    ->once()
			    ->andReturn( null );

		$biller = new BillerClient(
			m::mock('SoapClient'),
			m::mock('AfipClient\AccessTicketProvider'),
			$at_mock
		);

		$biller->getAT();
	}

	 
	public function testShouldReturnAccessTicket(){

		$ws_mock = m::mock('AfipClient\Clients\Client');

		$at_mock = m::mock('AfipClient\AccessTicket');
		$at_mock->shouldReceive('getTaxId')
			    ->once()
			    ->andReturn( '12345678' );

		$atp_mock = m::mock('AfipClient\AccessTicketProvider');
		$atp_mock->shouldReceive('processClientAccessTicket')
			     ->once();

		$biller = new BillerClient(
			m::mock('SoapClient'),
			$atp_mock,
			$at_mock
		);

		$this->assertInstanceOf( 'AfipClient\AccessTicket', $biller->getAT() );
	}

	



}