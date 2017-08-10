<?php
use PHPUnit\Framework\TestCase;
use AfipClient\Clients\Auth\AccessTicketDiskStore;
use \Mockery as m;

class AccessTicketDiskStoreTest extends TestCase {

	private $store;

	public function tearDown(){
 		m::close();
 	}

 	public function setUp(){
 		$this->store = new AccessTicketDiskStore(
 			m::mock('AfipClient\Utils\FileManager')
		);
 	}

	public function testInstance(){

	 	$this->assertInstanceOf( 'AfipClient\Clients\Auth\AccessTicketDiskStore', $this->store );

	}

	/**	 
	 * @expectedException \ArgumentCountError
	 */  
	public function testCreateRequiredDependencies(){
		new AccessTicketDiskStore();
	}

	public function testGetDataFromStorage(){

		$fm_mock = m::mock('AfipClient\Utils\FileManager');
		$fm_mock->shouldReceive(['getTempFileContent' => 'access_ticket_data'])
				->with( 'AT_file_name.xml' )
				->once();


		$store = new AccessTicketDiskStore(
 			$fm_mock
		);

		$client_mock = m::mock('AfipClient\Client\Client');

		$this->assertEquals( $store->getDataFromStorage( 'file_name' ), 'access_ticket_data' );


	}

	/**	 
	 * @expectedException AfipClient\ACException
	 */  
	public function testCantSaveDataToStorage(){

		$fm_mock = m::mock('AfipClient\Utils\FileManager');
		$fm_mock->shouldReceive(['putTempFileContent' => false])
				->with( 'AT_file_name.xml', 'access_ticket_data' )
				->once();


		$store = new AccessTicketDiskStore(
 			$fm_mock
		);

		$client_mock = m::mock('AfipClient\Client\Client');

		$store->saveDataToStorage( 'file_name', 'access_ticket_data' );


	}


}