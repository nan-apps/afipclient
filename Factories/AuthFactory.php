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
	 * @param string $cert_file_name nombre del archivo del certificado obtenido de afip
	 * @param string $key_file_name nombre del archivo de la clave que se uso para firmar
	 * @return SoapClient
	 */ 
	public static function create( $wsdl, $end_point, $cert_file_name, $key_file_name, $passprhase ){

		return new Auth( 
            SoapClientFactory::create( $wsdl, $end_point ),
            new AccessTicketLoader(),
            $cert_file_name,
            $key_file_name,
            $passprhase 
        );	

	}


}