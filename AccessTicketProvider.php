<?php
namespace AfipServices;

use AfipServices\AccessTicketClient;

interface AccessTicketProvider{

	/**
	 * @param WebService $service
	 */ 
	public function processAccessTicket( AccessTicketClient $service );


}