<?php
namespace AfipClient\Clients\Auth;

use AfipClient\Clients\Client;
use AfipClient\Traits\FileManager;

/**
 * Clase encargada guardar y obtener datos de access ticket en disco
 */
class AccessTicketStore{

	use FileManager;


	/**
	 * Si en disco hay datos para ticket de acceso, los levanta y se los carga al cliente
	 * @param Client $client el cliente, el cual posee el access ticket a cargar
	 */ 
	public function getDataFromStorage( Client $client ){

		$file = $this->getTempFilePath( "TA_{$client->getClientName()}.xml");		
		$access_ticket_data = "";

		if( file_exists( $file ) ){
			$access_ticket_data = file_get_contents( $file );			
		}

		return $access_ticket_data;
	}


	/**
	 * Guarda el ticket de acceso en disco
	 * @param $access_ticket_data xml con datos de acceso devuelto por el ws	 
	 * @throws WSException
	 */ 
	public function saveDataToStorage( Client $client, $access_ticket_data ){

		$path = $this->getTempFilePath( "TA_{$client->getClientName()}.xml" );

		$rsp = file_put_contents( $path, $access_ticket_data );

		if( $rsp === FALSE ){
			throw new WSException('Error guardando datos de ticket de acceso en disco', $this);
		}

	}


}