<?php
namespace AfipClient\Clients\Auth;

use AfipClient\Clients\Client;
use AfipClient\AccessTicketClient;

/**
 * Clase encargada de cargar el accessTicket en el cliente. 
 */
class AccessTicketLoader{

	/**
	 * Carga los datos al ticket de acceso del cliente
	 * @param Client $client el cliente, el cual posee el access ticket a cargar
	 * @param string $access_ticket_data datos a ser cargados
	 */ 
	public function load( Client $client, $access_ticket_data ){

		if ( !$client instanceof AccessTicketClient )
        	throw new WSException( 'El cliente debe ser una instancia de AccessTicketClient', $this );

		$xml = simplexml_load_string( $access_ticket_data );

		$client->getAccessTicket()->setToken( (string) $xml->credentials->token );
		$client->getAccessTicket()->setSign( (string) $xml->credentials->sign );
		$client->getAccessTicket()->setGenerationTime( (string) $xml->header->generationTime );
		$client->getAccessTicket()->setExpirationTime( (string) $xml->header->expirationTime );

	}

	/**
	 * Si en disco hay datos para ticket de acceso, los levanta y se los carga al cliente
	 * @param AccessTicketStore $store
	 * @param Client $client el cliente, el cual posee el access ticket a cargar
	 * @return boolean true si levanto datos no expirados de access ticket
	 */ 
	public function loadFromStorage( AccessTicketStore $store, Client $client ){

		if ( !$client instanceof AccessTicketClient )
        	throw new WSException( 'El cliente debe ser una instancia de AccessTicketClient', $this );

        $access_ticket_data = $store->getDataFromStorage( $client );

        if( $access_ticket_data )
			$this->load( $client, $access_ticket_data );			

		return !$client->getAccessTicket()->isExpired();

	}


}