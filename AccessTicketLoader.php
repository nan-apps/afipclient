<?php
namespace AfipServices;

use AfipServices\WebServices\WebService;
use AfipServices\Traits\FileManager;

/**
 * Clase encargada de cargar el accessTicket en el servicio. 
 */
class AccessTicketLoader{

	use FileManager;


	/**
	 * Carga los datos al ticket de acceso del servicio
	 * @param Service $service el servicio, el cual posee el access ticket a cargar
	 * @param string $access_ticket_data datos a ser cargados
	 */ 
	public function load( WebService $service, $access_ticket_data ){

		if ( !$service instanceof AccessTicketClient )
        	throw new WSException( 'El servicio debe ser una instancia de AccessTicketClient', $this );

		$xml = simplexml_load_string( $access_ticket_data );

		$service->getAccessTicket()->setToken( (string) $xml->credentials->token );
		$service->getAccessTicket()->setSign( (string) $xml->credentials->sign );
		$service->getAccessTicket()->setGenerationTime( (string) $xml->header->generationTime );
		$service->getAccessTicket()->setExpirationTime( (string) $xml->header->expirationTime );

	}

	/**
	 * Si en disco hay datos para ticket de acceso, los levanta y se los carga al servicio
	 * @param Service $service el servicio, el cual posee el access ticket a cargar
	 */ 
	public function loadFromStorage( WebService $service ){

		if ( !$service instanceof AccessTicketClient )
        	throw new WSException( 'El servicio debe ser una instancia de AccessTicketClient', $this );

		$file = $this->getTempFilePath( "TA_{$service->getServiceName()}.xml");		
		$access_ticket_data = "";

		if( file_exists( $file ) ){
			$access_ticket_data = file_get_contents( $file );			
			$this->load( $service, $access_ticket_data );			
		}

		return !$service->getAccessTicket()->isExpired();

	}


}