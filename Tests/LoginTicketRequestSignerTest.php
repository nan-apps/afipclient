<?php
use PHPUnit\Framework\TestCase;
use AfipClient\Clients\Auth\LoginTicketRequestSigner;
use \Mockery as m;

class LoginTicketRequestSignerTest extends TestCase {

	private $ltr;

	public function tearDown(){
 		m::close();
 	}

 	public function setUp(){

 		$this->ltr = new LoginTicketRequestSigner( m::mock('AfipClient\Utils\FileManager'), 
 											 	   m::mock('AfipClient\Utils\OpensslManager'),
 											 	   'cert_path', 
 											 	   'key_path', 
 											 	   'passphrase' );
 	}

	public function testInstance(){

	 	$this->assertInstanceOf( 'AfipClient\Clients\Auth\LoginTicketRequestSigner', $this->ltr );

	}

	/**	 
	 * @expectedException \ArgumentCountError
	 */  	
	public function testInstanceWithNoArguments(){
		new LoginTicketRequestSigner();
	}	


	/**	 
	 * @expectedException AfipClient\ACException
	 */  	
	public function testCantSignLTR(){

		$fm_mock = m::mock('AfipClient\Utils\FileManager');

		$fm_mock->shouldReceive(['createUniqueTempFile' => 'ltr_cms_file'])
				->with( m::type('string') )
				->once();

		$fm_mock->shouldReceive(['getContent' => 'cert'])
				->with( 'cert_path', true, m::type('string') )
				->once();		

		$fm_mock->shouldReceive(['getContent' => 'key'])
				->with( 'key_path', true, m::type('string') )
				->once();		

		$om_mock = m::mock('AfipClient\Utils\OpensslManager');
		$om_mock->shouldReceive(['pkcs7Sign' => false])
				->with( 'ltr_file', 'ltr_cms_file', 'cert', 'key', 'passphrase' )
				->once();		

		$fm_mock->shouldReceive('unlinkFiles')
				->with( ['ltr_file', 'ltr_cms_file'] )
				->once();				

 		$signer = new LoginTicketRequestSigner( $fm_mock, 
										     $om_mock,
										     'cert_path', 
										 	 'key_path', 
										 	 'passphrase' );

 		$signer->sign( 'ltr_file' );

 	}

 	public function testSignLTR(){

		$fm_mock = m::mock('AfipClient\Utils\FileManager');

		$fm_mock->shouldReceive(['createUniqueTempFile' => 'ltr_cms_file'])
				->with( m::type('string') )
				->once();

		$fm_mock->shouldReceive(['getContent' => 'cert'])
				->with( 'cert_path', true, m::type('string') )
				->once();		

		$fm_mock->shouldReceive(['getContent' => 'key'])
				->with( 'key_path', true, m::type('string') )
				->once();		

		$om_mock = m::mock('AfipClient\Utils\OpensslManager');
		$om_mock->shouldReceive(['pkcs7Sign' => true])
				->with( 'ltr_file', 'ltr_cms_file', 'cert', 'key', 'passphrase' )
				->once();	

		$fm_mock->shouldReceive(['getContent' => 'ltr_cms'])
				->with( 'ltr_cms_file' )
				->once();					

		$fm_mock->shouldReceive('unlinkFiles')
				->with( ['ltr_file', 'ltr_cms_file'] )
				->once();

		$om_mock->shouldReceive(['stripMIMEHeader' => 'ltr_cms'])
				->with( 'ltr_cms_file' )
				->once();							

 		$signer = new LoginTicketRequestSigner( $fm_mock, 
										     $om_mock,
										     'cert_path', 
										 	 'key_path', 
										 	 'passphrase' );

 		$this->assertEquals( $signer->sign( 'ltr_file' ), 'ltr_cms' );

 	}

}