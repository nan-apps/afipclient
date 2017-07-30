<?php
namespace AfipClient\Clients\Auth;


use AfipClient\Clients\Auth\AuthClient;
use AfipClient\Clients\Auth\AccessTicketLoader;
use AfipClient\Clients\Auth\AccessTicketStore;
use AfipClient\Clients\Auth\LoginTicketRequest;
use AfipClient\Clients\Auth\LoginTicketResponse;
use AfipClient\Clients\Client;
use AfipClient\AuthParamsProvider;
use AfipClient\AccessTicketClient;
use AfipClient\WSException;



/**
 * Clase encargada de manejar la respuesta del ws cuando le mandamos el ticket de requerimiento de acceso
 */
class AccesTicketProcessor implements AuthParamsProvider{

	private $auth_client;
	private $store;
	private $loader;
	private $login_ticket_request;
	private $login_ticket_response;
	private $service_client;

	public function __construct(  AuthClient $auth_client,
								  AccessTicketStore $store, 		 
		                          AccessTicketLoader $loader, 		 
		                          LoginTicketRequest $login_ticket_request, 
		                          LoginTicketResponse $login_ticket_response ){

		$this->auth_client = $auth_client;
		$this->store = $store;
		$this->loader = $loader;
		$this->login_ticket_request = $login_ticket_request;		
		$this->login_ticket_response = $login_ticket_response;		

	}

	/**
	 * Crear array con datos de acceso consultando el AccessTicket
	 * @return array ['token' => '', 'sign' => '', 'cuit' => '']
	 */ 
	public function getAuthParams( AccessTicketClient $service_client ){	

		$access_ticket = $this->getAT();
		return [ 'Token' => $access_ticket->getToken(),
			   	 'Sign' => $access_ticket->getSign(),
				 'Cuit' => $access_ticket->getTaxId() ];

	}

	/**	 
	 * @param Client $service_client cliente que requiere acceso
	 * @throws WSException
	 */ 
	public function processClientAccessTicket( AccessTicketClient $service_client ){
		
        //si el ticket del cliente esta vacio o vencido y no hay en storage o este tmb esta vacio o vencido => proceso
		if ( $service_client->getAccessTicket()->isExpired() ){

			if( !$this->loader->loadFromStorage( $this->store, $service_client ) ){

				//obtengo el cms para requerimiento de acceso
				$ltr_cms = $this->login_ticket_request->getRequestDataCms( $this->service_client );

				//envio el cms al WS 
				$response = $this->auth_client->sendCms( $ltr_cms );

				//Extraigo de la respuesta el xml con los datos de acceso
				$access_ticket_data = $this->login_ticket_response->getAccessTicketData( $response );

				//guardo datos en disco
				$this->store->saveDataToStorage( $this->service_client, $access_ticket_data );

				//se lo cargo al cliente cliente
				$this->loader->load(  );
			}

		} 
				
	}

	

}