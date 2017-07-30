<?php
namespace AfipClient;

use AfipClient\Clients\Client;

class WSException extends \Exception {
    
    protected $client;	
	protected $ws_response;

	/**
	 * @param string $message
	 * @param string $ws_response
	 * @param int $code
	 */ 
    function __construct( $message = '', Client $client = null, $ws_response = '', $code = 0  ) {

        parent::__construct( $message, $code );

        $this->ws_response = $ws_response;
        $this->client = $client;
    }

    /**
     * @return string
     */  
    public function getWSResponse(){
    	return $this->ws_response;
    }

    /**
     * @return Client
     */
    public function getClient(){
    	return $this->client;
    }

}
