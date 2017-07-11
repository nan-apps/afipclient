<?php
namespace AfipServices\Factories;

use AfipServices\Factories\SoapClientFactory;
use AfipServices\WebServices\Auth;
use AfipServices\AccessTicketLoader;

Class AuthFactory{

	/**
	 * Crea un cliente soap
	 * @param string $wsdl
	 * @param string $end_point
	 * @return SoapClient
	 */ 
	public static function create( $wsdl, $end_point, $passprhase ){

		return new Auth( 
            SoapClientFactory::create( $wsdl, $end_point ),
            new AccessTicketLoader(),
            $passprhase 
        );	

	}


}