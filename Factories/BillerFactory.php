<?php
namespace AfipClient\Factories;

use AfipClient\Clients\Biller\Biller;
use AfipClient\Clients\Biller\BillerClient;
use AfipClient\Factories\BillerClientFactory;

class BillerFactory
{

    /**
     * Crea un Biller ( Facturador electronico )
     * @param array $conf
     * @param string $cert_file_name nombre del archivo del certificado obtenido de afip
     * @param string $key_file_name nombre del archivo de la clave que se uso para firmar
     * @return BillerClient
     */
    public static function create(
 
        array $conf,
                                   BillerClient $biller_client = null
 
    ) {
        return new Biller(
            $biller_client ? $biller_client : BillerClientFactory::create($conf)
        );
    }
}
