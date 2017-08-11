<?php

use PHPUnit\Framework\TestCase;
use AfipClient\Factories\AuthClientFactory;
use \Mockery as m;

class AuthClientFactoryTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testCreateShouldReturnAnAuthClient()
    {

        //when i perform this action
        $auth = AuthClientFactory::create(
            [],
            m::mock('SoapClient'),
            m::mock('AfipClient\Clients\Auth\AccessTicketStore'),
            m::mock('AfipClient\Clients\Auth\AccessTicketLoader'),
            m::mock('AfipClient\Clients\Auth\LoginTicketRequest')
        );

        //the i expect this response
        $this->assertInstanceOf('AfipClient\Clients\Auth\AuthClient', $auth);
    }

    /**
     * @expectedException \ArgumentCountError
     */
    public function testCreateRequiredDependencies()
    {
        AuthClientFactory::create();
    }
}
