<?php
namespace Afip;

abstract class WebService{

	private $service_name;
	private $soap_client;

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

}