<?php
use PHPUnit\Framework\TestCase;
use AfipClient\Clients\Auth\LoginTicketResponse;
use \Mockery as m;

class LoginTicketResponseTest extends TestCase {

	private $ltr;

	public function tearDown(){
 		m::close();
 	}

 	public function setUp(){
 		$this->ltr = new LoginTicketResponse();
 	}

	public function testInstance(){

	 	$this->assertInstanceOf( 'AfipClient\Clients\Auth\LoginTicketResponse', $this->ltr );

	}

	public function testGetAccessTicketData(){

		$login_ticket_response = (object) [
			'loginCmsReturn' => 'access_ticket_data'
		];

		$this->assertEquals(
			$this->ltr->getAccessTicketData( $login_ticket_response ), $login_ticket_response->loginCmsReturn
		);

	}

}