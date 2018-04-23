<?php
namespace AfipClient\Clients\Biller;

use AfipClient\ACException;
use AfipClient\ACHelper;
use AfipClient\AuthParamsProvider;
use AfipClient\Clients\Client;
use AfipClient\Clients\Biller\BillerRequestManager;
use AfipClient\Clients\Biller\BillerResponseManager;

/**
 * Client de facturación electrónica, encargado de interactuar con api WSFEV1 de Afip
 */
class BillerClient extends Client
{
    protected $client_name = 'wsfe';
    protected $soap_client;
    protected $auth_params_provider;
    protected $request_manager;
    protected $response_manager;

    /**
     * @param SoapClient $soap_client SoapClientFactory::create( [wsdl], [end_point] )
     * @param AuthParamsProvider $acces_ticket_manager el objeto encargado de procesar y completar el AccessTicket
     * @param BillerRequestManager $request_manager el objeto encargado de manejar la consulta
     * @param BillerResponseManager $biller_response el objeto encargado de manejar la respuesta
     */
    public function __construct( 
                                 \SoapClient $soap_client,
                                 AuthParamsProvider $auth_params_provider,
                                 BillerRequestManager $request_manager,
                                 BillerResponseManager $response_manager
 
    ) {
        $this->soap_client = $soap_client;
        $this->auth_params_provider = $auth_params_provider;
        $this->request_manager = $request_manager;
        $this->response_manager = $response_manager;
    }

        
    /**
     * Solicitar cae y fecha de vencimiento al WS de facturacion
     * @param array $data
     * @return array [ string 'cae' => '',  \DateTime 'cae_validdate' => null,
     *                 int 'invoice_number' => 0, string 'tax_id' => '', \DateTime 'invoice_date' => null
     * 				   stdClass 'full_response' => null ]
     * @throws  ACException
     */
    public function requestCAE($data)
    {
        $request_params = $this->request_manager->buildCAEParams($this, $this->_getAuthParams(), $data);

        $response = $this->soap_client->FECAESolicitar($request_params);

        $parsed_data = $this->response_manager->validateAndParseCAERsp($response);

        if (!$parsed_data) {
            throw new ACException(
                "Error obteniendo CAE",
                $this,
                                   ACHelper::export_response($response)
            );
        }

        return $parsed_data;
    }

    /**
     * Obtiene el último número de comprobante autorizado
     * @param Array $data [ 'PtoVta' => '', 'CbteTipo' => '' ]
     * @return int
     */
    public function getLastAuthorizedDoc($data)
    {
        $request_params = $this->request_manager
                               ->buildLastAuthorizedDocParams($this->_getAuthParams(), $data);

        $response = $this->soap_client->FECompUltimoAutorizado($request_params);

        $doc_number = $this->response_manager
                           ->validateAndParseLastAuthorizedDocRsp($response);

        if (!$doc_number && $doc_number !== 0) {
            throw new ACException(
                "Error obteniendo ultimo número de comprobante autorizado",
                $this,
                                   ACHelper::export_response($response)
            );
        }

        return $doc_number;
    }

    /**
     * Obtiene puntos de centa autorizados
     * @return int
     */
    public function getAuthorizedSalePoint()
    {
        $request_params = $this->request_manager
                              ->buildAthorizedSalePointParams($this->_getAuthParams());

        $response = $this->soap_client->FEParamGetPtosVenta($request_params);

        $salepoint_num = $this->response_manager
                              ->validateAndParseAthorizedSalePoint($response);

        if (!$salepoint_num) {
            throw new ACException(
            
                "Error obteniendo ultimo número de comprobante autorizado",
            
                $this,
                                   ACHelper::export_response($response)
            
            );
        }
        
        return $salepoint_num;
    }

    /**
     * Devuelve array con datos de acceso consultando el AccessTicket
     * @return array ['token' => '', 'sign' => '', 'cuit' => '']
     */
    private function _getAuthParams()
    {
        return $this->auth_params_provider->getAuthParams($this);
    }
}
