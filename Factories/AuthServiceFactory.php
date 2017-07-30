<?php
namespace AfipClient\Factories;

use AfipClient\Factories\SoapClientFactory;
use AfipClient\Clients\Auth\AuthClient;


Class AuthClientFactory{

	/**
	 * Crea un AuthClient
	 * @param array $conf
	 * @return AuthClient
	 */ 
	public static function create( Array $conf,
								   \SoapClient $soap_client = null ){

		return new AuthClient( 
            $soap_client ? $soap_client : SoapClientFactory::create( $conf['auth_wsdl'], $conf['auth_end_point'] )
        );	

	}


}