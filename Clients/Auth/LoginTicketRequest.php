<?php
namespace AfipClient\Clients\Auth;

use AfipClient\Clients\Client;
use AfipClient\Clients\Auth\LoginTicketRequestSigner;
use AfipClient\Utils\FileManager;
use AfipClient\ACException;
use AfipClient\ACHelper;

/**
 * Clase encargada de obtener el ticket de requerimiento de acceso firmado
 */
class LoginTicketRequest
{
    private $file_manager;
    private $signer;

    /**
     * @param SoapClient $soap_client SoapClientFactory::create( [wsdl], [end_point] )
     * @param string $passphrase contraseña para firmar el ticket de requerimiento de acceso.
     */
    public function __construct( FileManager $file_manager, LoginTicketRequestSigner $signer ) 
    {
        $this->file_manager = $file_manager;
        $this->signer = $signer;
    }
    
    /**
     * Obtiene ticket de requerimiento de acceso firmado.
     * Asi obtiene los datos para rellenar el ticket de acceso  enviado por el cliente cliente.
     * @param $client_name nombre del cliente que requiere el acceso
     * @return string $ltr_cms Cryptographic Message Syntax
     */
    public function getCms(Client $client)
    {
        $this->file_manager->tempFolderPermissionsCheck();

        $ltr_path = $this->_createLtrTempFile($client->getClientName());

        return $this->signer->sign($ltr_path);
    }


    /**
     * Generar Ticket de requerimiento de Acceso para un ws ( Login Ticket Request )
     * @param $client_name Servicio al cual se quiere acceder
     * @return string
     * @throws ACException
     */
    private function _createLtrTempFile($client_name)
    {
        $ltr =  new \SimpleXMLElement(
        '<?xml version="1.0" encoding="UTF-8"?>' .
        '<loginTicketRequest version="1.0">'.
        '</loginTicketRequest>'
        );
        $ltr->addChild('header');
        $ltr->header->addChild('uniqueId', date('U'));
        $ltr->header->addChild('generationTime', date('c', date('U')-60));
        $ltr->header->addChild('expirationTime', date('c', date('U')+60));

        $ltr->addChild('service', $client_name);

        $ltr_path = $this->file_manager->createUniqueTempFile('LoginTicketRequest.xml');

        if (!$this->file_manager->asXML($ltr, $ltr_path)) {
            throw new ACException("Error creando ticket de requerimiento");
        }

        return $ltr_path;
    }
}
