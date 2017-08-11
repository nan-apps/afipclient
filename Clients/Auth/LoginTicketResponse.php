<?php
namespace AfipClient\Clients\Auth;

/**
 * Clase encargada de manejar la respuesta del ws cuando le mandamos el ticket de requerimiento de acceso
 */
class LoginTicketResponse
{
    private $response;

    
    /**
     * Extre los datos de acceso de la respuesta del WS
     * @param stdClass $response
     */
    public function getAccessTicketData($response)
    {
        return $response->loginCmsReturn;
    }
}
