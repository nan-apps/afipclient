<?php
namespace AfipClient;

use AfipClient\Clients\Client;

interface AuthParamsProvider
{

    /**
     * @param Client $service
     */
    public function getAuthParams(Client $service_client);
}
