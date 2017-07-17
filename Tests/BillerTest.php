<?php
use PHPUnit\Framework\TestCase;

use AfipServices\WebServices\Biller\BillerService;
use AfipServices\AccessTicketManager;
use AfipServices\AccessTicket;
use \Mockery as m;

class BillerTest extends TestCase {

	private $biller;

	public function tearDown(){
 		m::close();
 	}

 	public function setUp(){

 		$this->biller = new BillerService(
			m::mock('SoapClient'),
			m::mock('AfipServices\AccessTicketProvider'),
			m::mock('AfipServices\AccessTicket')
		);

 	}
	
	public function testInstance(){

	 	$this->assertInstanceOf( 'AfipServices\WebServices\Biller\BillerService', $this->biller );

	}

	/**	 
	 * @expectedException \ArgumentCountError
	 */  	
	public function testInstanceWithNoArguments(){
		new BillerService();
	}	

	public function testShouldBeAccessTicketClient(){


	 	$this->assertInstanceOf( 'AfipServices\AccessTicketClient', $this->biller );

	}

	/**	 
	 * @expectedException AfipServices\WSException
	 */  
	public function testAccessTicketShouldHaveTaxId(){

		$at_mock = m::mock('AfipServices\AccessTicket');
		$at_mock->shouldReceive('getTaxId')
			    ->once()
			    ->andReturn( null );

		$biller = new BillerService(
			m::mock('SoapClient'),
			m::mock('AfipServices\AccessTicketProvider'),
			$at_mock
		);

		$biller->getAT();
	}

	 
	public function testShouldReturnAccessTicket(){

		$ws_mock = m::mock('AfipServices\WebServices\WebService');

		$at_mock = m::mock('AfipServices\AccessTicket');
		$at_mock->shouldReceive('getTaxId')
			    ->once()
			    ->andReturn( '12345678' );

		$atp_mock = m::mock('AfipServices\AccessTicketProvider');
		$atp_mock->shouldReceive('processAccessTicket')
			     ->once();

		$biller = new BillerService(
			m::mock('SoapClient'),
			$atp_mock,
			$at_mock
		);

		$this->assertInstanceOf( 'AfipServices\AccessTicket', $biller->getAT() );
	}

	



}