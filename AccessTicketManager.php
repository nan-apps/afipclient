<?php
namespace Afip;

use Afip\AccessTicket;
use Afip\WebService;

interface AccessTicketManager{

	public function processAccessTicket( WebService $service, AccessTicket $access_ticket );


}