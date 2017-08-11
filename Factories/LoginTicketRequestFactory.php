<?php
namespace AfipClient\Factories;

use AfipClient\Clients\Auth\LoginTicketRequest;
use AfipClient\Clients\Auth\LoginTicketRequestSigner;
use AfipClient\Utils\FileManager;
use AfipClient\Utils\OpensslManager;

class LoginTicketRequestFactory
{

    /**
     * Crea un LoginTicketRequest, ticket de requerimiento de acceso
     * @param array $conf
     * @return AccessTickerProcessor
     */
    public static function create(
 
        $auth_cert_path,
                                   $auth_key_path,
                                   $auth_passprhase,
                                   FileManager $file_manager = null,
                                   LoginTicketRequestSigner $signer = null
 
    ) {
        return new LoginTicketRequest(
            $file_manager ? $file_manager : new FileManager(),

            $signer ? $signer : new LoginTicketRequestSigner(
                new FileManager(),
                new OpensslManager(),
                $auth_cert_path,
                $auth_key_path,
                $auth_passprhase
            )
        );
    }
}
