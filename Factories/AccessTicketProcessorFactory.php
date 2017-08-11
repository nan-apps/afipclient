<?php
namespace AfipClient\Factories;

use AfipClient\Clients\Auth\AuthClient;
use AfipClient\Clients\Auth\AccessTicket;
use AfipClient\Clients\Auth\AccessTicketProcessor;
use AfipClient\Clients\Auth\AccessTicketLoader;
use AfipClient\Clients\Auth\AccessTicketStore;
use AfipClient\Clients\Auth\AccessTicketDiskStore;
use AfipClient\Clients\Auth\LoginTicketRequest;
use AfipClient\Clients\Auth\LoginTicketResponse;
use AfipClient\Factories\LoginTicketRequestFactory;
use AfipClient\Factories\AuthClientFactory;
use AfipClient\Utils\FileManager;

class AccessTicketProcessorFactory
{

    /**
    * Crea un AccessTicketProcessor, el core de la lib
    * @param array $conf
    * @return AccessTickerProcessor
    */
    public static function create(

        array $conf,
        AuthClient $auth_client = null,
        AccessTicket $access_ticket = null,
        AccessTicketStore $access_ticket_store = null,
        AccessTicketLoader $access_ticket_loader = null,
        LoginTicketRequest $login_ticket_request = null,
        LoginTicketResponse $login_ticket_response = null

    ) {
        return new AccessTicketProcessor(

            $auth_client ? $auth_client : AuthClientFactory::create($conf),

            $access_ticket ? $access_ticket : new AccessTicket($conf['tax_id']),

            $access_ticket_store ? $access_ticket_store : new AccessTicketDiskStore(new FileManager()),

            $access_ticket_loader ? $access_ticket_loader : new AccessTicketLoader(),

            $login_ticket_request ? $login_ticket_request : LoginTicketRequestFactory::create(


                $conf['auth_cert_path'],
                $conf['auth_key_path'],
                $conf['auth_passprhase']


                ),

            $login_ticket_response ? $login_ticket_response : new LoginTicketResponse()

        );
    }
}
