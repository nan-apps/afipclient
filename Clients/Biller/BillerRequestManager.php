<?php
namespace AfipClient\Clients\Biller;

use AfipClient\WSException;

/**
 * Clase encargada de manejar la consulta a enviar a al api 
 */
Class BillerRequestManager {

	/**
	 * Armar el array para ser enviado al cliente y solicitar el cae
	 * @param array $data
	 * @return array $params
	 */ 
	public function buildCAEParams( $auth_params, $data ){

		return [];

		/*

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

		*/

	}
	

}