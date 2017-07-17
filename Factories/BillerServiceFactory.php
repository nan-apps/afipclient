<?php
namespace AfipServices\Factories;

use AfipServices\Factories\SoapClientFactory;
use AfipServices\WebServices\Biller\BillerService;
use AfipServices\WebServices\Auth\AuthService;
use AfipServices\AccessTicket;

Class BillerServiceFactory{

	/**
	 * Crea un BillerService
	 * @param string $wsdl
	 * @param string $end_point
	 * @param string $cert_file_name nombre del archivo del certificado obtenido de afip
	 * @param string $key_file_name nombre del archivo de la clave que se uso para firmar
	 * @return BillerService
	 */ 
	public static function create( AuthService $auth, 
											   $wsdl, 
											   $end_point, 
											   $cuit,
											   \SoapClient $soap_client = null,
											   AccessTicket $access_ticket = null ){

		return new BillerService( 
		    $soap_client ? $soap_client : SoapClientFactory::create( $wsdl, $end_point ), 
		    $auth, 
		    $access_ticket ? $access_ticket : new AccessTicket( $cuit ) 
		);

	}


}