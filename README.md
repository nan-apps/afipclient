# Clases para interactuar con servicios afip

1. `Auth` permite interactuar con el web service de autenticacion WSAA
2. `Biller` permite interactuar con el servicio de facturacion electronica WSFEV1

## Resources

En carpeta Resources debe haber: 

1. `cert.pem` este .pem nos lo da la afip.
2. `cert.key` es la llave con el cual generamos nuestro CSR. El CSR es lo que le enviamos a la afip para obtener el `.pem`

Mas info aca -> http://www.afip.gob.ar/ws/WSASS/WSASS_manual.pdf

##Ejemplo de uso

```
	$conf = [

		'AFIP_API_CONF' => [
	        'CUIT' => 'xxxxxxxxxxx',
	        'WSAA' => [
	            'PASSPHRASE' => '', //opcional
	            'WSDL'       => 'https://wsaahomo.afip.gov.ar/ws/services/LoginCms?wsdl',
	            'END_POINT'  => 'https://wsaahomo.afip.gov.ar/ws/services/LoginCms'
	        ],
	        'WSFEV1' => [
	            'WSDL'      => 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx?wsdl',
	            'END_POINT' => 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx'
	        ]
    	],

	];

    $auth_conf = $conf['WSAA'];            
    $biller_conf = $conf['WSFEV1'];            

	/**
	 * Servicio de autenticación
	 */ 
	$auth = new Auth( 
	    SoapClientFactory::create( $wsdl, $end_point ),                                 
	    $auth_conf['PASSPHRASE'] 
	);        

	/**
	 * 	Servicio de facturacion, recibe como parametro el servicio de autenticación 
	 */
	$biller = new Biller( 
	    SoapClientFactory::create( $biller_conf['WSDL'], $biller_conf['END_POINT'] ), 
	    $auth, 
	    new AccessTicket( $conf['CUIT'] ) 
	);

	//solicita cae y cae_validdate
	return $biller->requestCAE([  
		//Los datos de facturacion a enviar a la afip, para que los valida y nos responda con el cae
		//Los datos a enviar se pueden ver en el manual de F.E.
	]);

```


--------------------------------------------------------------------------
**Manuales**

1. Auth: http://www.afip.gob.ar/ws/WSAA/Especificacion_Tecnica_WSAA_1.2.2.pdf

2. F.E.: http://www.afip.gob.ar/fe/documentos/manual_desarrollador_COMPG_v2_9.pdf