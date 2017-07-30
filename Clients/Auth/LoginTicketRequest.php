<?php
namespace AfipClient\Clients\Auth;

use AfipClient\Clients\Client;
use AfipClient\Traits\FileManager;
use AfipClient\WSException;
use AfipClient\WSHelper;


/**
 * Clase encargada de obtener el ticket de requerimiento de acceso firmado
 */
class LoginTicketRequest{

	use FileManager;

	private $passphrase;
	private $cert_path;
	private $key_path;

	/**
	 * @param SoapClient $soap_client SoapClientFactory::create( [wsdl], [end_point] )
	 * @param string $passphrase contraseña para firmar el ticket de requerimiento de acceso.
	 */ 
	public function __construct( $cert_path, $key_path, $passphrase = '' ){

		$this->cert_path = $cert_path;
		$this->key_path = $key_path;
		$this->passphrase = $passphrase;

	}
	
	/**
	 * Obtiene ticket de requerimiento de acceso firmado. 
	 * Asi obtiene los datos para rellenar el ticket de acceso  enviado por el cliente cliente.
	 * @param $client_name nombre del cliente que requiere el acceso
	 * @param AccessTicket $access_ticket ticket a ser procesado
	 * @return string $ltr_cms Cryptographic Message Syntax
	 */
	public function getRequestDataCms( Client $client ){

		$client_name = $client->getClientName();

		return $this->_signLoginTicketRequest( 
			$this->_createLoginTicketRequest( $client_name )
		);

	}


	/**
	 * Generar Ticket de requerimiento de Acceso para un ws ( Login Ticket Request )
	 * @param $client_name Servicio al cual se quiere acceder
	 * @return string
	 * @throws WSException
	 */
	private function _createLoginTicketRequest( $client_name ){

		$ltr = new \SimpleXMLElement(
		'<?xml version="1.0" encoding="UTF-8"?>' .
		'<loginTicketRequest version="1.0">'.
		'</loginTicketRequest>');
		$ltr->addChild('header');
		$ltr->header->addChild('uniqueId',date('U'));
		$ltr->header->addChild('generationTime',date('c',date('U')-60));
		$ltr->header->addChild('expirationTime',date('c',date('U')+60));

		$ltr->addChild('client', $client_name );

		$ltr_path = $this->getTempFilePath( 'LoginTicketRequest.xml' );

		if( !$ltr->asXML( $ltr_path ) ){
			throw new WSException("Error creando ticket de requerimiento", $this);
		}

		return $ltr_path; 

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

	        $cert = file_get_contents( $this->validateFile( $this->cert_path, 'Certificado obtenido de afip' ) );
	        $key = file_get_contents( $this->validateFile( $this->key_path, 'Clave que se uso para firmar el pedido de certificado' ) );

	        $rc = openssl_pkcs7_sign(
	            $ltr_file,
	            $ltr_cms_file,
	            $cert,
	            [ $key, $this->passphrase ],
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


}