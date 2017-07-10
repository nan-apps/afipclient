<?php
namespace AfipServices;

use AfipServices\WebServices\WebService;

interface AccessTicketManager{

	/**
	 * @param WebService $service
	 */ 
	public function processAccessTicket( WebService $service );


}