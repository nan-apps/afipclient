<?php
namespace AfipServices;

use AfipServices\AccessTicket;
use AfipServices\WebServices\WebService;

interface AccessTicketManager{

	public function processAccessTicket( WebService $service, AccessTicket $access_ticket );


}