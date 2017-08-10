<?php
use PHPUnit\Framework\TestCase;
use AfipClient\Clients\Auth\LoginTicketRequest;
use \Mockery as m;

class LoginTicketRequestTest extends TestCase {

	private $ltr;

	public function tearDown(){
 		m::close();
 	}

 	public function setUp(){

 		$this->ltr = new LoginTicketRequest( m::mock('AfipClient\Utils\FileManager'), 
 											 m::mock('AfipClient\Clients\Auth\LoginTicketRequestSigner'));
 	}

	public function testInstance(){

	 	$this->assertInstanceOf( 'AfipClient\Clients\Auth\LoginTicketRequest', $this->ltr );

	}

	/**	 
	 * @expectedException \ArgumentCountError
	 */  	
	public function testInstanceWithNoArguments(){
		new LoginTicketRequest();
	}	


	/**
	 * @expectedException AfipClient\ACException
	 */ 
	public function testGetCmsButCantSaveLTR(){

		$biller_mock = m::mock("AfipClient\Clients\Biller\BillerClient");
		$biller_mock->shouldReceive(['getClientName' => 'name'])->once();

		$fm_mock = m::mock('AfipClient\Utils\FileManager');
		$fm_mock->shouldReceive( 'tempFolderPermissionsCheck')->once();		

		$fm_mock->shouldReceive( ['createUniqueTempFile' => 'ltr_temp_path'])
				->with( m::type('string') )
				->once();

		$fm_mock->shouldReceive( ['asXML' => false] )
				->with( m::type('SimpleXMLElement'), 'ltr_temp_path' )
				->once();

		$signer_mock = m::mock('AfipClient\Clients\Auth\LoginTicketRequestSigner');

		$ltr = new LoginTicketRequest( $fm_mock, $signer_mock);

		$ltr->getCms( $biller_mock );

	}

	public function testGetCms(){

		$biller_mock = m::mock("AfipClient\Clients\Biller\BillerClient");
		$biller_mock->shouldReceive(['getClientName' => 'name'])->once();

		$fm_mock = m::mock('AfipClient\Utils\FileManager');
		$fm_mock->shouldReceive( 'tempFolderPermissionsCheck')->once();		

		$fm_mock->shouldReceive( ['createUniqueTempFile' => 'ltr_temp_path'])
				->with( m::type('string') )
				->once();

		$fm_mock->shouldReceive( ['asXML' => true] )
				->with( m::type('SimpleXMLElement'), 'ltr_temp_path' )
				->once();

		$signer_mock = m::mock('AfipClient\Clients\Auth\LoginTicketRequestSigner');
		$signer_mock->shouldReceive(['sign' => 'ltr_cms'])->with( 'ltr_temp_path' )->once();

		$ltr = new LoginTicketRequest( $fm_mock, $signer_mock);

		$this->assertEquals( $ltr->getCms( $biller_mock ), 'ltr_cms' ); 

	}
	

}