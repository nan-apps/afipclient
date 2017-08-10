<?php

use AfipClient\ACException;
use AfipClient\Factories\BillerFactory;

if (php_sapi_name() != 'cli') {
  throw new Exception('This application must be run on the command line.');
}


if( !file_exists( 'conf.php' ) ){	
    throw new Exception("Copia el contenido de conf.example.php a conf.php y completa los datos correctamente\n");	
}

require_once('vendor/autoload.php');

try {

    $conf = include( 'conf.php' );

    /* Servicio de facturación */            
    $biller = BillerFactory::create( $conf );


    $data = array(
        'Cuit' => '123456789',
        'CantReg' => 1,
        'PtoVta' => $conf['biller_sale_point'], //null para que lo intente obtener el web service
        'CbteTipo' => 06, //A:01 B:06 C:11 
        'Concepto' => 2, //servicios
        'DocTipo' => 80, //80=CUIL
        'DocNro' => '123456789',
        'CbteDesde' => null, //para que lo calcule uitlizando el web service 
        'CbteHasta' => null, //para que lo calcule uitlizando el web service
        'CbteFch' => date('Ymd'),
        'ImpNeto' => 0,
        'ImpTotConc' => 1, 
        'ImpIVA' => 0,
        'ImpTrib' => 0,
        'ImpOpEx' => 0,
        'ImpTotal' => 1, 
        'FchServDesde' => date("Ymd"), 
        'FchServHasta' => date("Ymd"), 
        'FchVtoPago' => date("Ymd"),
        'MonId' => 'PES', //PES 
        'MonCotiz' => 1, //1 
    );


    //solicita cae y cae_validdate

    var_dump( $biller->requestCAE( $data ) );

    /* >> [ 
            'cae' => '', 
            'cae_validdate' => '',
            'invoice_number' => '',
            'sale_point' => '',
            'invoice_date' => '',
            'tax_id' => '',
            'full_response' => '',
        ]; */ 
    
} catch ( ACException $e ) {
    var_dump( $e->getMessage() );
}


