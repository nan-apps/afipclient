<?php
use PHPUnit\Framework\TestCase;
use AfipClient\Clients\Auth\AccessTicket;

class AccessTicketTest extends TestCase
{
    public function testInstance()
    {
        $at = new AccessTicket();

        $this->assertInstanceOf('AfipClient\Clients\Auth\AccessTicket', $at);
    }

    public function testShouldBeEmpty()
    {
        $at = new AccessTicket();

        $this->assertTrue($at->isEmpty());
    }

    public function testShouldNotBeEmpty()
    {
        $at = new AccessTicket('cuit', 'token', 'sign', 'generation_time', 'expiration_time');

        $this->assertTrue(!$at->isEmpty());
    }

    public function testShouldShouldBeExpired()
    {
        $at = new AccessTicket('cuit', 'token', 'sign', 'generation_time', 'expiration_time');

        $this->assertTrue($at->isExpired());
    }

    public function testShouldShouldNotBeExpired()
    {
        $at = new AccessTicket('cuit', 'token', 'sign', 'generation_time', date('c', time()+1000));

        $this->assertTrue(!$at->isExpired());
    }
}
