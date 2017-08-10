<?php
namespace AfipClient\Clients\Auth;

use AfipClient\Clients\Client;
use AfipClient\Utils\FileManager;
use AfipClient\ACException;
use AfipClient\ACHelper;

/**
 * Clase encargada guardar y obtener datos de access ticket en disco
 */
class AccessTicketDiskStore implements AccessTicketStore{

	private $file_manager;

	public function __construct(  FileManager $file_manager ){
		$this->file_manager = $file_manager;
	}

	/**
	 * Obtiene el ticket de acceso guardado en disco
	 * @param string $file_name 
	 */ 
	public function getDataFromStorage( $file_name ){

		$access_ticket_data = $this->file_manager
								   ->getTempFileContent( "AT_{$file_name}.xml" );

		return $access_ticket_data;
	}


	/**
	 * Guarda el ticket de acceso en disco
	 * @param $file_name string
	 * @param $access_ticket_data xml con datos de acceso devuelto por el ws	 
	 * @throws ACException
	 */ 
	public function saveDataToStorage( $file_name, $access_ticket_data ){

		$rsp = $this->file_manager
				    ->putTempFileContent( "AT_{$file_name}.xml", $access_ticket_data );

		if( $rsp === FALSE ){
			throw new ACException('Error guardando datos de ticket de acceso en disco');
		}

	}


}