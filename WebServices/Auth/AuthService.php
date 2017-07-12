<?php
namespace AfipServices\WebServices\Auth;

use AfipServices\WebServices\Auth\AccessTicketLoader;
use AfipServices\WebServices\Auth\AccessTicketStore;
use AfipServices\WebServices\Auth\LoginTicketRequest;
use AfipServices\WebServices\WebService;
use AfipServices\AccessTicketProvider;
use AfipServices\AccessTicketClient;
use AfipServices\WSException;

/**
 * WebService de Autenticación y Autorización
 */
Class AuthService extends WebService implements AccessTicketProvider{

	private $service_name = 'wsaa';
	private $soap_client;
	private $access_ticket_store;
	private $access_ticket_loader;
	private $login_ticket_request;

	/**
	 * @param SoapClient $soap_client SoapClientFactory::create( [wsdl], [end_point] )
	 * @param string $passphrase contraseña para firmar el ticket de requerimiento de acceso.
	 */ 
	public function __construct( \SoapClient $soap_client, 
		                          AccessTicketStore $access_ticket_store, 		 
		                          AccessTicketLoader $access_ticket_loader, 		 
		                          LoginTicketRequest $login_ticket_request){

		$this->soap_client = $soap_client;	
		$this->access_ticket_store = $access_ticket_store;
		$this->access_ticket_loader = $access_ticket_loader;
		$this->login_ticket_request = $login_ticket_request;		

	}
	
	/**
	 * @param WebService $client  el servicio cliente que quiere procesar el ticket de acceso
	 * @param AccessTicket $access_ticket ticket de acceso a ser procesado
	 * @throws WSException
	 */ 
	public function processAccessTicket( WebService $service ){

		if ( !$service instanceof AccessTicketClient ){
        	throw new WSException( 'El servicio debe ser una instancia de AccessTicketClient', $this );
		} 
        //si el ticket del servicio esta vacio o vencido y no hay en storage o este tmb esta vacio o vencido => proceso
		elseif ( $service->getAccessTicket()->isExpired() &&  
			    !$this->access_ticket_loader->loadFromStorage( $this->access_ticket_store, $service ) ) {
			//obtengo nuevos datos de acceso
			$access_ticket_data = $this->_getNewAccessTicketData( $service );

			//guardo datos en disco
			$this->access_ticket_store->saveDataToStorage( $service, $access_ticket_data );

			//se lo cargo al servicio cliente
			$this->access_ticket_loader->load( $service, $access_ticket_data );

		} else {
			//nada, el servicio ya tiene un ticket valido para operar
		}
				
	}

	/**
	 * Obtiene ticket de requerimiento de acceso firmado y lo envia a la api de afip
	 * para obtener los datos del ticket de acceso
	 * @param WebService $service
	 */ 
	private function _getNewAccessTicketData( WebService $service ){

		//obtengo el cms para requerimiento de acceso
		$ltr_cms = $this->login_ticket_request->getRequestDataCms( $service );

		//envio el cms al WS 
		$login_ticket_response = $this->_sendCms( $ltr_cms );

		//Extraigo de la respuesta el xml con los datos de acceso
		$access_ticket_data = $login_ticket_response->loginCmsReturn;

		return $access_ticket_data;

	}

	

	/**
	 * invoco el método LoginCMS del WSAA
	 * @param $login_ticket_request_cms (cryptographic message syntax)
	 * @return stdClass
	 */ 
	private function _sendCms( $login_ticket_request_cms ){

		return $this->soap_client->loginCms( [ 'in0' => $login_ticket_request_cms ] );

	}

}