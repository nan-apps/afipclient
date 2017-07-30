<?php

use PHPUnit\Framework\TestCase;
use AfipClient\Factories\BillerFactory;
use \Mockery as m;

class BillerFactoryTest extends TestCase {

	public function tearDown(){
 		m::close();
 	}

	public function testCreateShouldReturnABiller(){
				
		$biller = BillerFactory::create( 
			['conf'=>''],
			m::mock('AfipClient\Clients\Biller\BillerClient')
		);

		//the i expect this response
	 	$this->assertInstanceOf( 'AfipClient\Clients\Biller\Biller', $biller );

	}

	/**	 
	 * @expectedException \ArgumentCountError
	 */  
	public function testCreateRequiredDependencies(){
		BillerFactory::create();
	}

}