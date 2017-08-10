<?php
namespace AfipClient\Clients;

abstract class Client{

	protected $client_name;
	protected $soap_client;

	/**
	 * Devuelve el nombre del cliente
	 * @return string
	 */ 
	public function getClientName(){
		return $this->client_name;
	}

	/**
	 * Devuelve el cliente soap
	 * @return \SoapClient
	 */ 
	public function getSoapClient(){
		return $this->soap_client;
	}

}