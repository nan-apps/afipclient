<?php
namespace AfipClient\Clients\Biller;

use AfipClient\ACException;
use AfipClient\Clients\Biller\BillerClient;

/**
 * Clase encargada de manejar la consulta a enviar a al api
 */
class BillerRequestManager
{

    /**
     * Armar el array para ser enviado al cliente y solicitar el cae
     * @param BillerClient $biller_client
     * @param array $data
     * @param array $auth_params
     * @return array $params
     */
    public function buildCAEParams(BillerClient $biller_client, $auth_params, $data)
    {
        if (empty($data['CbteDesde'])) {
            $last_invoice_number = $biller_client->getLastAuthorizedDoc($data);
            $invoice_number = $last_invoice_number + 1;
        } else {
            $invoice_number = $data['CbteDesde'];
        }

        $sale_point = !empty($data['PtoVta']) ? $data['PtoVta'] : $biller_client->getAuthorizedSalePoint();

        $params = [
            'Auth' => $auth_params,
            'FeCAEReq' =>
                [
                    'FeCabReq' =>
                        [  'CantReg' => $data['CantReg'],
                                'PtoVta' => $sale_point,
                                'CbteTipo' => $data['CbteTipo']
                        ],
                    'FeDetReq' =>
                        [ 'FECAEDetRequest' =>
                            [        'Concepto' => $data['Concepto'],
                                    'DocTipo' => $data['DocTipo'],
                                    'DocNro' => $data['DocNro'],
                                    'CbteDesde' => $invoice_number,
                                    'CbteHasta' => $invoice_number,
                                    'CbteFch' => $data['CbteFch'],
                                    'ImpNeto' => $data['ImpNeto'],
                                    'ImpTotConc' => $data['ImpTotConc'],
                                    'ImpIVA' => $data['ImpIVA'],
                                    'ImpTrib' => $data['ImpTrib'],
                                    'ImpOpEx' => $data['ImpOpEx'],
                                    'ImpTotal' => $data['ImpTotal'],
                                    'FchServDesde' => isset($data['FchServDesde']) ? $data['FchServDesde'] : null,
                                    'FchServHasta' => isset($data['FchServHasta']) ? $data['FchServHasta'] : null,
                                    'FchVtoPago' => isset($data['FchVtoPago']) ? $data['FchVtoPago'] : null,
                                    'MonId' => $data['MonId'],
                                    'MonCotiz' => $data['MonCotiz'],
                                ],
                            ],
                        ],
        ];

        if ($data['ImpIVA']) {
            $params['FeCAEReq']['FeDetReq']['ImpIVA'] = [
                //alicuotas
            ];
        }

        if ($data['ImpTrib']) {
            $params['FeCAEReq']['FeDetReq']['Tributos'] = [
                //tributos
            ];
        }

        return $params;
    }

    /**
     * Armar el array para ser enviado al cliente y solicitar ultimo numero autorizado
     * @param array $data
     * @param array $auth_params
     * @return array $params
     */
    public function buildLastAuthorizedDocParams($auth_params, $data)
    {
        return [ 'Auth' => $auth_params,
                 'PtoVta' => $data['PtoVta'],
                 'CbteTipo' => $data['CbteTipo'] ];
    }

    /**
     * Armar el array para ser enviado al cliente y solicitar pto venta habilitado
     * @param array $auth_params
     * @return array $params
     */
    public function buildAthorizedSalePointParams($auth_params)
    {
        return [ 'Auth' => $auth_params ];
    }
}
