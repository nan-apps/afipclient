<?php
namespace AfipServices;

class WSHelper{

	/**
	 * Transforma la respuesta del WS en una array y luego en una representacion stringifada de ese array
	 * @param  $ws_response stdClass
	 */ 
	public static function export_response( \stdClass $ws_response ){		
		return var_export( json_decode( json_encode( $ws_response ), true ), true );
	}

	/**
     * Elimina los archivos pasados
     * @param Array $files
     */ 
    public static function unlink_files( Array $files ){
        array_map('unlink', $files );
    }


}