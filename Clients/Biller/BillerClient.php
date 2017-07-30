<?php
namespace AfipClient\Clients\Biller;

use AfipClient\WSException;
use AfipClient\WSHelper;
use AfipClient\AccessTicket;
use AfipClient\AccessTicketClient;
use AfipClient\AuthParamsProvider;
use AfipClient\Clients\Client;
use AfipClient\Traits\FileManager;

/**
 * Client de facturación electrónica, encargado de interactuar con api WSFEV1 de Afip 
 */
Class BillerClient extends Client{

	use FileManager;

	protected $client_name = 'wsfe';
	protected $soap_client;
	protected $auth_params_provider;	

	/**
	 * @param SoapClient $soap_client SoapClientFactory::create( [wsdl], [end_point] )
	 * @param AuthParamsProvider $acces_ticket_manager el objeto encargado de procesar y completar el AccessTicket
	 */ 
	public function __construct( \SoapClient $soap_client,
								 AuthParamsProvider $auth_params_provider  ){

		$this->soap_client = $soap_client;
		$this->auth_params_provider = $auth_params_provider;		
	}

	/**
	 * Devuelve el nombre del cliente
	 * @return string
	 */ 
	public function getClientName(){
		return $this->client_name;
	}

		
	/**
	 * Solicitar cae y fecha de vencimiento al WS de facturacion
	 * @param array $data  
	 * @return array [ string 'cae' => '',  \DateTime 'cae_validdate' => null, 
	 *                 int 'invoice_number' => 0, string 'tax_id' => '', \DateTime 'invoice_date' => null
	 * 				   stdClass 'full_response' => null ]	 
	 * @throws  WSException 
	 */
	public function requestCAE( $data ){

		$request_params = $this->request_manager->buildCAEParams( $this->_getAuthParams(), $data );

		$response = $this->soap_client->FECAESolicitar( $request_params );

		return $this->response_manager->validateAndParseCAE( $response );
		
		/*$request_params = $this->_buildRequestCAEParams( $data );
		
		$response = $this->soap_client->FECAESolicitar( $request_params );

		return $this->_validateAndParseResponse( $response );*/
		
	}	

	/**
	 * Devuelve array con datos de acceso consultando el AccessTicket
	 * @return array ['token' => '', 'sign' => '', 'cuit' => '']
	 */ 
	private function _getAuthParams(){
		return $this->auth_params_provider->getAuthParams( $this );
	}

	/**
	 * Obtiene el último número de comprobante autorizado
	 * @param Array $data [ 'PtoVta' => '', 'CbteTipo' => '' ]
	 * @return int 
	 */ 
	private function _getLastAuthorizedDoc( $data ){

		$params = [ 'Auth' => $this->_getAuthParams(),
				    'PtoVta' => $data['PtoVta'],
				    'CbteTipo' => $data['CbteTipo'] ];

		$response = $this->soap_client->FECompUltimoAutorizado( $params );

	    if( isset( $results->FECompUltimoAutorizadoResult->Errors ) ){	    	
	    	throw new WSException("Error obteniendo ultimo número de comprobante autorizado", $this,
	    						   WSHelper::export_response( $response ) );
	    }
	    
	    return intval( $response->FECompUltimoAutorizadoResult->CbteNro );

	}

	/**
	 * Obtiene puntos de centa autorizados
	 * @return int 
	 */ 
	private function _getAthorizedSalePoint(){

		$params = [ 'Auth' => $this->_getAuthParams() ];

		$response = $this->soap_client->FEParamGetPtosVenta( $params );

	    if( isset( $results->FECompUltimoAutorizadoResult->Errors ) ){	    	
	    	throw new WSException("Error obteniendo ultimo número de comprobante autorizado", $this,
	    						   WSHelper::export_response( $response ) );
	    }
	    
	    return intval( $response->FEParamGetPtosVentaResult->ResultGet->PtoVenta->Nro );

	}

	

}