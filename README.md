# Clases para interactuar con servicios afip

`Biller` permite interactuar con el servicio de facturacion electronica WSFEV1

## Configuracion

```php
return array(

    'tax_id'             => 'xxxxxxxxxx', //cuit de la empresa emisora
    'auth_passprhase'    => 'xxxxxxxxxx', //pass para firmar el certificado a enviar. Opcional
    'auth_wsdl'          => 'https://wsaahomo.afip.gov.ar/ws/services/LoginCms?wsdl', //si es local pasar ruta absoluta
    'auth_end_point'     => 'https://wsaahomo.afip.gov.ar/ws/services/LoginCms',
    'auth_cert_path'     =>  __DIR__ . '/Resources/cert.pem', //certificado que la lib firma para enviar a api afip y autenticar
    'auth_key_path'      =>  __DIR__ . '/Resources/cert.key', //clave con la que se genero el certificado en pagina de afip   
    'biller_wsdl'        =>  __DIR__ . "/Resources/wsfev1_wsdl.xml", //ejemplo de ruta absoluta a wsdl  
    'biller_end_point'   => 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx',
    'biller_sale_point'  => 1 //si no se le pasa, el biller intentara obtenerlo desde la api de afip

);
```
Para generacion de certificados para operar info aca -> http://www.afip.gob.ar/ws/WSASS/WSASS_manual.pdf

## Carpeta Temp
Debe tener permisos de escritura

## Ejemplo de uso para factura B

```php


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
        'CbtesAsoc' => [ // solo en notas de crédito o débito
            'CbteAsoc' => [
                'Tipo' => '06',
                'PtoVta' => 1,
                'Nro' => 1
            ]
        ]
    );

    //solicita cae y cae_validdate y mas

    var_dump( $biller->requestCAE( $data ) );

    /*[ 
        'cae' => '', 
        'cae_validdate' => '',
        'invoice_number' => '',
        'sale_point' => '',
        'invoice_date' => '',
        'tax_id' => '',
        'full_response' => '',
    ];*/ 
    
} catch ( ACException $e ) {
    var_dump( $e->getMessage() );
}




```

--------------------------------------------------------------------------
**Manuales AFIP**

1. Auth: http://www.afip.gob.ar/ws/WSAA/Especificacion_Tecnica_WSAA_1.2.2.pdf

2. F.E.: http://www.afip.gob.ar/fe/documentos/manual_desarrollador_COMPG_v2_9.pdf

----------------------------------------------------------------------------

## TODO

1. Probar y ajustar para facturas tipo A. 
