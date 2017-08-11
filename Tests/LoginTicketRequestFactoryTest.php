<?php

use PHPUnit\Framework\TestCase;
use AfipClient\Factories\LoginTicketRequestFactory;
use \Mockery as m;

class LoginTicketRequestFactoryTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testCreateShouldReturnAnLoginTicketRequest()
    {

        //when i perform this action
        $ltr = LoginTicketRequestFactory::create(
            '',
            '',
            '',
            m::mock('AfipClient\Utils\FileManager'),
            m::mock('AfipClient\Clients\Auth\LoginTicketRequestSigner')
        );

        //the i expect this response
        $this->assertInstanceOf('AfipClient\Clients\Auth\LoginTicketRequest', $ltr);
    }

    /**
     * @expectedException \ArgumentCountError
     */
    public function testCreateRequiredDependencies()
    {
        LoginTicketRequestFactory::create();
    }
}
