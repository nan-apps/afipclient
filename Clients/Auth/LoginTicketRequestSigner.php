<?php
namespace AfipClient\Clients\Auth;

use AfipClient\Clients\Client;
use AfipClient\Utils\FileManager;
use AfipClient\Utils\OpensslManager;
use AfipClient\ACException;
use AfipClient\ACHelper;


/**
 * Clase encargada de obtener el ticket de requerimiento de acceso firmado
 */
class LoginTicketRequestSigner{

	private $file_manager;
	private $openssl_manager;
	private $passphrase;
	private $cert_path;
	private $key_path;

	/**
	 * @param SoapClient $soap_client SoapClientFactory::create( [wsdl], [end_point] )
	 * @param string $passphrase contraseña para firmar el ticket de requerimiento de acceso.
	 */ 
	public function __construct( FileManager $file_manager, 
								 OpensslManager $openssl_manager, 
								 $cert_path, $key_path, $passphrase = '' ){

		$this->file_manager = $file_manager;
		$this->openssl_manager = $openssl_manager;
		$this->cert_path = $cert_path;
		$this->key_path = $key_path;
		$this->passphrase = $passphrase;

	}


	/**
	 * Firmar Ticket de requerimiento, para ser enviado solicitando acceso.
	 * @param $ltr_file path al Ticker de requerimiento
	 * @return string $ltr_cms Cryptographic Message Syntax
	 * @throws ACException 
	 */
	public function sign( $ltr_file ){

		try {

	        $ltr_cms_file = $this->file_manager->createUniqueTempFile( "LoginTicketRequest.xml.cms" );

	        $cert = $this->file_manager->getContent( $this->cert_path, true, 'Certificado obtenido de afip' );	        
	        $key = $this->file_manager->getContent( $this->key_path, true, 'Clave que se uso para firmar el pedido de certificado' );

	        $rc = $this->openssl_manager->pkcs7Sign(
	        	$ltr_file,
	            $ltr_cms_file,
	            $cert,
	            $key, 
	            $this->passphrase
        	);

	        if ($rc === FALSE) {
	            throw new ACException("Error firmando ticket de requerimiento");            
	        }

	        $ltr_cms = $this->file_manager->getContent( $ltr_cms_file );

	        // Destruir archivos temporales
	        $this->file_manager->unlinkFiles( [ $ltr_file, $ltr_cms_file ] );

	        // Descartar encabezados MIME
	        $ltr_cms_striped = $this->openssl_manager->stripMIMEHeader( $ltr_cms );

	        return $ltr_cms_striped;
			
		} catch ( ACException $e ) {			
			$this->file_manager->unlinkFiles( [ $ltr_file, $ltr_cms_file ] );
			throw $e;			
		}

	}


}