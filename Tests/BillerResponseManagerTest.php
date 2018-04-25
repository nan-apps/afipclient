<?php
use PHPUnit\Framework\TestCase;
use AfipClient\Clients\Biller\BillerResponseManager;
use AfipClient\ACHelper;
use \Mockery as m;

class BillerResponseManagerTest extends TestCase
{
    private $response_manager;

    public function tearDown()
    {
        m::close();
    }

    public function setUp()
    {
        $this->response_manager = new BillerResponseManager();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('AfipClient\Clients\Biller\BillerResponseManager', $this->response_manager);
    }

    public function testValidateAndParseCAERspWithError()
    {
        $fake_response = (object)[
            'FECAESolicitarResult' => (object) [
                'Errors' => (object) []
            ]
        ];

        $this->assertFalse($this->response_manager->validateAndParseCAERsp($fake_response));
    }

    public function testValidateAndParseCAERspWithNoCae()
    {
        $fake_response = (object) [
            'FECAESolicitarResult' => (object) [
                'FeDetResp' => (object) [
                    'FECAEDetResponse' => (object) [
                        'NO_CAE' => ''
                    ]
                ]
            ]
        ];

        $this->assertFalse($this->response_manager->validateAndParseCAERsp($fake_response));
    }

    public function testValidateAndParseCAERsp()
    {
        $cae = '123456789';
        $cae_validdate = '20170707';
        $invoice_number = 1;
        $tax_id = '123456';
        $invoice_date = '20170707';
        $sale_point = 1;
        $inv_type = 6;

        $fake_response = (object) [
            'FECAESolicitarResult' => (object) [
                'FeCabResp' => (object) [
                    'PtoVta' => $sale_point,
                    'CbteTipo' => $inv_type
                ],
                'FeDetResp' => (object) [
                    'FECAEDetResponse' => (object) [
                        'CAE' => $cae,
                        'CAEFchVto' => $cae_validdate,
                        'CbteDesde' => $invoice_number,
                        'DocNro' => $tax_id,
                        'CbteFch' => $invoice_date
                    ]
                ],
            ]
        ];

        $this->assertEquals(
            $this->response_manager
                 ->validateAndParseCAERsp($fake_response),
            [
                'cae' => $cae,
                'cae_validdate' => date_create_from_format('Ymd', $cae_validdate),
                'invoice_number' => $invoice_number,
                'sale_point' => $sale_point,
                'inv_type' => $inv_type,
                'invoice_date' => date_create_from_format('Ymd', $invoice_date),
                'tax_id' => $tax_id,
                'full_response' => ACHelper::export_response($fake_response)
            ]
        );
    }

    public function testValidateAndParseLastAuthorizedDocRspWithErrors()
    {
        $fake_response = (object)[
            'FECompUltimoAutorizadoResult' => (object) [
                'Errors' => (object) []
            ]
        ];

        $this->assertFalse($this->response_manager->validateAndParseLastAuthorizedDocRsp($fake_response));
    }

    public function testValidateAndParseLastAuthorizedDocRsp()
    {
        $fake_response = (object)[
            'FECompUltimoAutorizadoResult' => (object) [
                'CbteNro' => 1
            ]
        ];

        $this->assertEquals($this->response_manager->validateAndParseLastAuthorizedDocRsp($fake_response), 1);
    }

    public function testValidateAndParseAuthorizedSalePointWithErrors()
    {
        $fake_response = (object)[
            'FEParamGetPtosVentaResult' => (object) [
                'Errors' => (object) []
            ]
        ];

        $this->assertFalse($this->response_manager->validateAndParseAuthorizedSalePoint($fake_response));
    }

    public function testValidateAndParseAuthorizedSalePoint()
    {
        $fake_response = (object)[
            'FEParamGetPtosVentaResult' => (object) [
                'ResultGet' => (object) [
                    'PtoVenta' => (object) [
                        'Nro' => 1
                    ]
                ]
            ]
        ];

        $this->assertEquals($this->response_manager->validateAndParseAuthorizedSalePoint($fake_response), 1);
    }
}
