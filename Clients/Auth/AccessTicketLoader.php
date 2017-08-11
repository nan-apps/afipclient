<?php
namespace AfipClient\Clients\Auth;

use AfipClient\ACHelper;

/**
 * Clase encargada de cargar el accessTicket en el cliente.
 */
class AccessTicketLoader
{

    /**
     * Carga los datos al ticket de acceso del cliente
     * @param Client $client el cliente, el cual posee el access ticket a cargar
     * @param string $access_ticket_data datos a ser cargados
     * @return boolean true si levanto y estaban todos los datos
     */
    public function load(AccessTicket $access_ticket, $access_ticket_data)
    {
        $xml = simplexml_load_string($access_ticket_data);

        $access_ticket->setToken((string) $xml->credentials->token);
        $access_ticket->setSign((string) $xml->credentials->sign);
        $access_ticket->setGenerationTime((string) $xml->header->generationTime);
        $access_ticket->setExpirationTime((string) $xml->header->expirationTime);

        return !$access_ticket->isEmpty();
    }

    /**
     * Si en disco hay datos para ticket de acceso, los levanta y se los carga al cliente
     * @param string $file_name
     * @param AccessTicketStore $store
     * @param Client $client el cliente, el cual posee el access ticket a cargar
     * @return boolean true si levanto datos no expirados de access ticket
     */
    public function loadFromStorage($file_name, AccessTicketStore $store, AccessTicket $access_ticket)
    {
        $access_ticket_data = $store->getDataFromStorage($file_name);

        if ($access_ticket_data) {
            return $this->load($access_ticket, $access_ticket_data);
        } else {
            return false;
        }
    }
}
