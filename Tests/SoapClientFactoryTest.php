<?php

use PHPUnit\Framework\TestCase;
use AfipServices\Factories\SoapClientFactory;

class SoapClientFactoryTest extends TestCase {

	public function testCreateShouldReturnASoapClientInstance(){

		//arrenge
		//given this set of data
		$test_wsdl = 'http://www.webservicex.com/globalweather.asmx?WSDL';

		//act 
		//when i perform this action
		$soap_client = SoapClientFactory::create( $test_wsdl, '' );

		//asset
		//the i expect this response
	 	$this->assertInstanceOf( 'SoapClient', $soap_client );

	}

}