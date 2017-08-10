<?php
namespace AfipClient\Clients\Auth;

/**
 * Ticket de acceso
 */
class AccessTicket{
	
    private $token;
    private $sign;
    private $expiration_time;
    private $generation_time;
    private $tax_id; //cuit
    
    /**
     * @param string $tax_id
     * @param string $token
     * @param string $sign
     * @param string $generation_time
     * @param string $expiration_time
     */ 
    public function __construct( $tax_id = null, $token = null, $sign = null, $generation_time = null, 
                                 $expiration_time = null ){

        $this->tax_id = $tax_id;
        $this->token = $token;
        $this->sign = $sign;
        $this->generation_time = $generation_time;
        $this->expiration_time = $expiration_time;
    }

    /**
     * Devuelve el token
     * @return string
     */ 
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Devuelve la firma
     * @return string
     */ 
    public function getSign()
    {
        return $this->sign;
    }

    /**
     * Devuelve momento de creacion
     * @return string
     */ 
    public function getGenerationTime()
    {
        return $this->generation_time;
    }

    /**
     * Devuelve momento de vencimiento
     * @return string
     */ 
    public function getExpirationTime()
    {
        return $this->expiration_time;
    }

    /** 
     * Devuelve cuit
     * @return string
     */ 
    public function getTaxId(){
        return $this->tax_id;
    }

    /**
     * Setea token
     * @param string $token
     */ 
    public function setToken( $token ){
        return $this->token = $token;
    }

    /**
     * Setea sign
     * @param string $sign
     */ 
    public function setSign( $sign ){
        return $this->sign = $sign;
    }

    /**
     * Setea fecha generacion
     * @param string $generation_time
     */ 
    public function setGenerationTime( $generation_time ){
        return $this->generation_time = $generation_time;
    }

    /**
     * Setea fecha vencimiento
     * @param string $expiration_time
     */ 
    public function setExpirationTime( $expiration_time ){
        return $this->expiration_time = $expiration_time;
    }

    /**
     * Setea cuit
     * @param string $tax_id
     */ 
    public function setTaxId( $tax_id ){
        return $this->tax_id = $tax_id;
    }

    /**
     * True si alguno de sus campos obligatorios esta vacio
     * @return boolean
     */ 
    public function isEmpty(){
        return !$this->token || !$this->sign || !$this->generation_time || 
               !$this->expiration_time || !$this->tax_id;
    }

    /**
     * True si el momento de vencimiento ya paso
     * @return boolean
     */ 
    public function isExpired(){

        if( $this->isEmpty() ) return true;
        
        return strtotime( $this->expiration_time ) < time();
    }

    /**
     * isExpired alias
     * @return boolean
     */ 
    public function isEmptyOrExpired(){

        return $this->isExpired();
        
    }

}
