<?php
namespace AfipServices\WebServices;

use AfipServices\WSException;
use AfipServices\WSHelper;
use AfipServices\AccessTicketManager;
use AfipServices\AccessTicketClient;
use AfipServices\AccessTicket;
use AfipServices\AccessTicketLoader;
use AfipServices\WebServices\WebService;
use AfipServices\Traits\FileManager;

/**
 * WebService de Autenticación y Autorización
 */
Class Auth extends WebService implements AccessTicketManager{

	use FileManager;

	private $service_name = 'wsaa';

	private $passphrase;
	private $soap_client;
	private $clients_access_tickets = [];

	/**
	 * @param SoapClient $soap_client SoapClientFactory::create( [wsdl], [end_point] )
	 * @param string $passphrase contraseña para firmar el ticket de requerimiento de acceso.
	 */ 
	public function __construct( \SoapClient $soap_client, 
		                          AccessTicketLoader $access_ticket_loader, 		                          
		                          $passphrase = '' ){

		$this->soap_client = $soap_client;	
		$this->access_ticket_loader = $access_ticket_loader;
		$this->passphrase = $passphrase;

	}
	
	/**
	 * @param WebService $client  el servicio ciente que quiere procesar el ticket de acceso
	 * @param AccessTicket $access_ticket ticket de acceso a ser procesado
	 * @throws WSException
	 */ 
	public function processAccessTicket( WebService $service ){

		if ( !$service instanceof AccessTicketClient )
        	throw new WSException( 'El servicio debe ser una instancia de AccessTicketClient', $this );

        //si el ticket del servicio esta vacio o vencido y no hay en storage o este tmb esta vacio o vencido => proceso
		if( ( $service->getAccessTicket()->isEmpty() || $service->getAccessTicket()->isExpired() ) 			 
			  && !$this->access_ticket_loader->loadFromStorage( $service ) ){

			$this->_process( $service );

		}										 	

				
	}

	/**
	 * Genera el ticket de requerimiento de acceso, lo firma, lo envia al ws. 
	 * Asi obtiene los datos para rellenar el ticket de acceso  enviado por el servicio cliente.
	 * @param $service_name nombre del servicio que requiere el acceso
	 * @param AccessTicket $access_ticket ticket a ser procesado
	 */
	private function _process( WebService $service ){

		$service_name = $service->getServiceName();

		//Obtengo el ticket de requerimiento de acceso
		$ltr = $this->_createLoginTicketRequest( $service_name );
		
		//Lo firmo
		$ltr_cms = $this->_signLoginTicketRequest( $ltr );

		//envio el TRA firmado al WS 
		$response = $this->_sendLoginTicketRequest( $ltr_cms );
		
		//Extraigo de la respuesta el xml con los datos de acceso
		$access_ticket_data = $response->loginCmsReturn;

		//Guardo los datos del ticket de acceso en disco devuelto por el WS
		$this->_saveAccessTicketData( $service_name, $access_ticket_data );

		//genero el access_ticket a partir de los datos devueltos por el ws
		//$this->_buildAccessTicket( $access_ticket, $access_ticket_data );

		$this->access_ticket_loader->load( $service, $access_ticket_data );

	}


	/**
	 * Generar Ticket de requerimiento de Acceso para un ws ( Login Ticket Request )
	 * @param $service_name Servicio al cual se quiere acceder
	 * @return string
	 * @throws WSException
	 */
	private function _createLoginTicketRequest( $service_name ){

		$ltr = new \SimpleXMLElement(
		'<?xml version="1.0" encoding="UTF-8"?>' .
		'<loginTicketRequest version="1.0">'.
		'</loginTicketRequest>');
		$ltr->addChild('header');
		$ltr->header->addChild('uniqueId',date('U'));
		$ltr->header->addChild('generationTime',date('c',date('U')-60));
		$ltr->header->addChild('expirationTime',date('c',date('U')+60));

		$ltr->addChild('service', $service_name );

		$ltr_path = $this->getTempFilePath( 'LoginTicketRequest.xml' );

		if( !$ltr->asXML( $ltr_path ) ){
			throw new WSException("Error creando ticket de requerimiento", $this);
		}

		return $ltr_path; 

	}

	/**
	 * Guarda el ticket de acceso en disco
	 * @param $access_ticket_data xml con datos de acceso devuelto por el ws
	 * @param $access_ticket_data_save_path ruta dnde guardar el ticket de acceso
	 * @throws WSException
	 */ 
	private function _saveAccessTicketData( $service_name, $access_ticket_data ){

		$path = $this->getTempFilePath( "TA_{$service_name}.xml" );

		$rsp = file_put_contents( $path, $access_ticket_data );

		if( $rsp === FALSE ){
			throw new WSException('Error guardando datos de ticket de acceso en disco', $this);
		}

	}

	/**
	 * Firmar Ticket de requerimiento, para ser enviado solicitando acceso.
	 * @param $ltr_file path al Ticker de requerimiento
	 * @return string $ltr_cms Cryptographic Message Syntax
	 * @throws WSException 
	 */
	private function _signLoginTicketRequest( $ltr_file ){

		try {

			$this->tempFolderPermissionsCheck();
	        
	        $ltr_cms_file = tempnam( $this->getTempFolderPath(), "LoginTicketRequest.xml.cms");

	        $cert_pem = file_get_contents( $this->getResourcesFilePath('cert.pem', true) );
	        $cert_key = file_get_contents( $this->getResourcesFilePath('cert.key', true) );

	        $rc = openssl_pkcs7_sign(
	            $ltr_file,
	            $ltr_cms_file,
	            $cert_pem,
	            [ $cert_key, $this->passphrase ],
	            [],
	            !PKCS7_DETACHED
	        );

	        if ($rc === FALSE) {
	            throw new WSException("Error firmando ticket de requerimiento", $this);            
	        }

	        $ltr_cms = file_get_contents($ltr_cms_file);

	        // Destruir archivos temporales
	        WSHelper::unlink_files( [ $ltr_file, $ltr_cms_file ] );

	        // Descartar encabezados MIME
	        $ltr_cms = preg_replace("/^(.*\n){5}/", "", $ltr_cms);

	        return $ltr_cms;
			
		} catch ( WSException $e ) {			
			WSHelper::unlink_files( [ $ltr_file, $ltr_cms_file ] );
			throw $e;			
		}

	}

	/**
	 * invoco el método LoginCMS del WSAA
	 * @param $soap_client 
	 * @return stdClass
	 */ 
	private function _sendLoginTicketRequest( $ltr_cms ){

		if( !$this->soap_client ){
			throw new WSException( "El cliente Soap es necesario para operar", $this );
		}

		return $this->soap_client->loginCms( [ 'in0' => $ltr_cms ] );
	}

}