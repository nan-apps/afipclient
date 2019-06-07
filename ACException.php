<?php
namespace AfipClient;

use AfipClient\ACHelper;
use AfipClient\Clients\Client;

class ACException extends \Exception
{
    protected $client;
    protected $ws_response;
    protected $ws_response_obj;

    /**
     * @param string $message
     * @param string $ws_response
     * @param int $code
     */
    public function __construct($message = '', Client $client = null, \stdClass $ws_response = NULL, $code = 0)
    {
        parent::__construct($message, $code);

        $this->ws_response = ACHelper::export_response($ws_response);
        $this->ws_response_obj = $ws_response;
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getWSResponse()
    {
        return $this->ws_response;
    }

     /**
     * @return stdClass
     */
    public function getWSResponseObject()
    {
        return $this->ws_response_obj;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }
}
