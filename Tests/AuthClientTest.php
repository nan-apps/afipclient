<?php
use PHPUnit\Framework\TestCase;

use AfipClient\Clients\Auth\AuthClient;
use AfipClient\Clients\Biller\BillerClient;
use AfipClient\Clients\Auth\AccessTicketManager;
use \Mockery as m;

class AuthClientTest extends TestCase
{
    private $auth;

    public function tearDown()
    {
        m::close();
    }

    public function setUp()
    {
        $this->auth = new AuthClient(
            m::mock('SoapClient')
        );
    }

    public function testInstance()
    {
        $this->assertInstanceOf('AfipClient\Clients\Auth\AuthClient', $this->auth);
    }

    public function testSendCms()
    {
        $soap_mock = m::mock('SoapClient');
        $soap_mock->shouldReceive(['loginCms' => 'response' ])
                  ->with([ 'in0' => 'login_ticket_request_cms' ])
                  ->once();

        $auth = new AuthClient(
            $soap_mock
        );

        $this->assertEquals($auth->sendCms('login_ticket_request_cms'), 'response');
    }
}
