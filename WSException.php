<?php
namespace Afip;

use Afip\WebService;

class WSException extends \Exception {
    
    protected $service;	
	protected $ws_response;

	/**
	 * @param string $message
	 * @param string $ws_response
	 * @param int $code
	 */ 
    function __construct( $message = '', WebService $service = null, $ws_response = '', $code = 0  ) {

        parent::__construct( $message, $code );

        $this->ws_response = $ws_response;
        $this->service = $service;
    }

    /**
     * @return string
     */  
    public function getWSResponse(){
    	return $this->ws_response;
    }

    /**
     * @return WebService
     */
    public function getService(){
    	return $this->service;
    }

}
