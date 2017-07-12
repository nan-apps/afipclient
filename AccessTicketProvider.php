<?php
namespace AfipServices;

use AfipServices\WebServices\WebService;

interface AccessTicketProvider{

	/**
	 * @param WebService $service
	 */ 
	public function processAccessTicket( WebService $service );


}