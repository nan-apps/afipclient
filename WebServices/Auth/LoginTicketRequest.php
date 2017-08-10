<?php

namespace AfipServices\WebServices\Auth;

use AfipServices\WebServices\WebService;
use AfipServices\Traits\FileManager;
use AfipServices\WSException;
use AfipServices\WSHelper;


/**
 * Clase encargada de obtener el ticket de requerimiento de acceso firmado
 */
class LoginTicketRequest
{

  use FileManager;

  private $passphrase;
  private $cert_file_name;
  private $key_file_name;

  /**
   * @param SoapClient $soap_client SoapClientFactory::create( [wsdl], [end_point] )
   * @param string     $passphrase contraseña para firmar el ticket de requerimiento de acceso.
   */
  public function __construct($cert_file_name, $key_file_name, $passphrase = '')
  {

    $this->cert_file_name = $cert_file_name;
    $this->key_file_name = $key_file_name;
    $this->passphrase = $passphrase;

  }

  /**
   * Obtiene ticket de requerimiento de acceso firmado.
   * Asi obtiene los datos para rellenar el ticket de acceso  enviado por el servicio cliente.
   * @param              $service_name nombre del servicio que requiere el acceso
   * @param AccessTicket $access_ticket ticket a ser procesado
   */
  public function getRequestDataCms(WebService $service)
  {

    $service_name = $service->getServiceName();

    return $this->_signLoginTicketRequest(
      $this->_createLoginTicketRequest($service_name)
    );

  }


  /**
   * Generar Ticket de requerimiento de Acceso para un ws ( Login Ticket Request )
   * @param $service_name Servicio al cual se quiere acceder
   * @return string
   * @throws WSException
   */
  private function _createLoginTicketRequest($service_name)
  {

    $ltr = new \SimpleXMLElement(
      '<?xml version="1.0" encoding="UTF-8"?>' .
      '<loginTicketRequest version="1.0">' .
      '</loginTicketRequest>');
    $ltr->addChild('header');
    $ltr->header->addChild('uniqueId', date('U'));
    $ltr->header->addChild('generationTime', date('c', date('U') - 60));
    $ltr->header->addChild('expirationTime', date('c', date('U') + 60));

    $ltr->addChild('service', $service_name);

    $ltr_path = $this->getTempFilePath('LoginTicketRequest.xml');

    if (!$ltr->asXML($ltr_path)) {
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
  private function _signLoginTicketRequest($ltr_file)
  {

    try {

      $this->tempFolderPermissionsCheck();

      $ltr_cms_file = tempnam($this->getTempFolderPath(), "LoginTicketRequest.xml.cms");

      $cert = file_get_contents($this->getResourcesFilePath($this->cert_file_name, true));
      $key = file_get_contents($this->getResourcesFilePath($this->key_file_name, true));

      $rc = openssl_pkcs7_sign(
        $ltr_file,
        $ltr_cms_file,
        $cert,
        [$key, $this->passphrase],
        [],
        !PKCS7_DETACHED
      );

      if ($rc === FALSE) {
        throw new WSException("Error firmando ticket de requerimiento", $this);
      }

      $ltr_cms = file_get_contents($ltr_cms_file);

      // Destruir archivos temporales
      WSHelper::unlink_files([$ltr_file, $ltr_cms_file]);

      // Descartar encabezados MIME
      $ltr_cms = preg_replace("/^(.*\n){5}/", "", $ltr_cms);

      return $ltr_cms;

    } catch (WSException $e) {
      WSHelper::unlink_files([$ltr_file, $ltr_cms_file]);
      throw $e;
    }

  }


}