<?php
use PHPUnit\Framework\TestCase;
use AfipClient\Clients\Auth\AccessTicketLoader;
use \Mockery as m;

class AccessTicketLoaderTest extends TestCase {

	private $atl;

	public function tearDown(){
 		m::close();
 	}

 	public function setUp(){
 		$this->atl = new AccessTicketLoader();
 	}

	public function testInstance(){

	 	$this->assertInstanceOf( 'AfipClient\Clients\Auth\AccessTicketLoader', $this->atl );

	}

	/*public function testAccessTicketIsLoaded(){

		$biller_mock = m::mock('AfipClient\Clients\Biller\BillerClient');
		$biller_mock->shouldReceive([
			'getAccessTicket->setToken', 'getAccessTicket->setSign', 
			'getAccessTicket->setGenerationTime', 'getAccessTicket->setExpirationTime'
		])->once();


		$this->atl->load( $biller_mock, '<?xml version="1.0" ?><test></test>' );


	}*/


}