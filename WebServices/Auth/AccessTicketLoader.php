<?php
namespace AfipServices\WebServices\Auth;

use AfipServices\WebServices\WebService;
use AfipServices\AccessTicketClient;

/**
 * Clase encargada de cargar el accessTicket en el servicio. 
 */
class AccessTicketLoader{

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
	 * @param AccessTicketStore $store
	 * @param Service $service el servicio, el cual posee el access ticket a cargar
	 * @return boolean true si levanto datos no expirados de access ticket
	 */ 
	public function loadFromStorage( AccessTicketStore $store, WebService $service ){

		if ( !$service instanceof AccessTicketClient )
        	throw new WSException( 'El servicio debe ser una instancia de AccessTicketClient', $this );

        $access_ticket_data = $store->getDataFromStorage( $service );

        if( $access_ticket_data )
			$this->load( $service, $access_ticket_data );			

		return !$service->getAccessTicket()->isExpired();

	}


}