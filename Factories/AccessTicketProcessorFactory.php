<?php
namespace AfipClient\Factories;

use AfipClient\Factories\SoapClientFactory;
use AfipClient\Clients\Auth\AuthClient;
use AfipClient\Clients\Auth\AccessTicketLoader;
use AfipClient\Clients\Auth\AccessTicketStore;
use AfipClient\Clients\Auth\LoginTicketRequest;
use AfipClient\Clients\Auth\LoginTicketResponse;

Class AccessTicketProcessorFactory{

	/**
	 * Crea un AuthClient
	 * @param array $conf
	 * @return AuthClient
	 */ 
	public static function create( Array $conf,
								   \SoapClient $soap_client = null,
								   AccessTicketStore $access_ticket_store = null, 
								   AccessTicketLoader $access_ticket_loader = null,
								   LoginTicketRequest $login_ticket_request = null, 
								   LoginTicketResponse $login_ticket_response = null ){

		return new AccessTicketProcessor( 
			$auth_client ? $auth_client : AuthClientFactory::create( $conf ),
            $access_ticket_store ? $access_ticket_store : new AccessTicketStore(), 		 
            $access_ticket_loader ? $access_ticket_loader : new AccessTicketLoader(), 		 
            $login_ticket_request ? $login_ticket_request : new LoginTicketRequest( $auth['auth_cert_path'], 
            																		$auth['auth_key_path'], 
            																		$auth['auth_passprhase']  ),
            $login_ticket_response ? $login_ticket_response : new LoginTicketResponse()           
        );	

	}


}