<?php
namespace AfipClient\Clients\Auth;

use AfipClient\Clients\Client;
use AfipClient\ACHelper;
use AfipClient\ACException;

/**
 * Client de Autenticación y Autorización, encargado de interactuar con api
 */
Class AuthClient extends Client{

	protected $client_name = 'wsaa';
	protected $soap_client;	

	/**
	 * @param SoapClient $soap_client SoapClientFactory::create( [wsdl], [end_point] )
	 * @param string $passphrase contraseña para firmar el ticket de requerimiento de acceso.
	 */ 
	public function __construct( \SoapClient $soap_client ){

		$this->soap_client = $soap_client;	

	}	
	
	/**
	 * invoco el método LoginCMS del WSAA
	 * @param $login_ticket_request_cms (cryptographic message syntax)
	 * @return string
	 */ 
	public function sendCms( $login_ticket_request_cms ){

		try {

			return $this->soap_client->loginCms( [ 'in0' => $login_ticket_request_cms ] );			

		} catch ( \SoapFault $e ) {			
			throw new ACException("Error interactuando con API ({$e->getMessage()})", $this );			
		}


	}

}