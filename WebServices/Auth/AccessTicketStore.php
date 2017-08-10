<?php
namespace AfipServices\WebServices\Auth;

use AfipServices\WebServices\WebService;
use AfipServices\Traits\FileManager;

/**
 * Clase encargada guardar y obtener datos de access ticket en disco
 */
class AccessTicketStore{

	use FileManager;


	/**
	 * Si en disco hay datos para ticket de acceso, los levanta y se los carga al servicio
	 * @param Service $service el servicio, el cual posee el access ticket a cargar
	 */ 
	public function getDataFromStorage( WebService $service ){

		$file = $this->getTempFilePath( "TA_{$service->getServiceName()}_{$service->getUniqueID()}.xml");
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
	public function saveDataToStorage( WebService $service, $access_ticket_data ){

		$path = $this->getTempFilePath( "TA_{$service->getServiceName()}_{$service->getUniqueID()}.xml" );

		$rsp = file_put_contents( $path, $access_ticket_data );

		if( $rsp === FALSE ){
			throw new WSException('Error guardando datos de ticket de acceso en disco', $this);
		}

	}


}