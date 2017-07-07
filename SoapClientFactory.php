<?php
namespace AfipServices;

Class SoapClientFactory{

	/**
	 * Crea un cliente soap
	 * @param string $wsdl
	 * @param string $end_point
	 * @return SoapClient
	 */ 
	public static function create( $wsdl, $end_point ){

		return new \SoapClient( $wsdl, 
                [
                    'soap_version'   => SOAP_1_2,
                    'location'       => $end_point,
                ]);

	}


}