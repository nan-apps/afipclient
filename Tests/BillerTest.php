<?php
use PHPUnit\Framework\TestCase;

use AfipServices\WebServices\Biller;
use AfipServices\AccessTicketManager;
use AfipServices\AccessTicket;

class BillerTest extends TestCase {

	private $biller;

	public function setUp(){
		$this->biller = new Biller();
	}

	public function testInstance(){


	 	$this->assertInstanceOf( 'AfipServices\WebServices\Biller', $this->biller );

	}

	public function testShouldBeAccessTicketManager(){


	 	$this->assertInstanceOf( 'AfipServices\AccessTicketClient', $this->biller );

	}

	/**	 
	 * @expectedException AfipServices\WSException
	 */  
	public function testAccessTicketRequired(){
		$this->biller->getAT();
	}

	/**	 
	 * @expectedException AfipServices\WSException
	 */  
	public function testAccessTicketShouldHaveTaxId(){

		$this->biller = new Biller( null, null, new AccessTicket() );

		$this->biller->getAT();
	}

	



}