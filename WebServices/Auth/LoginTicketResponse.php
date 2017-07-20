<?php
namespace AfipServices\WebServices\Auth;

use AfipServices\WebServices\WebService;
use AfipServices\Traits\FileManager;
use AfipServices\WSException;
use AfipServices\WSHelper;


/**
 * Clase encargada de obtener el ticket de requerimiento de acceso firmado
 */
class LoginTicketResponse{

	private $response;

	/**
	 * Setea la respuesta dada por el WS
	 * @param stdClass $response
	 */ 
	public function setResponse( $response ){
		$this->response = $response;
	}

	public function getAccessTicketData(){
		return $this->response->loginCmsReturn;
	}

}