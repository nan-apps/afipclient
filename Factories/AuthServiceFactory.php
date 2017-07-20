<?php
namespace AfipServices\Factories;

use AfipServices\Factories\SoapClientFactory;
use AfipServices\WebServices\Auth\AuthService;
use AfipServices\WebServices\Auth\AccessTicketLoader;
use AfipServices\WebServices\Auth\AccessTicketStore;
use AfipServices\WebServices\Auth\LoginTicketRequest;
use AfipServices\WebServices\Auth\LoginTicketResponse;

Class AuthServiceFactory{

	/**
	 * Crea un AuthService
	 * @param string $wsdl
	 * @param string $end_point
	 * @param string $cert_file_name nombre del archivo del certificado obtenido de afip
	 * @param string $key_file_name nombre del archivo de la clave que se uso para firmar
	 * @param string $passprhase
	 * @return AuthService
	 */ 
	public static function create( $wsdl, 
								   $end_point, 
								   $cert_file_name, 
								   $key_file_name, 
								   $passprhase = '',
								   \SoapClient $soap_client = null,
								   AccessTicketStore $access_ticket_store = null, 
								   AccessTicketLoader $access_ticket_loader = null,
								   LoginTicketRequest $login_ticket_request = null, 
								   LoginTicketResponse $login_ticket_response = null ){

		return new AuthService( 
            $soap_client ? $soap_client : SoapClientFactory::create( $wsdl, $end_point ),
            $access_ticket_store ? $access_ticket_store : new AccessTicketStore(), 		 
            $access_ticket_loader ? $access_ticket_loader : new AccessTicketLoader(), 		 
            $login_ticket_request ? $login_ticket_request : new LoginTicketRequest( $cert_file_name, $key_file_name, $passprhase  ),
            $login_ticket_response ? $login_ticket_response : new LoginTicketResponse()           
        );	

	}


}