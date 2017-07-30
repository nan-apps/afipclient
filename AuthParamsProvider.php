<?php
namespace AfipClient;

use AfipClient\AccessTicketClient;

interface AuthParamsProvider{

	/**
	 * @param Client $service
	 */ 
	public function getAuthParams( AccessTicketClient $service_client );


}