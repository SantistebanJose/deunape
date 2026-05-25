<?php
/**
 * clssSunat.php
 * Clase reutilizable para generar, firmar y enviar comprobantes a SUNAT.
 * Soporta: Boleta (03), Factura (01), Nota de Crédito (07)
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class SunatComprobante
{
    private string $base_dir;
    public string $version = 'v6';


    public function __construct()
    {
        $this->base_dir = rtrim(dirname(__DIR__), '/') . '/';
    }

    // =========================================================
    // PUNTO DE ENTRADA PRINCIPAL
    // =========================================================
    public function enviar(array $emisor, array $cliente, array $cabecera, array $items): array
    {
        // FIX: forzar correlativo como string con ceros para evitar conversión numérica
        $cabecera['correlativo'] = str_pad((string)(int)$cabecera['correlativo'], 8, '0', STR_PAD_LEFT);

        try {
            $sucursal   = $emisor['sucursal'] ?? '1';
            $base       = $this->base_dir . "sucursales/{$sucursal}/";
            $esRender   = getenv('RENDER') === 'true' || !is_writable(dirname($base));
            $carpetaxml = $esRender ? "/tmp/xml_{$sucursal}/" : $base . "xml/";
            $carpetacdr = $esRender ? "/tmp/cdr_{$sucursal}/" : $base . "cdr/";

            if (!is_dir($carpetaxml)) mkdir($carpetaxml, 0777, true);
            if (!is_dir($carpetacdr)) mkdir($carpetacdr, 0777, true);

            $tipo      = $cabecera['tipo_comprobante'];
            $nombrexml = $emisor['ruc'] . "-" . $tipo . "-" . $cabecera['serie'] . "-" . $cabecera['correlativo'];
            $this->ultima_ruta_xml = $carpetaxml;
            $rutaXML   = $carpetaxml . $nombrexml . '.XML';

            // PASO 01: Generar XML
            $xml = match ($tipo) {
                '07'    => $this->generarXMLNotaCredito($emisor, $cliente, $cabecera, $items),
                default => $this->generarXMLInvoice($emisor, $cliente, $cabecera, $items),
            };

            $doc = new DOMDocument('1.0', 'utf-8');
            $doc->formatOutput       = false;
            $doc->preserveWhiteSpace = true;
            $doc->loadXML($xml);
            $doc->save($rutaXML);

            // PASO 02: Firmar
            $firma = $this->firmar($rutaXML, $emisor);
            if (!$firma['ok']) {
                return ['estado' => false, 'mensaje' => $firma['mensaje']];
            }

            // PASO 03: ZIP
            $nombrezip = $nombrexml . ".ZIP";
            $rutazip   = $carpetaxml . $nombrezip;

            if (!$this->crearZip($rutazip, $rutaXML, $nombrexml . '.XML')) {
                return ['estado' => false, 'mensaje' => 'No se pudo crear el archivo ZIP'];
            }

            // PASO 04: Enviar a SUNAT
            $resp = $this->enviarWS($rutazip, $emisor, $nombrezip);

            // PASO 05: Procesar CDR
            if ($resp['http_code'] == 200) {
                $docResp = new DOMDocument();
                $docResp->loadXML($resp['body']);
                $appResp = $docResp->getElementsByTagName('applicationResponse')->item(0);

                if ($appResp && $appResp->nodeValue) {
                    $cdr = base64_decode($appResp->nodeValue);
                    file_put_contents($carpetacdr . "R-" . $nombrezip, $cdr);

                    if (class_exists('ZipArchive')) {
                        $zipCdr = new ZipArchive();
                        if ($zipCdr->open($carpetacdr . "R-" . $nombrezip) === true) {
                            $zipCdr->extractTo($carpetacdr . 'R-' . $nombrexml);
                            $zipCdr->close();
                        }
                    } else {
                        $carpetaExtr = $carpetacdr . 'R-' . $nombrexml;
                        if (!is_dir($carpetaExtr)) mkdir($carpetaExtr, 0777, true);
                        $proc = proc_open(
                            'unzip -o ' . escapeshellarg($carpetacdr . "R-" . $nombrezip) . ' -d ' . escapeshellarg($carpetaExtr),
                            [['pipe','r'],['pipe','w'],['pipe','w']], $pipes
                        );
                        if (is_resource($proc)) proc_close($proc);
                    }

                    $msgSunat = $this->leerMensajeCDR($carpetacdr . 'R-' . $nombrexml);

                    return [
                        'estado'        => true,
                        'mensaje'       => 'Comprobante enviado correctamente a SUNAT',
                        'codigo_sunat'  => '0',
                        'mensaje_sunat' => $msgSunat ?: '',
                        'nombrexml'     => $nombrexml,
                        'nombrezip'     => $nombrezip,
                    ];
                } else {
                    $code = $docResp->getElementsByTagName('faultcode')->item(0)->nodeValue ?? '';
                    $msg  = $docResp->getElementsByTagName('faultstring')->item(0)->nodeValue ?? '';
                    return ['estado' => false, 'mensaje' => "Error SUNAT [{$code}]: {$msg}"];
                }
            } else {
                return [
                    'estado'  => false,
                    'mensaje' => 'Error de conexión: HTTP ' . $resp['http_code']
                        . ' | cURL: ' . ($resp['error'] ?: 'sin error curl')
                        . ' | Body: ' . substr($resp['body'] ?: '', 0, 2000),
                ];
            }
        } catch (Throwable $e) {
            return ['estado' => false, 'mensaje' => 'Excepción: ' . $e->getMessage()];
        }
    }

    // =========================================================
    // XML — INVOICE (Boleta 03 y Factura 01)
    // =========================================================
    private function generarXMLInvoice(array $emisor, array $cliente, array $cabecera, array $items): string
    {
        $moneda = $cabecera['moneda'] ?? 'PEN';

        $xml  = '<?xml version="1.0" encoding="utf-8"?>';
        $xml .= '<Invoice xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
     xmlns:xsd="http://www.w3.org/2001/XMLSchema"
     xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
     xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
     xmlns:ccts="urn:un:unece:uncefact:documentation:2"
     xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
     xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"
     xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2"
     xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2"
     xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2">
    <ext:UBLExtensions>
        <ext:UBLExtension><ext:ExtensionContent/></ext:UBLExtension>
    </ext:UBLExtensions>
    <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
    <cbc:CustomizationID schemeAgencyName="PE:SUNAT">2.0</cbc:CustomizationID>
    <cbc:ProfileID schemeName="Tipo de Operacion" schemeAgencyName="PE:SUNAT"
        schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo17">' . $cabecera['tipo_operacion'] . '</cbc:ProfileID>
    <cbc:ID>' . $cabecera['serie'] . '-' . $cabecera['correlativo'] . '</cbc:ID>
    <cbc:IssueDate>' . $cabecera['fecha_emision'] . '</cbc:IssueDate>
    <cbc:IssueTime>' . substr($cabecera['hora_emision'], 0, 8) . '</cbc:IssueTime>
    <cbc:InvoiceTypeCode listAgencyName="PE:SUNAT" listName="Tipo de Documento"
        listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01"
        listID="0101" name="Tipo de Operacion">' . $cabecera['tipo_comprobante'] . '</cbc:InvoiceTypeCode>
    <cbc:DocumentCurrencyCode listID="ISO 4217 Alpha" listName="Currency"
        listAgencyName="United Nations Economic Commission for Europe">' . $moneda . '</cbc:DocumentCurrencyCode>
    <cbc:LineCountNumeric>' . count($items) . '</cbc:LineCountNumeric>';

        $xml .= $this->xmlSignature($emisor, $cabecera);
        $xml .= $this->xmlSupplier($emisor);
        $xml .= $this->xmlCustomer($cliente);
        $xml .= $this->xmlTaxTotal($cabecera, $moneda);
        $xml .= $this->xmlLegalMonetaryTotal($cabecera, $moneda);

        foreach ($items as $v) {
            $xml .= $this->xmlInvoiceLine($v, $moneda);
        }

        $xml .= '</Invoice>';
        return $xml;
    }

    // =========================================================
    // XML — NOTA DE CRÉDITO (07)
    // =========================================================
    private function generarXMLNotaCredito(array $emisor, array $cliente, array $cabecera, array $items): string
    {
        $moneda = $cabecera['moneda'] ?? 'PEN';

        $xml  = '<?xml version="1.0" encoding="utf-8"?>';
        $xml .= '<CreditNote xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
     xmlns:xsd="http://www.w3.org/2001/XMLSchema"
     xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
     xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
     xmlns:ccts="urn:un:unece:uncefact:documentation:2"
     xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
     xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"
     xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2"
     xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2"
     xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2">
    <ext:UBLExtensions>
        <ext:UBLExtension><ext:ExtensionContent/></ext:UBLExtension>
    </ext:UBLExtensions>
    <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
    <cbc:CustomizationID schemeAgencyName="PE:SUNAT">2.0</cbc:CustomizationID>
    <cbc:ID>' . $cabecera['serie'] . '-' . $cabecera['correlativo'] . '</cbc:ID>
    <cbc:IssueDate>' . $cabecera['fecha_emision'] . '</cbc:IssueDate>
    <cbc:IssueTime>' . substr($cabecera['hora_emision'], 0, 8) . '</cbc:IssueTime>
    <cbc:DocumentCurrencyCode listID="ISO 4217 Alpha" listName="Currency"
        listAgencyName="United Nations Economic Commission for Europe">' . $moneda . '</cbc:DocumentCurrencyCode>
    <cbc:LineCountNumeric>' . count($items) . '</cbc:LineCountNumeric>
    <cac:DiscrepancyResponse>
        <cbc:ReferenceID>' . $cabecera['serie_ref'] . '-' . $cabecera['correlativo_ref'] . '</cbc:ReferenceID>
        <cbc:ResponseCode listAgencyName="PE:SUNAT" listName="Tipo de nota de credito"
            listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo09">' . ($cabecera['tipo_nota'] ?? '01') . '</cbc:ResponseCode>
        <cbc:Description><![CDATA[' . ($cabecera['motivo_nota'] ?? 'Anulacion de la operacion') . ']]></cbc:Description>
    </cac:DiscrepancyResponse>
    <cac:BillingReference>
        <cac:InvoiceDocumentReference>
            <cbc:ID>' . $cabecera['serie_ref'] . '-' . $cabecera['correlativo_ref'] . '</cbc:ID>
            <cbc:DocumentTypeCode listAgencyName="PE:SUNAT" listName="Tipo de Documento"
                listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01">' . ($cabecera['tipo_comp_ref'] ?? '03') . '</cbc:DocumentTypeCode>
        </cac:InvoiceDocumentReference>
    </cac:BillingReference>';

        $xml .= $this->xmlSignature($emisor, $cabecera);
        $xml .= $this->xmlSupplier($emisor);
        $xml .= $this->xmlCustomer($cliente);
        $xml .= $this->xmlTaxTotal($cabecera, $moneda);
        $xml .= $this->xmlLegalMonetaryTotal($cabecera, $moneda);

        foreach ($items as $v) {
            $xml .= $this->xmlCreditNoteLine($v, $moneda);
        }

        $xml .= '</CreditNote>';
        return $xml;
    }

    // =========================================================
    // BLOQUES XML COMUNES
    // =========================================================
    private function xmlSignature(array $emisor, array $cabecera): string
    {
        return '
    <cac:Signature>
        <cbc:ID>' . $cabecera['serie'] . '-' . $cabecera['correlativo'] . '</cbc:ID>
        <cac:SignatoryParty>
            <cac:PartyIdentification>
                <cbc:ID>' . $emisor['ruc'] . '</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><![CDATA[' . $emisor['razon_social'] . ']]></cbc:Name>
            </cac:PartyName>
        </cac:SignatoryParty>
        <cac:DigitalSignatureAttachment>
            <cac:ExternalReference>
                <cbc:URI>#SignatureSP</cbc:URI>
            </cac:ExternalReference>
        </cac:DigitalSignatureAttachment>
    </cac:Signature>';
    }

    private function xmlSupplier(array $e): string
    {
        $td = '6'; // Emisor siempre RUC
        return '
    <cac:AccountingSupplierParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="' . $td . '" schemeName="Documento de Identidad"
                    schemeAgencyName="PE:SUNAT"
                    schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . htmlspecialchars($e['ruc']) . '</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><![CDATA[' . $e['razon_social'] . ']]></cbc:Name>
            </cac:PartyName>
            <cac:PartyTaxScheme>
                <cbc:RegistrationName><![CDATA[' . $e['razon_social'] . ']]></cbc:RegistrationName>
                <cbc:CompanyID schemeID="' . $td . '" schemeName="SUNAT:Identificador de Documento de Identidad"
                    schemeAgencyName="PE:SUNAT"
                    schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . htmlspecialchars($e['ruc']) . '</cbc:CompanyID>
                <cac:TaxScheme>
                    <cbc:ID schemeID="' . $td . '" schemeName="SUNAT:Identificador de Documento de Identidad"
                        schemeAgencyName="PE:SUNAT"
                        schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . htmlspecialchars($e['ruc']) . '</cbc:ID>
                </cac:TaxScheme>
            </cac:PartyTaxScheme>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA[' . $e['razon_social'] . ']]></cbc:RegistrationName>
                <cac:RegistrationAddress>
                    <cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI">' . $e['ubigeo'] . '</cbc:ID>
                    <cbc:AddressTypeCode listAgencyName="PE:SUNAT" listName="Establecimientos anexos">0000</cbc:AddressTypeCode>
                    <cbc:CityName><![CDATA[' . $e['provincia'] . ']]></cbc:CityName>
                    <cbc:CountrySubentity><![CDATA[' . $e['departamento'] . ']]></cbc:CountrySubentity>
                    <cbc:District><![CDATA[' . $e['distrito'] . ']]></cbc:District>
                    <cac:AddressLine>
                        <cbc:Line><![CDATA[' . $e['direccion'] . ']]></cbc:Line>
                    </cac:AddressLine>
                    <cac:Country>
                        <cbc:IdentificationCode listID="ISO 3166-1"
                            listAgencyName="United Nations Economic Commission for Europe"
                            listName="Country">PE</cbc:IdentificationCode>
                    </cac:Country>
                </cac:RegistrationAddress>
            </cac:PartyLegalEntity>
            <cac:Contact>
                <cbc:Name><![CDATA[' . ($e['telefono'] ?? '') . ']]></cbc:Name>
            </cac:Contact>
        </cac:Party>
    </cac:AccountingSupplierParty>';
    }

    private function xmlCustomer(array $c): string
    {
        $td     = (string)($c['tipo_documento'] ?? '1');
        $numDoc = $c['numero_doc_cliente'] ?? $c['ruc'] ?? '';
        $nombre = $c['razon_social'] ?? $c['cliente'] ?? '';
        $dir    = $c['direccion'] ?? '';

        return '
    <cac:AccountingCustomerParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="' . $td . '" schemeName="Documento de Identidad"
                    schemeAgencyName="PE:SUNAT"
                    schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $numDoc . '</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><![CDATA[' . $nombre . ']]></cbc:Name>
            </cac:PartyName>
            <cac:PartyTaxScheme>
                <cbc:RegistrationName><![CDATA[' . $nombre . ']]></cbc:RegistrationName>
                <cbc:CompanyID schemeID="' . $td . '" schemeName="SUNAT:Identificador de Documento de Identidad"
                    schemeAgencyName="PE:SUNAT"
                    schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $numDoc . '</cbc:CompanyID>
                <cac:TaxScheme>
                    <cbc:ID schemeID="' . $td . '" schemeName="SUNAT:Identificador de Documento de Identidad"
                        schemeAgencyName="PE:SUNAT"
                        schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $numDoc . '</cbc:ID>
                </cac:TaxScheme>
            </cac:PartyTaxScheme>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA[' . $nombre . ']]></cbc:RegistrationName>
                <cac:RegistrationAddress>
                    <cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI"/>
                    <cbc:CityName><![CDATA[]]></cbc:CityName>
                    <cbc:CountrySubentity><![CDATA[]]></cbc:CountrySubentity>
                    <cbc:District><![CDATA[]]></cbc:District>
                    <cac:AddressLine>
                        <cbc:Line><![CDATA[' . $dir . ']]></cbc:Line>
                    </cac:AddressLine>
                    <cac:Country>
                        <cbc:IdentificationCode listID="ISO 3166-1"
                            listAgencyName="United Nations Economic Commission for Europe"
                            listName="Country"/>
                    </cac:Country>
                </cac:RegistrationAddress>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingCustomerParty>';
    }

    private function xmlTaxTotal(array $cab, string $moneda): string
    {
        $n = fn($v) => number_format((float)($v ?? 0), 2, '.', '');

        $xml = '
    <cac:TaxTotal>
        <cbc:TaxAmount currencyID="' . $moneda . '">' . $n($cab['total_impuestos']) . '</cbc:TaxAmount>
        <cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID="' . $moneda . '">' . $n($cab['total_op_gravadas']) . '</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="' . $moneda . '">' . $n($cab['igv']) . '</cbc:TaxAmount>
            <cac:TaxCategory>
                <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier"
                    schemeAgencyName="United Nations Economic Commission for Europe">S</cbc:ID>
                <cac:TaxScheme>
                    <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">1000</cbc:ID>
                    <cbc:Name>IGV</cbc:Name>
                    <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>';

        if ((float)($cab['total_op_exoneradas'] ?? 0) > 0) {
            $xml .= '
        <cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID="' . $moneda . '">' . $n($cab['total_op_exoneradas']) . '</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="' . $moneda . '">0.00</cbc:TaxAmount>
            <cac:TaxCategory>
                <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier"
                    schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                <cac:TaxScheme>
                    <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9997</cbc:ID>
                    <cbc:Name>EXO</cbc:Name>
                    <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>';
        }

        if ((float)($cab['total_op_inafectas'] ?? 0) > 0) {
            $xml .= '
        <cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID="' . $moneda . '">' . $n($cab['total_op_inafectas']) . '</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="' . $moneda . '">0.00</cbc:TaxAmount>
            <cac:TaxCategory>
                <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier"
                    schemeAgencyName="United Nations Economic Commission for Europe">O</cbc:ID>
                <cac:TaxScheme>
                    <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9998</cbc:ID>
                    <cbc:Name>INA</cbc:Name>
                    <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>';
        }

        if ((float)($cab['icbper'] ?? 0) > 0) {
            $xml .= '
        <cac:TaxSubtotal>
            <cbc:TaxAmount currencyID="' . $moneda . '">' . $n($cab['icbper']) . '</cbc:TaxAmount>
            <cac:TaxCategory>
                <cac:TaxScheme>
                    <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">7152</cbc:ID>
                    <cbc:Name>ICBPER</cbc:Name>
                    <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>';
        }

        $xml .= '
    </cac:TaxTotal>';
        return $xml;
    }

    private function xmlLegalMonetaryTotal(array $cab, string $moneda): string
    {
        $n = fn($v) => number_format((float)($v ?? 0), 2, '.', '');
        return '
    <cac:LegalMonetaryTotal>
        <cbc:LineExtensionAmount currencyID="' . $moneda . '">' . $n($cab['total_antes_impuestos']) . '</cbc:LineExtensionAmount>
        <cbc:TaxInclusiveAmount currencyID="' . $moneda . '">' . $n($cab['total_despues_impuestos']) . '</cbc:TaxInclusiveAmount>
        <cbc:PayableAmount currencyID="' . $moneda . '">' . $n($cab['total_a_pagar']) . '</cbc:PayableAmount>
    </cac:LegalMonetaryTotal>';
    }

    private function xmlInvoiceLine(array $v, string $moneda): string
    {
        $n       = fn($val) => number_format((float)($val ?? 0), 2, '.', '');
        $codigos = $this->resolverCodigosImpuesto($v);
        $unidad  = $v['unidad'] ?? 'NIU';

        $xml = '
    <cac:InvoiceLine>
        <cbc:ID>' . $v['item'] . '</cbc:ID>
        <cbc:InvoicedQuantity unitCode="' . $unidad . '">' . $v['cantidad'] . '</cbc:InvoicedQuantity>
        <cbc:LineExtensionAmount currencyID="' . $moneda . '">' . $n($v['total_antes_impuestos'] ?? $v['valor_total'] ?? 0) . '</cbc:LineExtensionAmount>
        <cac:PricingReference>
            <cac:AlternativeConditionPrice>
                <cbc:PriceAmount currencyID="' . $moneda . '">' . $n($v['precio_lista'] ?? $v['pu_con_igv'] ?? ($v['valor_unitario'] * 1.18)) . '</cbc:PriceAmount>
                <cbc:PriceTypeCode listName="Tipo de Precio" listAgencyName="PE:SUNAT"
                    listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo16">01</cbc:PriceTypeCode>
            </cac:AlternativeConditionPrice>
        </cac:PricingReference>
        <cac:TaxTotal>
            <cbc:TaxAmount currencyID="' . $moneda . '">' . $n($v['total_impuestos'] ?? $v['igv'] ?? 0) . '</cbc:TaxAmount>
            <cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID="' . $moneda . '">' . $n($v['valor_total'] ?? 0) . '</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID="' . $moneda . '">' . $n($v['igv'] ?? 0) . '</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier"
                        schemeAgencyName="United Nations Economic Commission for Europe">' . $codigos['cat_id'] . '</cbc:ID>
                    <cbc:Percent>' . $codigos['porcentaje'] . '</cbc:Percent>
                    <cbc:TaxExemptionReasonCode listAgencyName="PE:SUNAT" listName="Afectacion del IGV"
                        listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo07">' . $codigos['razon'] . '</cbc:TaxExemptionReasonCode>
                    <cac:TaxScheme>
                        <cbc:ID schemeID="UN/ECE 5153" schemeName="Codigo de tributos"
                            schemeAgencyName="PE:SUNAT">' . $codigos['cod_tributo'] . '</cbc:ID>
                        <cbc:Name>' . $codigos['nombre_tributo'] . '</cbc:Name>
                        <cbc:TaxTypeCode>' . $codigos['tipo_codigo'] . '</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>';

        if ((float)($v['icbper'] ?? 0) > 0) {
            $xml .= '
            <cac:TaxSubtotal>
                <cbc:TaxAmount currencyID="' . $moneda . '">' . $n($v['icbper']) . '</cbc:TaxAmount>
                <cbc:BaseUnitMeasure unitCode="' . $unidad . '">' . $v['cantidad'] . '</cbc:BaseUnitMeasure>
                <cac:TaxCategory>
                    <cbc:PerUnitAmount currencyID="' . $moneda . '">' . $n($v['factor_icbper'] ?? 0.50) . '</cbc:PerUnitAmount>
                    <cac:TaxScheme>
                        <cbc:ID>7152</cbc:ID>
                        <cbc:Name>ICBPER</cbc:Name>
                        <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>';
        }

        $xml .= '
        </cac:TaxTotal>
        <cac:Item>
            <cbc:Description><![CDATA[' . $v['nombre'] . ']]></cbc:Description>
            <cac:SellersItemIdentification>
                <cbc:ID>' . htmlspecialchars($v['codigo_producto'] ?? '001') . '</cbc:ID>
            </cac:SellersItemIdentification>
            <cac:CommodityClassification>
                <cbc:ItemClassificationCode listID="UNSPSC" listAgencyName="GS1 US"
                    listName="Item Classification">' . ($v['clasificacion'] ?? '10191509') . '</cbc:ItemClassificationCode>
            </cac:CommodityClassification>
        </cac:Item>
        <cac:Price>
            <cbc:PriceAmount currencyID="' . $moneda . '">' . $n($v['valor_unitario'] ?? 0) . '</cbc:PriceAmount>
        </cac:Price>
    </cac:InvoiceLine>';

        return $xml;
    }

    private function xmlCreditNoteLine(array $v, string $moneda): string
    {
        $n       = fn($val) => number_format((float)($val ?? 0), 2, '.', '');
        $codigos = $this->resolverCodigosImpuesto($v);
        $unidad  = $v['unidad'] ?? 'NIU';

        return '
    <cac:CreditNoteLine>
        <cbc:ID>' . $v['item'] . '</cbc:ID>
        <cbc:CreditedQuantity unitCode="' . $unidad . '">' . $v['cantidad'] . '</cbc:CreditedQuantity>
        <cbc:LineExtensionAmount currencyID="' . $moneda . '">' . $n($v['total_antes_impuestos'] ?? $v['valor_total'] ?? 0) . '</cbc:LineExtensionAmount>
        <cac:PricingReference>
            <cac:AlternativeConditionPrice>
                <cbc:PriceAmount currencyID="' . $moneda . '">' . $n($v['precio_lista'] ?? ($v['valor_unitario'] * 1.18)) . '</cbc:PriceAmount>
                <cbc:PriceTypeCode listName="Tipo de Precio" listAgencyName="PE:SUNAT"
                    listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo16">01</cbc:PriceTypeCode>
            </cac:AlternativeConditionPrice>
        </cac:PricingReference>
        <cac:TaxTotal>
            <cbc:TaxAmount currencyID="' . $moneda . '">' . $n($v['total_impuestos'] ?? $v['igv'] ?? 0) . '</cbc:TaxAmount>
            <cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID="' . $moneda . '">' . $n($v['valor_total'] ?? 0) . '</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID="' . $moneda . '">' . $n($v['igv'] ?? 0) . '</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier"
                        schemeAgencyName="United Nations Economic Commission for Europe">' . $codigos['cat_id'] . '</cbc:ID>
                    <cbc:Percent>' . $codigos['porcentaje'] . '</cbc:Percent>
                    <cbc:TaxExemptionReasonCode listAgencyName="PE:SUNAT" listName="Afectacion del IGV"
                        listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo07">' . $codigos['razon'] . '</cbc:TaxExemptionReasonCode>
                    <cac:TaxScheme>
                        <cbc:ID schemeID="UN/ECE 5153" schemeName="Codigo de tributos"
                            schemeAgencyName="PE:SUNAT">' . $codigos['cod_tributo'] . '</cbc:ID>
                        <cbc:Name>' . $codigos['nombre_tributo'] . '</cbc:Name>
                        <cbc:TaxTypeCode>' . $codigos['tipo_codigo'] . '</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        </cac:TaxTotal>
        <cac:Item>
            <cbc:Description><![CDATA[' . $v['nombre'] . ']]></cbc:Description>
            <cac:SellersItemIdentification>
                <cbc:ID>' . htmlspecialchars($v['codigo_producto'] ?? '001') . '</cbc:ID>
            </cac:SellersItemIdentification>
            <cac:CommodityClassification>
                <cbc:ItemClassificationCode listID="UNSPSC" listAgencyName="GS1 US"
                    listName="Item Classification">' . ($v['clasificacion'] ?? '10191509') . '</cbc:ItemClassificationCode>
            </cac:CommodityClassification>
        </cac:Item>
        <cac:Price>
            <cbc:PriceAmount currencyID="' . $moneda . '">' . $n($v['valor_unitario'] ?? 0) . '</cbc:PriceAmount>
        </cac:Price>
    </cac:CreditNoteLine>';
    }

    // =========================================================
    // RESOLVER CÓDIGOS DE IMPUESTO
    // =========================================================
    private function resolverCodigosImpuesto(array $item): array
    {
        if (!empty($item['codigos']) && count($item['codigos']) >= 5) {
            $c = $item['codigos'];
            return [
                'cat_id'         => $c[0],
                'razon'          => $c[1],
                'cod_tributo'    => $c[2],
                'nombre_tributo' => $c[3],
                'tipo_codigo'    => $c[4],
                'porcentaje'     => $c[5] ?? 18,
            ];
        }

        $tipo = strtoupper($item['tipo_impuesto'] ?? 'IGV');

        return match ($tipo) {
            'EXONERADO' => ['cat_id'=>'E','razon'=>'20','cod_tributo'=>'9997','nombre_tributo'=>'EXO','tipo_codigo'=>'VAT','porcentaje'=>0],
            'INAFECTO'  => ['cat_id'=>'O','razon'=>'30','cod_tributo'=>'9998','nombre_tributo'=>'INA','tipo_codigo'=>'FRE','porcentaje'=>0],
            default     => ['cat_id'=>'S','razon'=>'10','cod_tributo'=>'1000','nombre_tributo'=>'IGV','tipo_codigo'=>'VAT','porcentaje'=>18],
        };
    }

    // =========================================================
    // FIRMAR XML
    // =========================================================
    private function firmar(string $rutaXML, array $emisor): array
    {
        $sucursal = $emisor['sucursal'] ?? '1';
        $base     = $this->base_dir . "sucursales/{$sucursal}/";
        $pass_pfx = $emisor['pass_certificado'];
        $ruta_pfx = $base . ($emisor['certificado'] ?? '');

        if (!file_exists($ruta_pfx)) {
            $candidatos = array_merge(glob($base . '*.pfx') ?: [], glob($base . '*.p12') ?: []);
            $ruc = $emisor['ruc'] ?? '';
            foreach ($candidatos as $c) {
                if (strpos(basename($c), $ruc) !== false) { $ruta_pfx = $c; break; }
            }
            if (!file_exists($ruta_pfx) && !empty($candidatos)) {
                $ruta_pfx = $candidatos[0];
            }
        }

        if (!file_exists($ruta_pfx)) {
            $archivos = implode(', ', array_map('basename', glob($base . '*') ?: []));
            return ['ok' => false, 'mensaje' => "No se encontró certificado en sucursales/{$sucursal}/. Archivos: [{$archivos}]."];
        }

        $pfx   = file_get_contents($ruta_pfx);
        $certs = [];

        $resultado = openssl_pkcs12_read($pfx, $certs, (string)$pass_pfx);
        if (!$resultado) $resultado = openssl_pkcs12_read($pfx, $certs, '');
        if (!$resultado) $resultado = $this->_leerPfxLegacy($pfx, (string)$pass_pfx, $certs);

        if (!$resultado && function_exists('shell_exec') && !getenv('RENDER')) {
            $pfx_path  = realpath($ruta_pfx);
            $tmp       = sys_get_temp_dir();
            $pem_path  = $tmp . '/cert_temp_' . getmypid() . '.pem';
            $pfx_nuevo = $tmp . '/cert_nuevo_' . getmypid() . '.pfx';
            shell_exec("openssl pkcs12 -in \"{$pfx_path}\" -out \"{$pem_path}\" -passin pass:{$pass_pfx} -passout pass:{$pass_pfx} -legacy 2>&1");
            if (!file_exists($pem_path))
                shell_exec("openssl pkcs12 -in \"{$pfx_path}\" -out \"{$pem_path}\" -passin pass:{$pass_pfx} -passout pass:{$pass_pfx} 2>&1");
            shell_exec("openssl pkcs12 -export -in \"{$pem_path}\" -out \"{$pfx_nuevo}\" -passin pass:{$pass_pfx} -passout pass:{$pass_pfx} 2>&1");
            if (file_exists($pfx_nuevo))
                $resultado = openssl_pkcs12_read(file_get_contents($pfx_nuevo), $certs, $pass_pfx);
            @unlink($pem_path);
            @unlink($pfx_nuevo);
        }

        if (!$resultado) {
            $passInfo = empty($pass_pfx) ? 'VACÍA' : 'proporcionada (' . strlen((string)$pass_pfx) . ' chars)';
            return ['ok' => false, 'mensaje' => "No se pudo leer el PFX (" . strlen($pfx) . " bytes). Contraseña {$passInfo}. OpenSSL: " . OPENSSL_VERSION_TEXT];
        }

        $docFirma = new DOMDocument();
        $docFirma->load($rutaXML);

        $objDSig = new XMLSecurityDSig();
        $objDSig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
        $objDSig->addReference(
            $docFirma,
            XMLSecurityDSig::SHA1,
            ['http://www.w3.org/2000/09/xmldsig#enveloped-signature'],
            ['force_uri' => true]
        );

        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, ['type' => 'private']);
        $objKey->loadKey($certs['pkey']);
        $objDSig->sign($objKey);
        $objDSig->add509Cert($certs['cert']);

        $xpath = new DOMXPath($docFirma);
        $xpath->registerNamespace('ext', 'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2');
        $extensionContent = $xpath->query('//ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent')->item(0);

        if (!$extensionContent) {
            return ['ok' => false, 'mensaje' => 'No se encontró ext:ExtensionContent en el XML'];
        }

        $objDSig->appendSignature($extensionContent);
        $docFirma->save($rutaXML);

        return ['ok' => true];
    }

    // =========================================================
    // ENVIAR A SUNAT VÍA SOAP
    // =========================================================
    private function enviarWS(string $rutazip, array $emisor, string $nombrezip): array
    {
        $contenido = base64_encode(file_get_contents($rutazip));

        $xml_envio = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
            xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
            xmlns:ser="http://service.sunat.gob.pe"
            xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
         <soapenv:Header>
            <wsse:Security>
                <wsse:UsernameToken>
                    <wsse:Username>' . $emisor['ruc'] . $emisor['usuario_sol'] . '</wsse:Username>
                    <wsse:Password>' . $emisor['clave_sol'] . '</wsse:Password>
                </wsse:UsernameToken>
            </wsse:Security>
         </soapenv:Header>
         <soapenv:Body>
            <ser:sendBill>
                <fileName>' . $nombrezip . '</fileName>
                <contentFile>' . $contenido . '</contentFile>
            </ser:sendBill>
         </soapenv:Body>
        </soapenv:Envelope>';

        // FIX: default produccion
        $ws = ($emisor['ambiente'] ?? 'produccion') === 'produccion'
            ? 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService'
            : 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService';

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $ws,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $xml_envio,
            CURLOPT_HTTPAUTH       => CURLAUTH_ANY,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_CAINFO         => $this->_resolverCacert($emisor),
            CURLOPT_HTTPHEADER     => [
                'Content-type: text/xml; charset="utf-8"',
                'Accept: text/xml',
                'Cache-Control: no-cache',
                'Pragma: no-cache',
                'SOAPAction: ',
                'Content-length: ' . strlen($xml_envio),
            ],
        ]);

        $response  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error     = curl_error($ch);
        curl_close($ch);

        return ['body' => $response, 'http_code' => $http_code, 'error' => $error];
    }

    // =========================================================
    // HELPERS
    // =========================================================
    private function _resolverCacert(array $emisor): string
    {
        $sucursal    = $emisor['sucursal'] ?? '1';
        $en_sucursal = $this->base_dir . "sucursales/{$sucursal}/cacert.pem";
        if (file_exists($en_sucursal)) return $en_sucursal;
        $en_raiz = $this->base_dir . 'cacert.pem';
        if (file_exists($en_raiz)) return $en_raiz;
        return '';
    }

    // FIX: ZIP sin compresión (Store) — SUNAT requiere método 0
    private function crearZip(string $rutaZip, string $rutaArchivo, string $nombreDentroZip): bool
    {
        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            if ($zip->open($rutaZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) return false;
            $zip->addFile($rutaArchivo, $nombreDentroZip);
            // Forzar sin compresión (Store) — SUNAT no acepta Deflate
            $zip->setCompressionName($nombreDentroZip, ZipArchive::CM_STORE);
            $zip->close();
            return true;
        }

        // Fallback ZIP manual puro PHP — offset corregido
        $contenido  = file_get_contents($rutaArchivo);
        $tamano     = strlen($contenido);
        $crc        = crc32($contenido);
        $nombre     = $nombreDentroZip;
        $lenNombre  = strlen($nombre);
        $timestamp  = $this->_dosTime(time());

        // Local file header (30 bytes + nombre + datos)
        $localHeader  = pack('V', 0x04034b50);  // signature
        $localHeader .= pack('v', 20);           // version needed
        $localHeader .= pack('v', 0);            // flags
        $localHeader .= pack('v', 0);            // compression: store
        $localHeader .= pack('V', $timestamp);   // mod time/date
        $localHeader .= pack('V', $crc);         // crc32
        $localHeader .= pack('V', $tamano);      // compressed size
        $localHeader .= pack('V', $tamano);      // uncompressed size
        $localHeader .= pack('v', $lenNombre);   // filename length
        $localHeader .= pack('v', 0);            // extra field length
        $localHeader .= $nombre;
        $localHeader .= $contenido;

        $offsetCentral = 30 + $lenNombre + $tamano; // offset correcto

        // Central directory header
        $centralHeader  = pack('V', 0x02014b50);  // signature
        $centralHeader .= pack('v', 20);           // version made by
        $centralHeader .= pack('v', 20);           // version needed
        $centralHeader .= pack('v', 0);            // flags
        $centralHeader .= pack('v', 0);            // compression
        $centralHeader .= pack('V', $timestamp);   // mod time/date
        $centralHeader .= pack('V', $crc);         // crc32
        $centralHeader .= pack('V', $tamano);      // compressed size
        $centralHeader .= pack('V', $tamano);      // uncompressed size
        $centralHeader .= pack('v', $lenNombre);   // filename length
        $centralHeader .= pack('v', 0);            // extra field length
        $centralHeader .= pack('v', 0);            // comment length
        $centralHeader .= pack('v', 0);            // disk number start
        $centralHeader .= pack('v', 0);            // internal attributes
        $centralHeader .= pack('V', 0x20);         // external attributes (archivo normal)
        $centralHeader .= pack('V', 0);            // offset local header
        $centralHeader .= $nombre;

        $centralSize = strlen($centralHeader);

        // End of central directory
        $endRecord  = pack('V', 0x06054b50);       // signature
        $endRecord .= pack('v', 0);                // disk number
        $endRecord .= pack('v', 0);                // disk with central dir
        $endRecord .= pack('v', 1);                // entries on disk
        $endRecord .= pack('v', 1);                // total entries
        $endRecord .= pack('V', $centralSize);     // central dir size
        $endRecord .= pack('V', $offsetCentral);   // offset central dir ← CORREGIDO
        $endRecord .= pack('v', 0);                // comment length

        return file_put_contents($rutaZip, $localHeader . $centralHeader . $endRecord) !== false;
    }

    private function _dosTime(int $timestamp): int
    {
        return (((int)date('Y', $timestamp) - 1980) << 25)
             | ((int)date('n', $timestamp) << 21)
             | ((int)date('j', $timestamp) << 16)
             | ((int)date('G', $timestamp) << 11)
             | ((int)date('i', $timestamp) << 5)
             | ((int)date('s', $timestamp) >> 1);
    }

    private function _leerPfxLegacy(string $pfx_data, string $pass, array &$certs): bool
    {
        $tmp     = sys_get_temp_dir();
        $pfx_tmp = $tmp . '/pfx_legacy_' . getmypid() . '.pfx';
        $pem_tmp = $tmp . '/pem_legacy_' . getmypid() . '.pem';

        file_put_contents($pfx_tmp, $pfx_data);

        if (function_exists('proc_open')) {
            $intentos = [
                "openssl pkcs12 -in %s -out %s -passin pass:%s -nodes -legacy 2>&1",
                "openssl pkcs12 -in %s -out %s -passin pass:%s -nodes 2>&1",
                "openssl pkcs12 -in %s -out %s -passin pass:%s -nodes -nomacver 2>&1",
            ];

            foreach ($intentos as $cmdTpl) {
                $cmd  = sprintf($cmdTpl, escapeshellarg($pfx_tmp), escapeshellarg($pem_tmp), escapeshellarg($pass));
                $proc = proc_open($cmd, [['pipe','r'],['pipe','w'],['pipe','w']], $pipes);
                if (is_resource($proc)) proc_close($proc);
                if (file_exists($pem_tmp) && filesize($pem_tmp) > 10) break;
            }

            if (file_exists($pem_tmp) && filesize($pem_tmp) > 10) {
                $pem_content = file_get_contents($pem_tmp);
                @unlink($pem_tmp);
                @unlink($pfx_tmp);

                preg_match('/-----BEGIN CERTIFICATE-----.*?-----END CERTIFICATE-----/s', $pem_content, $certMatch);
                preg_match('/-----BEGIN (?:RSA )?PRIVATE KEY-----.*?-----END (?:RSA )?PRIVATE KEY-----/s', $pem_content, $keyMatch);

                if (!empty($certMatch) && !empty($keyMatch)) {
                    $certs['cert'] = $certMatch[0];
                    $certs['pkey'] = $keyMatch[0];
                    return true;
                }
            }
        }

        @unlink($pfx_tmp);
        @unlink($pem_tmp);
        return false;
    }

    private function leerMensajeCDR(string $carpeta): ?string
    {
        $archivos = glob($carpeta . '/*.xml') ?: glob($carpeta . '/*.XML');
        if (empty($archivos)) return null;

        $cdrDoc = new DOMDocument();
        @$cdrDoc->load($archivos[0]);

        $desc = $cdrDoc->getElementsByTagName('Description')->item(0);
        return $desc ? $desc->nodeValue : null;
    }
}