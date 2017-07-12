<?php

use PHPUnit\Framework\TestCase;
use AfipServices\SoapClientFactory;

class AuthServiceFactoryTest extends TestCase {

	public function testCreateShouldReturnASoapClientInstance(){

		//given this set of data
		$test_wsdl = 'http://www.webservicex.com/globalweather.asmx?WSDL';

		//when i perform this action
		$auth = AuthServiceFactory::create( $test_wsdl, '' );

		//the i expect this response
	 	$this->assertInstanceOf( 'SoapClient', $soap_client );

	}

}