<?php
namespace AfipClient\Factories;

use AfipClient\AuthParamsProvider;
use AfipClient\Clients\Biller\BillerClient;
use AfipClient\Factories\SoapClientFactory;
use AfipClient\Factories\AccessTicketProcessorFactory;
use AfipClient\Clients\Biller\BillerRequestManager;
use AfipClient\Clients\Biller\BillerResponseManager;

Class BillerClientFactory{

	/**
	 * Crea un BillerClient
	 * @param array $conf
	 * @param string $cert_file_name nombre del archivo del certificado obtenido de afip
	 * @param string $key_file_name nombre del archivo de la clave que se uso para firmar
	 * @return BillerClient
	 */ 
	public static function create( Array $conf,
								   \SoapClient $soap_client = null,
								   AuthParamsProvider $auth_params_provider = null, 
								   BillerRequestManager $request_manager = null,
								   BillerResponseManager $response_manager = null ){

		
		return new BillerClient( 
		    $soap_client ? $soap_client 
		    			 : SoapClientFactory::create( $conf['biller_wsdl'], 
		                 							  $conf['biller_end_point'] ),  

		    $auth_params_provider ? $auth_params_provider 
		                             : AccessTicketProcessorFactory::create( $conf ),
			$request_manager ?: new BillerRequestManager(),
			$response_manager ?: new BillerResponseManager() 
		);

	}


}