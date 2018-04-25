<?php
use PHPUnit\Framework\TestCase;

use AfipClient\Clients\Biller\BillerClient;
use AfipClient\Clients\Auth\AccessTicketManager;
use AfipClient\Clients\Auth\AccessTicket;

use \Mockery as m;

class BillerClientTest extends TestCase
{
    private $biller;

    public function tearDown()
    {
        m::close();
    }

    public function setUp()
    {
        $this->biller = new BillerClient(
            m::mock('SoapClient'),
            m::mock('AfipClient\AuthParamsProvider'),
            m::mock('AfipClient\Clients\Biller\BillerRequestManager'),
            m::mock('AfipClient\Clients\Biller\BillerResponseManager')
        );
    }
    
    public function testInstance()
    {
        $this->assertInstanceOf('AfipClient\Clients\Biller\BillerClient', $this->biller);
    }

    /**
     * @expectedException \ArgumentCountError
     */
    public function testInstanceWithNoArguments()
    {
        new BillerClient();
    }

    public function testResquestCAE()
    {
        $provider_mock = m::mock('AfipClient\AuthParamsProvider');
        $provider_mock->shouldReceive(['getAuthParams' => ['auth_params'] ])
                      ->with('AfipClient\Clients\Biller\BillerClient')
                      ->once();

        $req_mock = m::mock('AfipClient\Clients\Biller\BillerRequestManager');
        $req_mock->shouldReceive('buildCAEParams')
                 ->once()
                 ->with('AfipClient\Clients\Biller\BillerClient', ['auth_params'], ['data'])
                 ->andReturn(['request']);

        $response = m::mock('stdClass');
        
        $soap_mock = m::mock('SoapClient');
        $soap_mock->shouldReceive('FECAESolicitar')
                  ->once()
                  ->with(['request'])
                  ->andReturn($response);


        $rsp_mock = m::mock('AfipClient\Clients\Biller\BillerResponseManager');
        $rsp_mock->shouldReceive('validateAndParseCAERsp')
                 ->once()
                 ->with($response)
                 ->andReturn(['cae']);

        $biller = new BillerClient(
            $soap_mock,
            $provider_mock,
            $req_mock,
            $rsp_mock
        );

        $this->assertEquals($biller->requestCAE(['data']), ['cae']);
    }

    /**
     * @expectedException AfipClient\ACException
     */
    public function testResquestCAEError()
    {
        $provider_mock = m::mock('AfipClient\AuthParamsProvider');
        $provider_mock->shouldReceive(['getAuthParams' => ['auth_params'] ])
                      ->with('AfipClient\Clients\Biller\BillerClient')
                      ->once();

        $req_mock = m::mock('AfipClient\Clients\Biller\BillerRequestManager');
        $req_mock->shouldReceive('buildCAEParams')
                 ->once()
                 ->with('AfipClient\Clients\Biller\BillerClient', ['auth_params'], ['data'])
                 ->andReturn(['request']);

        $response = m::mock('stdClass');
        
        $soap_mock = m::mock('SoapClient');
        $soap_mock->shouldReceive('FECAESolicitar')
                  ->once()
                  ->with(['request'])
                  ->andReturn($response);


        $rsp_mock = m::mock('AfipClient\Clients\Biller\BillerResponseManager');
        $rsp_mock->shouldReceive('validateAndParseCAERsp')
                 ->once()
                 ->with($response)
                 ->andReturn(false);

        $biller = new BillerClient(
            $soap_mock,
            $provider_mock,
            $req_mock,
            $rsp_mock
        );

        $biller->requestCAE(['data']);
    }


    public function testGetLastAuthorizedDoc()
    {
        $provider_mock = m::mock('AfipClient\AuthParamsProvider');
        $provider_mock->shouldReceive(['getAuthParams' => ['auth_params'] ])
                      ->with('AfipClient\Clients\Biller\BillerClient')
                      ->once();

        $req_mock = m::mock('AfipClient\Clients\Biller\BillerRequestManager');
        $req_mock->shouldReceive('buildLastAuthorizedDocParams')
                 ->once()
                 ->with(['auth_params'], ['data'])
                 ->andReturn(['request']);

        $response = m::mock('stdClass');
        
        $soap_mock = m::mock('SoapClient');
        $soap_mock->shouldReceive('FECompUltimoAutorizado')
                  ->once()
                  ->with(['request'])
                  ->andReturn($response);


        $rsp_mock = m::mock('AfipClient\Clients\Biller\BillerResponseManager');
        $rsp_mock->shouldReceive('validateAndParseLastAuthorizedDocRsp')
                 ->once()
                 ->with($response)
                 ->andReturn(1);

        $biller = new BillerClient(
            $soap_mock,
            $provider_mock,
            $req_mock,
            $rsp_mock
        );

        $this->assertEquals($biller->getLastAuthorizedDoc(['data']), 1);
    }


