<?php

return array(

    'tax_id'             => 'xxxxxxxxxxx',
    'auth_passprhase'    => 'xxxxxxxxxxx',
    'auth_wsdl'          => 'https://wsaahomo.afip.gov.ar/ws/services/LoginCms?wsdl',
    'auth_end_point'     => 'https://wsaahomo.afip.gov.ar/ws/services/LoginCms',
    'auth_cert_path'     =>  __DIR__ . '/Resources/cert.pem',
    'auth_key_path'      =>  __DIR__ . '/Resources/cert.key',    
    'biller_wsdl'        =>  __DIR__ . "/Resources/wsfev1_wsdl.xml", // o 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx?wsdl',
    'biller_end_point'   => 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx',
    'biller_sale_point'  => 1

);
