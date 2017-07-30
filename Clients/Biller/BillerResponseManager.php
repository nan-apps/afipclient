<?php
namespace AfipClient\Clients\Biller;

use AfipClient\WSException;

/**
 * Clase encargada de manejar la respuesta de la api afip facturacion 
 */
Class BillerResponseManager {


	/**
	 * Parsea y prepara array para ser devuelto
	 * @param Array $response
	 * @return Array
	 */ 
	public function validateAndParseCAE( \stdClass $response ){

		return [];

		/*
			if( isset( $response->FECAESolicitarResult->Errors ) || 
			!isset( $response->FECAESolicitarResult->FeDetResp->FECAEDetResponse->CAE )			
			){
			throw new WSException( "Error obteniendo CAE", $this,	 
	    						   WSHelper::export_response( $response ) );
		} 

		$cae = (string) $response->FECAESolicitarResult->FeDetResp->FECAEDetResponse->CAE;
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

		*/

	}
	

}