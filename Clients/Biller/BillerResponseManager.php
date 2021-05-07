<?php
namespace AfipClient\Clients\Biller;

use AfipClient\ACException;
use AfipClient\ACHelper;

/**
 * Clase encargada de manejar la respuesta de la api afip facturacion
 */
class BillerResponseManager
{


    /**
     * Parsea y prepara array para ser devuelto en cae request
     * @param Array $response
     * @return Array
     */
    public function validateAndParseCAERsp(\stdClass $response)
    {
        if (isset($response->FECAESolicitarResult->Errors) ||
            !isset($response->FECAESolicitarResult->FeDetResp->FECAEDetResponse->CAE)
            ) {
            return false;
        }

        $cae = (string) $response->FECAESolicitarResult->FeDetResp->FECAEDetResponse->CAE;
        $cae_validdate = (string) $response->FECAESolicitarResult->FeDetResp->FECAEDetResponse->CAEFchVto;
        $invoice_number = (int) $response->FECAESolicitarResult->FeDetResp->FECAEDetResponse->CbteDesde;
        $sale_point = (int) $response->FECAESolicitarResult->FeCabResp->PtoVta;
        $inv_type = (int) $response->FECAESolicitarResult->FeCabResp->CbteTipo;
        $tax_id = (string) $response->FECAESolicitarResult->FeDetResp->FECAEDetResponse->DocNro;
        $invoice_date = (string) $response->FECAESolicitarResult->FeDetResp->FECAEDetResponse->CbteFch;

        return [
            'cae' => $cae,
            'cae_validdate' => date_create_from_format('Ymd', $cae_validdate),
            'invoice_number' => $invoice_number,
            'sale_point' => $sale_point,
            'inv_type' => $inv_type,
            'invoice_date' => date_create_from_format('Ymd', $invoice_date),
            'tax_id' => $tax_id,
            'full_response' => ACHelper::export_response($response)
        ];
    }

    /**
     * Parsea y valida respuesta de api para obtener ultimo nro autorizado
     * @param Array $response
     * @return int|boolean
     */
    public function validateAndParseLastAuthorizedDocRsp(\stdClass $response)
    {
        if (isset($response->FECompUltimoAutorizadoResult->Errors)) {
            return false;
        } else {
            return intval($response->FECompUltimoAutorizadoResult->CbteNro);
        }
    }

    /**
     * Parsea y valida respuesta de api para obtener pto de venta autorizado
     * todo: multiples pto de venta?
     * @param Array $response
     * @return int|boolean
     */
    public function validateAndParseAuthorizedSalePoint(\stdClass $response)
    {
        if (isset($response->FEParamGetPtosVentaResult->Errors)) {
            return false;
        } else {
            return intval($response->FEParamGetPtosVentaResult->ResultGet->PtoVenta->Nro);
        }
    }
}
