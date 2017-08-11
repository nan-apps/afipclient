<?php
namespace AfipClient\Utils;

use AfipClient\ACException;
use AfipClient\ACHelper;

class OpensslManager
{

    
    /**
     * Firma un mensaje S/MIME
     * @param string ltr_file ruta al ticket de requerimiento a firmar
     * @param string ltr_cms_file ruta dnde se guardara el ticket formado,
     * @param $cert certificado
     * @param $key clave
     * @param $passprhase
     * @return boolean
     */
    public function pkcs7Sign($ltr_file, $ltr_cms_file, $cert, $key, $passphrase = '')
    {
        return openssl_pkcs7_sign(
                $ltr_file,
                $ltr_cms_file,
                $cert,
                [ $key, $passphrase ],
                [],
                !PKCS7_DETACHED
            );
    }

    /**
     * Descarta encabezado mime de archivo firmado digitalemente
     * @return string
     */
    public function stripMIMEHeader($cms)
    {
        return preg_replace("/^(.*\n){5}/", "", $cms);
    }
}
