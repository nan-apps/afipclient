<?php
namespace AfipServices\WebServices\Biller;

use AfipServices\WSException;
use AfipServices\WSHelper;
use AfipServices\AccessTicket;
use AfipServices\AccessTicketClient;
use AfipServices\AccessTicketProvider;
use AfipServices\WebServices\WebService;
use AfipServices\Traits\FileManager;

/**
 * WebService de facturación electrónica, WSFEV1 de Afip 
 */
Class BillerService extends WebService implements AccessTicketClient{

	use FileManager;

	protected $service_name = 'wsfe';
	protected $soap_client;
	protected $access_ticket_provider;
	protected $access_ticket;

	/**
	 * @param SoapClient $soap_client SoapClientFactory::create( [wsdl], [end_point] )
	 * @param AccessTicketProvider $acces_ticket_manager el objeto encargado de procesar y completar el AccessTicket
	 * @param AccessTicket $access_ticket
	 */ 
	public function __construct( \SoapClient $soap_client,
								 AccessTicketProvider $access_ticket_provider, 
								 AccessTicket $access_ticket  ){

		$this->soap_client = $soap_client;
		$this->access_ticket_provider = $access_ticket_provider;
		$this->access_ticket = $access_ticket;
	}

	/**
	 * Devuelve el nombre del servicio
	 * @return string
	 */ 
	public function getServiceName(){
		return $this->service_name;
	}

	/**
	 * Devuelve el access ticket
	 * @return AccessTicket
	 */ 
	public function getAccessTicket(){
		return $this->access_ticket;
	}

	/**
	 * Le solicita el Ticket de Acceso al AccessTicketProvider
	 * @return AccessTicket
	 */ 
	public function getAT(){

		if( !$this->access_ticket->getTaxId() ){			
			throw new WSException("El Ticket de acceso al WSFE de Afip debe tener cuit", $this);			
		}


		$this->access_ticket_provider->processAccessTicket( $this );
		return $this->access_ticket;
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

		$request_params = $this->_buildRequestCAEParams( $data );
		
		$response = $this->soap_client->FECAESolicitar( $request_params );

		return $this->_validateAndParseResponse( $response );
		
	}	


	/**
	 * Parsea y prepara array para ser devuelto
	 * @param Array $response
	 * @return Array
	 */ 
	private function _validateAndParseResponse( \stdClass $response ){

		$cae = (string) $response->FECAESolicitarResult->FeDetResp->FECAEDetResponse->CAE;

		if( isset( $response->FECAESolicitarResult->Errors ) || !$cae ){
		  die(debug($response));
			throw new WSException( "Error obteniendo CAE", $this,	 
	    						   WSHelper::export_response( $response ) );
		}

		$cae_validdate = (string) $response->FECAESolicitarResult->FeDetResp->FECAEDetResponse->CAEFchVto;
		$invoice_number = (int) $response->FECAESolicitarResult->FeDetResp->FECAEDetResponse->CbteDesde;
		$tax_id = (string) $response->FECAESolicitarResult->FeDetResp->FECAEDetResponse->DocNro;
		$invoice_date = (string) $response->FECAESolicitarResult->FeDetResp->FECAEDetResponse->CbteFch;

		return [ 
			'cae' => $cae, 
			'cae_validdate' => date_create_from_format( 'Ymd', $cae_validdate ),
			'invoice_number' => $invoice_number,
			'invoice_date' => date_create_from_format( 'Ymd', $invoice_date ),
			'tax_id' => $tax_id,
			'full_response' => WSHelper::export_response( $response )
		];	

	}

	/**
	 * Armar el array para ser enviado al servicio y solicitar el cae
	 * @param array $data
	 * @return array $params
	 */ 
	private function _buildRequestCAEParams( Array $data = [] ){

		if( !$data['CbteDesde'] ){
			$last_invoice_number = $this->_getLastAuthorizedDoc( $data );
			$invoice_number = $last_invoice_number + 1;			
		} else {
			$invoice_number = $data['CbteDesde'];
		}

		$sale_point = $data['PtoVta'] ? $data['PtoVta'] : $this->_getAthorizedSalePoint();
		

		$params = [ 
		 	'Auth' => $this->_getAuthParams( $data ),
			'FeCAEReq' => 
				[ 
					'FeCabReq' => 
						[  'CantReg' => $data['CantReg'],
								'PtoVta' => $sale_point,
								'CbteTipo' => $data['CbteTipo'] 
						],
					'FeDetReq' => 
						[ 'FECAEDetRequest' => 
							[  'Concepto' => $data['Concepto'],
									'DocTipo' => $data['DocTipo'],
									'DocNro' => $data['DocNro'],
									'CbteDesde' => $invoice_number,
									'CbteHasta' => $invoice_number,
									'CbteFch' => $data['CbteFch'],
									'ImpNeto' => $data['ImpNeto'],
									'ImpTotConc' => $data['ImpTotConc'], 
									'ImpIVA' => $data['ImpIVA'],
									'ImpTrib' => $data['ImpTrib'],
									'ImpOpEx' => $data['ImpOpEx'],
									'ImpTotal' => $data['ImpTotal'], 
									'FchServDesde' => $data['FchServDesde'], 
									'FchServHasta' => $data['FchServHasta'], 
									'FchVtoPago' => $data['FchVtoPago'], 
									'MonId' => $data['MonId'],  
									'MonCotiz' => $data['MonCotiz'],  
								], 
							], 
						], 
		];

		if( $data['ImpIVA'] ){
			$params['FeCAEReq']['FeDetReq']['ImpIVA'] = [
				//alicuotas
			];
		}

		if( $data['ImpTrib'] ){
			$params['FeCAEReq']['FeDetReq']['Tributos'] = [
				//tributos
			];	
		}

		return $params;
	}

	/**
	 * Crear array con datos de acceso consultando el AccessTicket
	 * @return array ['token' => '', 'sign' => '', 'cuit' => '']
	 */ 
	private function _getAuthParams(){	

		$access_ticket = $this->getAT();
		return [ 'Token' => $access_ticket->getToken(),
			   	 'Sign' => $access_ticket->getSign(),
				 'Cuit' => $access_ticket->getTaxId() ];

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