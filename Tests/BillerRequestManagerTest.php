<?php
use PHPUnit\Framework\TestCase;
use AfipClient\Clients\Biller\BillerRequestManager;
use \Mockery as m;

class BillerRequestManagerTest extends TestCase
{
    private $request_manager;

    public function tearDown()
    {
        m::close();
    }

    public function setUp()
    {
        $this->request_manager = new BillerRequestManager();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('AfipClient\Clients\Biller\BillerRequestManager', $this->request_manager);
    }



    public function testbuildCAEParams()
    {
        $client_mock = m::mock('AfipClient\Clients\Biller\BillerClient');
        $client_mock->shouldNotReceive('getLastAuthorizedDoc');
        $client_mock->shouldNotReceive('getAuthorizedSalePoint');

        $auth_params = [];

        $data = [
            'CantReg' => 1,
            'PtoVta' => 1,
            'CbteTipo' => 1,
            'Concepto' => 1,
            'DocTipo' => 1,
            'DocNro' => '12',
            'CbteDesde' => 1,
            'CbteHasta' => 1,
            'CbteFch' => date('Ymd'),
            'ImpNeto' => 1,
            'ImpTotConc' => 1,
            'ImpIVA' => 1,
            'ImpTrib' => 1,
            'ImpOpEx' => 1,
            'ImpTotal' => 1,
            'FchServDesde' => date("Ymd"),
            'FchServHasta' => date("Ymd"),
            'FchVtoPago' => date("Ymd"),
            'MonId' => 'PES', //PES
            'MonCotiz' => 1, //1
        ];

        $rsp = $this->request_manager->buildCAEParams($client_mock, $auth_params, $data);

        $this->assertArrayHasKey('FeCAEReq', $rsp);
    }

    public function testbuildCAEParamsWithSomeAutoValues()
    {
        $auth_params = ['auth' => 'params'];

        $data = [
            'CantReg' => 1,
            'PtoVta' => null, //auto
            'CbteTipo' => 1,
            'Concepto' => 1,
            'DocTipo' => 1,
            'DocNro' => '12',
            'CbteDesde' => null, //auto
            'CbteHasta' => 1,
            'CbteFch' => date('Ymd'),
            'ImpNeto' => 1,
            'ImpTotConc' => 1,
            'ImpIVA' => 1,
            'ImpTrib' => 1,
            'ImpOpEx' => 1,
            'ImpTotal' => 1,
            'FchServDesde' => date("Ymd"),
            'FchServHasta' => date("Ymd"),
            'FchVtoPago' => date("Ymd"),
            'MonId' => 'PES', //PES
            'MonCotiz' => 1, //1
        ];

        $client_mock = m::mock('AfipClient\Clients\Biller\BillerClient');
        $client_mock->shouldReceive(['getLastAuthorizedDoc' => 1])
                    ->once()
                    ->with($data);

        $client_mock->shouldReceive(['getAuthorizedSalePoint' => 1])
                    ->once();

        $rsp = $this->request_manager->buildCAEParams($client_mock, $auth_params, $data);

        $this->assertArrayHasKey('FeCAEReq', $rsp);
        $this->assertEquals($rsp['FeCAEReq']['FeDetReq']['FECAEDetRequest']['CbteDesde'], 2);
        $this->assertEquals($rsp['Auth'], ['auth' => 'params']);
    }

    public function testBuildLastAuthorizedDocParams()
    {
        $auth_params = ['auth' => 'params'];
        $data = [
            'PtoVta' => 1,
            'CbteTipo' => 1
        ];

        $rsp = $this->request_manager->buildLastAuthorizedDocParams($auth_params, $data);

        $this->assertEquals($rsp, [
            'Auth' => ['auth' => 'params'], 'PtoVta' => 1, 'CbteTipo' => 1
        ]);
    }


    public function testBuildAuthorizedSalePointParams()
    {
        $auth_params = ['auth' => 'params'];
        
        $rsp = $this->request_manager->buildAuthorizedSalePointParams($auth_params);

        $this->assertEquals($rsp, [
            'Auth' => ['auth' => 'params']
        ]);
    }
}
