<?php
namespace Afip\Services;

use Afip\WebService;
use Afip\WSException;
use Afip\WSHelper;
use Afip\AccessTicket;
use Afip\AccessTicketManager;
use Afip\Traits\FileManager;

/**
 * WebService de facturación electrónica, WSFEV1 de Afip 
 */
Class Biller extends WebService{

	use FileManager;

	protected $service_name = 'wsfe';
	protected $soap_client = null;
	protected $access_ticket_manager = null;
	protected $access_ticket = null;

	/**
	 * @param SoapClient $soap_client SoapClientFactory::create( [wsdl], [end_point] )
	 * @param AccessTicketManager $acces_ticket_manager el objeto encargado de procesar y completar el AccessTicket
	 * @param AccessTicket $access_ticker 
	 */ 
	public function __construct( \SoapClient $soap_client,
								 AccessTicketManager $access_ticket_manager = null, 
								 AccessTicket $access_ticket = null  ){

		$this->soap_client = $soap_client;
		$this->access_ticket_manager = $access_ticket_manager;

		if( !$access_ticket->getTaxId() ){
			throw new WSException("El AccessTicket debe tener cuit");			
		}

		$this->access_ticket = $access_ticket;
	}

	/**
	 * Devuelve el nombre del servicio
	 */ 
	public function getServiceName(){
		return $this->service_name;
	}

	/**
	 * Le solicita el Ticket de Acceso al AccessTicketManager
	 * @return AccessTicket
	 */ 
	public function getAT(){
		$this->access_ticket_manager->processAccessTicket( $this, $this->access_ticket );
		return $this->access_ticket;
	}


	/**
	 * Solicitar cae y fecha de vencimiento al WS de facturacion
	 * @param array $data  
	 * @return array [ string 'cae' => '',  \DateTime 'cae_validdate' => '' ]	 
	 * @throws  WSException 
	 */
	public function requestCAE( $data ){

		$request_params = $this->_buildRequestCAEParams( $data );
		
		$response = $this->soap_client->FECAESolicitar( $request_params );

		$cae = (string) $response->FECAESolicitarResult->FeDetResp->FECAEDetResponse->CAE;
		$cae_validdate = (string) $response->FECAESolicitarResult->FeDetResp->FECAEDetResponse->CAEFchVto;

		if( isset( $response->FECAESolicitarResult->Errors ) || !$cae || !$cae_validdate ){
			throw new WSException( "Error obteniendo CAE", $this,	 
	    						   WSHelper::export_response( $response ) );
		}

		$date_data = date_parse_from_format( $cae_validdate );

		return [ 
			'cae' => $cae, 
			'cae_validdate' => date_create_from_format( 'Ymd', $cae_validdate )
		];	
		
	}	

	/**
	 * Armar el array para ser enviado al servicio y solicitar el cae
	 * @param array $data
	 * @return array $params
	 */ 
	private function _buildRequestCAEParams( Array $data = [] ){

		if( !$data['CbteDesde'] ){
			$last_inv_number = $this->_getLastAuthorizedDoc( $data );
			$inv_number = $last_inv_number + 1;			
		} else {
			$inv_number = $data['CbteDesde'];
		}

		$params = [ 
		 	'Auth' => $this->_getAuthParams( $data ),
			'FeCAEReq' => 
				[ 
					'FeCabReq' => 
						[  'CantReg' => $data['CantReg'],
								'PtoVta' => $data['PtoVta'],
								'CbteTipo' => $data['CbteTipo'] 
						],
					'FeDetReq' => 
						[ 'FECAEDetRequest' => 
							[  'Concepto' => $data['Concepto'],
									'DocTipo' => $data['DocTipo'],
									'DocNro' => $data['DocNro'],
									'CbteDesde' => $inv_number,
									'CbteHasta' => $inv_number,
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
				    'CbteTipo' => 2 ];

		$response = $this->soap_client->FECompUltimoAutorizado( $params );

	    if( isset( $results->FECompUltimoAutorizadoResult->Errors ) ){	    	
	    	throw new WSException("Error obteniendo ultimo número de comprobante autorizado", $this,
	    						   WSHelper::export_response( $response ) );
	    }
	    
	    return intval( $response->FECompUltimoAutorizadoResult->CbteNro );

	}

}