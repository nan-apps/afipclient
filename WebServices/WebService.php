<?php
namespace AfipServices\WebServices;

abstract class WebService{

	private $service_name;
	private $soap_client;
	private $unique_id;


	public function __construct()
  {
    $this->unique_id = uniqid();
  }

  /**
	 * Devuelve el nombre del servicio
	 * @return string
	 */ 
	public function getServiceName(){
		return $this->service_name;
	}

	/**
	 * Devuelve el cliente soap
	 * @return \SoapClient
	 */ 
	public function getSoapClient(){
		return $this->soap_client;
	}

  /**
   * Setea un id unico para esta instancia. Util en casos donde se desean varias instancias simultaneas
   * para diferentes CUIT's.
   * @param string $id
   */
	public function setUniqueID(string $id) {
	  $this->unique_id = $id;
  }

  /*
   * Devuelve un id unico para esta instancia.
   * Esta propiedad es por defecto uniqid()
   * @return String
   */
  public function getUniqueID():string {
	  return $this->unique_id;
  }

}