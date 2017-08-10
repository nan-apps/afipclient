<?php
namespace AfipClient;

class ACHelper{

	/**
	 * Transforma la respuesta del WS en una array y luego en una representacion stringifada de ese array
	 * @param  $ws_response stdClass
	 */ 
	public static function export_response( \stdClass $ws_response ){		
		return var_export( json_decode( json_encode( $ws_response ), true ), true );
	}

	/**
	 * imprime data en formato para debug
	 * @param $data
	 */ 
	public static function pr( $data ){

		echo "<pre>";
		var_dump( $data );
		echo "</pre>";

	}

	/**
	 * imprime data en formato para debug y luego hace die()
	 * @param $data
	 */ 
	public static function prd( $data ){

		self::pr($data);
		die();

	}
	

}