<?php
use PHPUnit\Framework\TestCase;

use AfipClient\Clients\Biller\Biller;
use AfipClient\Clients\Auth\AccessTicketManager;

use \Mockery as m;

class BillerTest extends TestCase {

	private $biller;

	public function tearDown(){
 		m::close();
 	}

 	public function setUp(){

 		$this->biller = new Biller(
			m::mock('AfipClient\Clients\Biller\BillerClient')
		);

 	}
	
	public function testInstance(){

	 	$this->assertInstanceOf( 'AfipClient\Clients\Biller\Biller', $this->biller );

	}

	/**	 
	 * @expectedException \ArgumentCountError
	 */  	
	public function testInstanceWithNoArguments(){
		new Biller();
	}	


	public function testRequesetCAE(){

		$data = ['data' => ''];
		$client_mock = m::mock('AfipClient\Clients\Biller\BillerClient');
		$client_mock->shouldReceive('requestCAE')
					->with( $data )
					->once()
					->andReturn( ['cae' => 123] );

		$biller = new Biller( $client_mock );

		$this->assertEquals( $biller->requestCAE( $data ), ['cae' => '123'] );

	}


	



}