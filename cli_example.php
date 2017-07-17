<?php

use AfipServices\WSException;
use AfipServices\Factories\AuthServiceFactory;
use AfipServices\Factories\BillerServiceFactory;

if (php_sapi_name() != 'cli') {
  throw new Exception('This application must be run on the command line.');
}


if( !file_exists( 'conf.php' ) ){
	echo "Copia el contenido de conf.example.php a conf.php y completa los datos correctamente\n";
	die();
}

include_once('vendor/autoload.php');
$conf = include( 'conf.php' );


$auth_conf = $conf['wsaa'];
$biller_conf = $conf['wsfev1'];

/* Servicio de autenticacion */
$auth = AuthServiceFactory::create( $auth_conf['wsdl'], 
                                    $auth_conf['end_point'],
                                    $auth_conf['cert_file_name'],
                                    $auth_conf['key_file_name'],
                                    $auth_conf['passprhase']  );

/* Servicio de facturación */            
$biller = BillerServiceFactory::create( $auth, 
                                        $biller_conf['wsdl'], 
                                        $biller_conf['end_point'], 
                                        $conf['cuit'] );


$data = array(
    'Cuit' => '123456789',
    'CantReg' => 1,
    'PtoVta' => $biller_conf['sale_point'], //null para que lo intente obtener el web service
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

try {

    var_dump( $biller->requestCAE( $data ) );
    
} catch ( WSException $e ) {
    var_dump( $e );
}


