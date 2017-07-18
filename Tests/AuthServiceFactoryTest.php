<?php

use PHPUnit\Framework\TestCase;
use AfipServices\Factories\AuthServiceFactory;
use \Mockery as m;

class AuthServiceFactoryTest extends TestCase {

	public function tearDown(){
 		m::close();
 	}

	public function testCreateShouldReturnAnAuthService(){

		//when i perform this action
		$auth = AuthServiceFactory::create( 
			'', 
			'', 
			'', 
			'', 
			'', 
			m::mock('SoapClient'),
			m::mock('AfipServices\WebServices\Auth\AccessTicketStore'),
			m::mock('AfipServices\WebServices\Auth\AccessTicketLoader'),
			m::mock('AfipServices\WebServices\Auth\LoginTicketRequest')
		);

		//the i expect this response
	 	$this->assertInstanceOf( 'AfipServices\WebServices\Auth\AuthService', $auth );

	}

	/**	 
	 * @expectedException \ArgumentCountError
	 */  
	public function testCreateRequiredDependencies(){
		AuthServiceFactory::create();
	}

}