    /**
     * @expectedException AfipClient\ACException
     */
    public function testGetLastAuthorizedDocError()
    {
        $provider_mock = m::mock('AfipClient\AuthParamsProvider');
        $provider_mock->shouldReceive(['getAuthParams' => ['auth_params'] ])
                      ->with('AfipClient\Clients\Biller\BillerClient')
                      ->once();

        $req_mock = m::mock('AfipClient\Clients\Biller\BillerRequestManager');
        $req_mock->shouldReceive('buildLastAuthorizedDocParams')
                 ->once()
                 ->with(['auth_params'], ['data'])
                 ->andReturn(['request']);

        $response = m::mock('stdClass');
        
        $soap_mock = m::mock('SoapClient');
        $soap_mock->shouldReceive('FECompUltimoAutorizado')
                  ->once()
                  ->with(['request'])
                  ->andReturn($response);


        $rsp_mock = m::mock('AfipClient\Clients\Biller\BillerResponseManager');
        $rsp_mock->shouldReceive('validateAndParseLastAuthorizedDocRsp')
                 ->once()
                 ->with($response)
                 ->andReturn(false);

        $biller = new BillerClient(
            $soap_mock,
            $provider_mock,
            $req_mock,
            $rsp_mock
        );

        $biller->getLastAuthorizedDoc(['data']);
    }

    public function testGetAuthorizedSalePoint()
    {
        $provider_mock = m::mock('AfipClient\AuthParamsProvider');
        $provider_mock->shouldReceive(['getAuthParams' => ['auth_params'] ])
                      ->with('AfipClient\Clients\Biller\BillerClient')
                      ->once();

        $req_mock = m::mock('AfipClient\Clients\Biller\BillerRequestManager');
        $req_mock->shouldReceive('buildAuthorizedSalePointParams')
                 ->once()
                 ->with(['auth_params'])
                 ->andReturn(['request']);

        $response = m::mock('stdClass');
        
        $soap_mock = m::mock('SoapClient');
        $soap_mock->shouldReceive('FEParamGetPtosVenta')
                  ->once()
                  ->with(['request'])
                  ->andReturn($response);


        $rsp_mock = m::mock('AfipClient\Clients\Biller\BillerResponseManager');
        $rsp_mock->shouldReceive('validateAndParseAuthorizedSalePoint')
                 ->once()
                 ->with($response)
                 ->andReturn(1);

        $biller = new BillerClient(
            $soap_mock,
            $provider_mock,
            $req_mock,
            $rsp_mock
        );

        $this->assertEquals($biller->getAuthorizedSalePoint(['data']), 1);
    }


    /**
     * @expectedException AfipClient\ACException
     */
    public function testGetAuthorizedSalePointError()
    {
        $provider_mock = m::mock('AfipClient\AuthParamsProvider');
        $provider_mock->shouldReceive(['getAuthParams' => ['auth_params'] ])
                      ->with('AfipClient\Clients\Biller\BillerClient')
                      ->once();

        $req_mock = m::mock('AfipClient\Clients\Biller\BillerRequestManager');
        $req_mock->shouldReceive('buildAuthorizedSalePointParams')
                 ->once()
                 ->with(['auth_params'])
                 ->andReturn(['request']);

        $response = m::mock('stdClass');
        
        $soap_mock = m::mock('SoapClient');
        $soap_mock->shouldReceive('FEParamGetPtosVenta')
                  ->once()
                  ->with(['request'])
                  ->andReturn($response);


        $rsp_mock = m::mock('AfipClient\Clients\Biller\BillerResponseManager');
        $rsp_mock->shouldReceive('validateAndParseAuthorizedSalePoint')
                 ->once()
                 ->with($response)
                 ->andReturn(false);

        $biller = new BillerClient(
            $soap_mock,
            $provider_mock,
            $req_mock,
            $rsp_mock
        );

        $biller->getAuthorizedSalePoint(['data']);
    }
}
