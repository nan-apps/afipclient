<?php
namespace AfipClient\Clients\Auth;

use AfipClient\Clients\Client;

/**
 * Clase encargada guardar y obtener datos de access ticket en disco
 */
interface AccessTicketStore
{

    /**
     * @param string $file_name
     */
    public function getDataFromStorage($file_name);

    /**
     * @param string $file_name
     * @param string $access_ticket_data xml con datos de acceso devuelto por el ws
     * @throws ACException
     */
    public function saveDataToStorage($file_name, $access_ticket_data);
}
