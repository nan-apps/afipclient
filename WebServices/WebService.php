<?php
namespace AfipServices\WebServices;

abstract class WebService{

	private $service_name;
	private $soap_client;
	private $unique_id;

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

	public function setUniqueID(string $id) {
	  $this->unique_id = $id;
  }

  public function getUniqueID() {
	  return $this->unique_id;
  }

}