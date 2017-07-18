<?php

use PHPUnit\Framework\TestCase;
use AfipServices\Factories\BillerServiceFactory;
use \Mockery as m;

class BillerServiceFactoryTest extends TestCase {

	public function tearDown(){
 		m::close();
 	}

	public function testCreateShouldReturnAnBillerService(){
				
		$biller = BillerServiceFactory::create( 
			m::mock('AfipServices\WebServices\Auth\AuthService'),
			'',
			'',
			'',
			m::mock('SoapClient'),
			m::mock('AfipServices\AccessTicket')
		);

		//the i expect this response
	 	$this->assertInstanceOf( 'AfipServices\WebServices\Biller\BillerService', $biller );

	}

	/**	 
	 * @expectedException \ArgumentCountError
	 */  
	public function testCreateRequiredDependencies(){
		BillerServiceFactory::create();
	}

}