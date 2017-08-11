<?php
namespace AfipClient\Factories;

use AfipClient\Factories\SoapClientFactory;
use AfipClient\Clients\Auth\AuthClient;
use AfipClient\ACHelper;

class AuthClientFactory
{

    /**
     * Crea un AuthClient
     * @param array $conf
     * @return AuthClient
     */
    public static function create(
 
        array $conf,
                                   \SoapClient $soap_client = null
 
    ) {
        return new AuthClient(
            $soap_client ? $soap_client : SoapClientFactory::create($conf['auth_wsdl'], $conf['auth_end_point'])
        );
    }
}
