<?php

use PHPUnit\Framework\TestCase;
use AfipClient\Factories\BillerClientFactory;
use \Mockery as m;

class BillerClientFactoryTest extends TestCase {

	public function tearDown(){
 		m::close();
 	}

	public function testCreateShouldReturnAnBillerClient(){
				
		$biller = BillerClientFactory::create( 
			['conf' => ''],
			m::mock('SoapClient'),
			m::mock('AfipClient\AuthParamsProvider')
		);

		//the i expect this response
	 	$this->assertInstanceOf( 'AfipClient\Clients\Biller\BillerClient', $biller );

	}

	/**	 
	 * @expectedException \ArgumentCountError
	 */  
	public function testCreateRequiredDependencies(){
		BillerClientFactory::create();
	}

}