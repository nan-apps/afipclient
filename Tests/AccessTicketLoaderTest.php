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

	public function testLoadFromStorageButNothingThere(){

		
		$store_mock = m::mock('AfipClient\Clients\Auth\AccessTicketStore');
		$store_mock->shouldReceive(['getDataFromStorage' => false])
				   ->with( 'file_name' )
				   ->once();

		$at_mock = m::mock('AfipClient\Clients\Auth\AccessTicket');

		$this->assertFalse(
			$this->atl->loadFromStorage( 'file_name', $store_mock, $at_mock )
		);

	}

	public function testLoadFromStorage(){

		$xml_string = $this->_getXmlString();

		$biller_mock = m::mock('AfipClient\Clients\Biller\BillerClient');
		$store_mock = m::mock('AfipClient\Clients\Auth\AccessTicketStore');
		$store_mock->shouldReceive( [ 'getDataFromStorage' => $xml_string ] )
				   ->with('file_name' )
				   ->once();

		$xml = simplexml_load_string( $xml_string );

		$at_mock = m::mock('AfipClient\Clients\Auth\AccessTicket');
		$at_mock->shouldReceive('setToken')
				->with( (string) $xml->credentials->token )->once();
		$at_mock->shouldReceive('setSign')
				->with( (string) $xml->credentials->sign )->once();		
		$at_mock->shouldReceive('setGenerationTime')
				->with( (string) $xml->header->generationTime )->once();				
		$at_mock->shouldReceive('setExpirationTime')
			->with( (string) $xml->header->expirationTime )->once();

		$at_mock->shouldReceive(['isEmpty' => false]);	

		$this->assertTrue( 
			$this->atl->loadFromStorage('file_name', $store_mock, $at_mock ) 
		);

	}

	public function testLoad(){

		$xml_string = $this->_getXmlString();
		
		$xml = simplexml_load_string( $xml_string );

		$at_mock = m::mock('AfipClient\Clients\Auth\AccessTicket');
		$at_mock->shouldReceive('setToken')
				->with( (string) $xml->credentials->token )->once();
		$at_mock->shouldReceive('setSign')
				->with( (string) $xml->credentials->sign )->once();		
		$at_mock->shouldReceive('setGenerationTime')
				->with( (string) $xml->header->generationTime )->once();				
		$at_mock->shouldReceive('setExpirationTime')
			->with( (string) $xml->header->expirationTime )->once();

		$at_mock->shouldReceive(['isEmpty' => false]);

		
		$this->assertTrue( $this->atl->load( $at_mock, $xml_string ) );
		

	}

	private function _getXmlString(){
		return 
		'<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
			<loginTicketResponse version="1">
			    <header>
			        <source></source>
			        <destination></destination>
			        <uniqueId></uniqueId>
			        <generationTime>2017-07-19T20:50:00.581-03:00</generationTime>
			        <expirationTime>2017-07-20T08:50:00.581-03:00</expirationTime>
			    </header>
			    <credentials>   
			    	<token>1</token>
        			<sign>1</sign>    
			    </credentials>
			</loginTicketResponse>';
	}


}