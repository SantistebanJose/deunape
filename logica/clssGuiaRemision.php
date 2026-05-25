<?php
/**
 * clssGuiaRemision.php
 * Clase reutilizable para generar, firmar y enviar Guías de Remisión a SUNAT.
 * Soporta:
 *   - Guía de Remisión Remitente  (tipo 09)
 *   - Guía de Remisión Transportista (tipo 31)
 *
 * Basada en clssSunat.php — misma arquitectura, mismos patrones.
 *
 * Uso:
 *   require_once 'clssGuiaRemision.php';
 *   $guia = new SunatGuiaRemision();
 *   $resultado = $guia->enviar($emisor, $destinatario, $cabecera, $items);
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class SunatGuiaRemision
{
    private string $base_dir;

    public function __construct()
    {
        $this->base_dir = rtrim(dirname(__DIR__), '/') . '/';
    }

    // =========================================================
    // PUNTO DE ENTRADA PRINCIPAL
    // =========================================================
    /**
     * @param array $emisor        Datos del remitente/transportista (ruc, certificado, ...)
     * @param array $destinatario  Datos del destinatario
     * @param array $cabecera      Cabecera de la guía (tipo, serie, correlativo, ...)
     * @param array $items         Líneas de detalle (bienes a trasladar)
     * @return array ['estado' => bool, 'mensaje' => string, 'nombrexml' => string]
     */
    public function enviar(array $emisor, array $destinatario, array $cabecera, array $items): array
    {
        try {
            // ── Rutas dinámicas por sucursal ──────────────────────────
            $sucursal   = $emisor['sucursal'] ?? '1';
            $base       = $this->base_dir . "sucursales/{$sucursal}/";

            $esRender   = getenv('RENDER') === 'true' || !is_writable(dirname($base));
            $carpetaxml = $esRender ? "/tmp/xml_{$sucursal}/" : $base . "xml/";
            $carpetacdr = $esRender ? "/tmp/cdr_{$sucursal}/" : $base . "cdr/";

            if (!is_dir($carpetaxml)) mkdir($carpetaxml, 0777, true);
            if (!is_dir($carpetacdr)) mkdir($carpetacdr, 0777, true);

            // Tipo: 09 = Remitente, 31 = Transportista
            $tipo      = $cabecera['tipo_comprobante'] ?? '09';
            $nombrexml = $emisor['ruc'] . "-" . $tipo . "-" . $cabecera['serie'] . "-" . $cabecera['correlativo'];
            $rutaXML   = $carpetaxml . $nombrexml . '.XML';

            // ── PASO 01: Generar XML ──────────────────────────────────
            $xml = match ($tipo) {
                '31'    => $this->generarXMLTransportista($emisor, $destinatario, $cabecera, $items),
                default => $this->generarXMLRemitente($emisor, $destinatario, $cabecera, $items),
            };

            $doc = new DOMDocument('1.0', 'utf-8');
            $doc->formatOutput       = false;
            $doc->preserveWhiteSpace = true;
            $doc->loadXML($xml);
            $doc->save($rutaXML);

            // ── PASO 02: Firmar ───────────────────────────────────────
            $firma = $this->firmar($rutaXML, $emisor);
            if (!$firma['ok']) {
                return ['estado' => false, 'mensaje' => $firma['mensaje']];
            }

            // ── PASO 03: ZIP ──────────────────────────────────────────
            $nombrezip = $nombrexml . ".ZIP";
            $rutazip   = $carpetaxml . $nombrezip;

            if (!$this->crearZip($rutazip, $rutaXML, $nombrexml . '.XML')) {
                return ['estado' => false, 'mensaje' => 'No se pudo crear el archivo ZIP'];
            }

            // ── PASO 04: Enviar a SUNAT ───────────────────────────────
            $resp = $this->enviarWS($rutazip, $emisor, $nombrezip);

            // ── PASO 05: Procesar CDR ─────────────────────────────────
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
                        'estado'    => true,
                        'mensaje'   => $msgSunat ?: 'Guía de remisión enviada correctamente a SUNAT',
                        'nombrexml' => $nombrexml,
                        'nombrezip' => $nombrezip,
                    ];
                } else {
                    $code = $docResp->getElementsByTagName('faultcode')->item(0)?->nodeValue ?? '';
                    $msg  = $docResp->getElementsByTagName('faultstring')->item(0)?->nodeValue ?? '';
                    return ['estado' => false, 'mensaje' => "Error SUNAT [{$code}]: {$msg}"];
                }
            } else {
                return ['estado' => false, 'mensaje' => 'Error de conexión: ' . $resp['error']];
            }
        } catch (Throwable $e) {
            return ['estado' => false, 'mensaje' => 'Excepción: ' . $e->getMessage()];
        }
    }

    // =========================================================
    // XML — GUÍA DE REMISIÓN REMITENTE (09)
    // Catálogo 20 SUNAT: Modalidad traslado
    //   01 = Transporte Público
    //   02 = Transporte Privado
    // =========================================================
    private function generarXMLRemitente(array $emisor, array $destinatario, array $cabecera, array $items): string
    {
        $moneda     = 'PEN'; // Las GR siempre en PEN
        $modalidad  = $cabecera['modalidad_traslado'] ?? '02'; // 01=Público, 02=Privado
        $motivo     = $cabecera['motivo_traslado']    ?? '01'; // Catálogo 20

        $xml  = '<?xml version="1.0" encoding="utf-8"?>';
        $xml .= '<DespatchAdvice
         xmlns="urn:oasis:names:specification:ubl:schema:xsd:DespatchAdvice-2"
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xmlns:xsd="http://www.w3.org/2001/XMLSchema"
         xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
         xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
         xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
         xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">
    <ext:UBLExtensions>
        <ext:UBLExtension><ext:ExtensionContent/></ext:UBLExtension>
    </ext:UBLExtensions>
    <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
    <cbc:CustomizationID schemeAgencyName="PE:SUNAT">2.0</cbc:CustomizationID>
    <cbc:ID>' . $cabecera['serie'] . '-' . $cabecera['correlativo'] . '</cbc:ID>
    <cbc:IssueDate>' . $cabecera['fecha_emision'] . '</cbc:IssueDate>
    <cbc:IssueTime>' . ($cabecera['hora_emision'] ?? date('H:i:s')) . '</cbc:IssueTime>
    <cbc:DespatchAdviceTypeCode listAgencyName="PE:SUNAT"
        listName="Tipo de Documento"
        listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01">' . ($cabecera['tipo_comprobante'] ?? '09') . '</cbc:DespatchAdviceTypeCode>';

        // ── Referencia a comprobante relacionado (opcional) ───────
        if (!empty($cabecera['comprobante_ref_serie'])) {
            $xml .= '
    <cac:AdditionalDocumentReference>
        <cbc:ID>' . $cabecera['comprobante_ref_serie'] . '-' . $cabecera['comprobante_ref_correlativo'] . '</cbc:ID>
        <cbc:DocumentTypeCode listAgencyName="PE:SUNAT"
            listName="Tipo de Documento"
            listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01">' . ($cabecera['comprobante_ref_tipo'] ?? '01') . '</cbc:DocumentTypeCode>
    </cac:AdditionalDocumentReference>';
        }

        $xml .= $this->xmlSignatureGR($emisor, $cabecera);

        // ── Entrega (destinatario y punto llegada) ────────────────
        $xml .= $this->xmlShipment($cabecera, $destinatario, $modalidad, $motivo);

        // ── Remitente ─────────────────────────────────────────────
        $xml .= $this->xmlDespatchSupplierParty($emisor);

        // ── Destinatario ──────────────────────────────────────────
        $xml .= $this->xmlDeliveryCustomerParty($destinatario);

        // ── Líneas de detalle ─────────────────────────────────────
        foreach ($items as $v) {
            $xml .= $this->xmlDespatchLine($v);
        }

        $xml .= '
</DespatchAdvice>';
        return $xml;
    }

    // =========================================================
    // XML — GUÍA DE REMISIÓN TRANSPORTISTA (31)
    // =========================================================
    private function generarXMLTransportista(array $emisor, array $destinatario, array $cabecera, array $items): string
    {
        $xml  = '<?xml version="1.0" encoding="utf-8"?>';
        $xml .= '<DespatchAdvice
         xmlns="urn:oasis:names:specification:ubl:schema:xsd:DespatchAdvice-2"
         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xmlns:xsd="http://www.w3.org/2001/XMLSchema"
         xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
         xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
         xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
         xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">
    <ext:UBLExtensions>
        <ext:UBLExtension><ext:ExtensionContent/></ext:UBLExtension>
    </ext:UBLExtensions>
    <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
    <cbc:CustomizationID schemeAgencyName="PE:SUNAT">2.0</cbc:CustomizationID>
    <cbc:ID>' . $cabecera['serie'] . '-' . $cabecera['correlativo'] . '</cbc:ID>
    <cbc:IssueDate>' . $cabecera['fecha_emision'] . '</cbc:IssueDate>
    <cbc:IssueTime>' . ($cabecera['hora_emision'] ?? date('H:i:s')) . '</cbc:IssueTime>
    <cbc:DespatchAdviceTypeCode listAgencyName="PE:SUNAT"
        listName="Tipo de Documento"
        listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01">31</cbc:DespatchAdviceTypeCode>';

        // Referencia a la GR Remitente relacionada (requerido en tipo 31)
        if (!empty($cabecera['guia_remitente_serie'])) {
            $xml .= '
    <cac:AdditionalDocumentReference>
        <cbc:ID>' . $cabecera['guia_remitente_serie'] . '-' . $cabecera['guia_remitente_correlativo'] . '</cbc:ID>
        <cbc:DocumentTypeCode listAgencyName="PE:SUNAT"
            listName="Tipo de Documento"
            listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01">09</cbc:DocumentTypeCode>
    </cac:AdditionalDocumentReference>';
        }

        $xml .= $this->xmlSignatureGR($emisor, $cabecera);
        $xml .= $this->xmlShipmentTransportista($cabecera, $destinatario);
        $xml .= $this->xmlDespatchSupplierParty($emisor);
        $xml .= $this->xmlDeliveryCustomerParty($destinatario);

        foreach ($items as $v) {
            $xml .= $this->xmlDespatchLine($v);
        }

        $xml .= '
</DespatchAdvice>';
        return $xml;
    }

    // =========================================================
    // BLOQUES XML COMUNES
    // =========================================================

    private function xmlSignatureGR(array $emisor, array $cabecera): string
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

    /**
     * Shipment para Guía Remitente (tipo 09)
     * Incluye: Delivery, TransportHandlingUnit y Transportista (si es público)
     */
    private function xmlShipment(array $cabecera, array $destinatario, string $modalidad, string $motivo): string
    {
        $xml  = '
    <cac:Shipment>
        <cbc:ID>1</cbc:ID>
        <cbc:HandlingCode listAgencyName="PE:SUNAT"
            listName="Motivo de traslado"
            listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo20">' . $motivo . '</cbc:HandlingCode>
        <cbc:HandlingInstructions><![CDATA[' . ($cabecera['indicaciones'] ?? '') . ']]></cbc:HandlingInstructions>
        <cbc:GrossWeightMeasure unitCode="' . ($cabecera['unidad_peso'] ?? 'KGM') . '">' . number_format((float)($cabecera['peso_bruto_total'] ?? 0), 3, '.', '') . '</cbc:GrossWeightMeasure>
        <cbc:SplitConsignmentIndicator>false</cbc:SplitConsignmentIndicator>
        <cac:ShipmentStage>
            <cbc:TransportModeCode listName="Modalidad de traslado"
                listAgencyName="PE:SUNAT"
                listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo18">' . $modalidad . '</cbc:TransportModeCode>
            <cac:TransitPeriod>
                <cbc:StartDate>' . ($cabecera['fecha_traslado'] ?? $cabecera['fecha_emision'] ?? date('Y-m-d')) . '</cbc:StartDate>
            </cac:TransitPeriod>';

        // Transportista (solo si modalidad = 01 Público)
        if ($modalidad === '01' && !empty($cabecera['transportista_ruc'])) {
            $xml .= '
            <cac:CarrierParty>
                <cac:PartyIdentification>
                    <cbc:ID schemeID="' . ($cabecera['transportista_tipo_doc'] ?? '6') . '"
                        schemeName="Documento de Identidad"
                        schemeAgencyName="PE:SUNAT"
                        schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $cabecera['transportista_ruc'] . '</cbc:ID>
                </cac:PartyIdentification>
                <cac:PartyName>
                    <cbc:Name><![CDATA[' . ($cabecera['transportista_razon_social'] ?? '') . ']]></cbc:Name>
                </cac:PartyName>
            </cac:CarrierParty>';
        }

        // Conductor (transporte privado o público)
        if (!empty($cabecera['conductor_licencia'])) {
            $xml .= '
            <cac:DriverPerson>
                <cbc:ID schemeID="' . ($cabecera['conductor_tipo_doc'] ?? '1') . '"
                    schemeName="Documento de Identidad"
                    schemeAgencyName="PE:SUNAT"
                    schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . ($cabecera['conductor_doc'] ?? '') . '</cbc:ID>
                <cbc:FirstName><![CDATA[' . ($cabecera['conductor_nombres'] ?? '') . ']]></cbc:FirstName>
                <cbc:FamilyName><![CDATA[' . ($cabecera['conductor_apellidos'] ?? '') . ']]></cbc:FamilyName>
                <cbc:JobTitle>' . ($cabecera['conductor_licencia'] ?? '') . '</cbc:JobTitle>
            </cac:DriverPerson>';
        }

        $xml .= '
        </cac:ShipmentStage>';

        // Punto de llegada
        $xml .= '
        <cac:Delivery>
            <cac:DeliveryAddress>
                <cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI">' . ($destinatario['ubigeo_llegada'] ?? $destinatario['ubigeo'] ?? '000000') . '</cbc:ID>
                <cac:AddressLine>
                    <cbc:Line><![CDATA[' . ($destinatario['direccion_llegada'] ?? $destinatario['direccion'] ?? '') . ']]></cbc:Line>
                </cac:AddressLine>
            </cac:DeliveryAddress>
        </cac:Delivery>';

        // Punto de partida
        $xml .= '
        <cac:OriginAddress>
            <cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI">' . ($cabecera['ubigeo_partida'] ?? $cabecera['ubigeo_origen'] ?? '000000') . '</cbc:ID>
            <cac:AddressLine>
                <cbc:Line><![CDATA[' . ($cabecera['direccion_partida'] ?? $cabecera['direccion_origen'] ?? '') . ']]></cbc:Line>
            </cac:AddressLine>
        </cac:OriginAddress>';

        // Vehículo (transporte privado o público)
        if (!empty($cabecera['placa_vehiculo'])) {
            $xml .= '
        <cac:TransportHandlingUnit>
            <cbc:ID>1</cbc:ID>
            <cac:TransportEquipment>
                <cbc:ID>' . $cabecera['placa_vehiculo'] . '</cbc:ID>
            </cac:TransportEquipment>
        </cac:TransportHandlingUnit>';
        }

        $xml .= '
    </cac:Shipment>';
        return $xml;
    }

    /**
     * Shipment para Guía Transportista (tipo 31)
     */
    private function xmlShipmentTransportista(array $cabecera, array $destinatario): string
    {
        $xml  = '
    <cac:Shipment>
        <cbc:ID>1</cbc:ID>
        <cbc:HandlingCode listAgencyName="PE:SUNAT"
            listName="Motivo de traslado"
            listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo20">' . ($cabecera['motivo_traslado'] ?? '01') . '</cbc:HandlingCode>
        <cbc:GrossWeightMeasure unitCode="' . ($cabecera['unidad_peso'] ?? 'KGM') . '">' . number_format((float)($cabecera['peso_bruto_total'] ?? 0), 3, '.', '') . '</cbc:GrossWeightMeasure>
        <cbc:SplitConsignmentIndicator>false</cbc:SplitConsignmentIndicator>
        <cac:ShipmentStage>
            <cbc:TransportModeCode listName="Modalidad de traslado"
                listAgencyName="PE:SUNAT"
                listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo18">01</cbc:TransportModeCode>
            <cac:TransitPeriod>
                <cbc:StartDate>' . ($cabecera['fecha_traslado'] ?? date('Y-m-d')) . '</cbc:StartDate>
            </cac:TransitPeriod>';

        if (!empty($cabecera['conductor_licencia'])) {
            $xml .= '
            <cac:DriverPerson>
                <cbc:ID schemeID="' . ($cabecera['conductor_tipo_doc'] ?? '1') . '"
                    schemeName="Documento de Identidad"
                    schemeAgencyName="PE:SUNAT"
                    schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . ($cabecera['conductor_doc'] ?? '') . '</cbc:ID>
                <cbc:FirstName><![CDATA[' . ($cabecera['conductor_nombres'] ?? '') . ']]></cbc:FirstName>
                <cbc:FamilyName><![CDATA[' . ($cabecera['conductor_apellidos'] ?? '') . ']]></cbc:FamilyName>
                <cbc:JobTitle>' . $cabecera['conductor_licencia'] . '</cbc:JobTitle>
            </cac:DriverPerson>';
        }

        $xml .= '
        </cac:ShipmentStage>
        <cac:Delivery>
            <cac:DeliveryAddress>
                <cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI">' . ($destinatario['ubigeo_llegada'] ?? $destinatario['ubigeo'] ?? '000000') . '</cbc:ID>
                <cac:AddressLine>
                    <cbc:Line><![CDATA[' . ($destinatario['direccion_llegada'] ?? $destinatario['direccion'] ?? '') . ']]></cbc:Line>
                </cac:AddressLine>
            </cac:DeliveryAddress>
        </cac:Delivery>
        <cac:OriginAddress>
            <cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI">' . ($cabecera['ubigeo_partida'] ?? '000000') . '</cbc:ID>
            <cac:AddressLine>
                <cbc:Line><![CDATA[' . ($cabecera['direccion_partida'] ?? '') . ']]></cbc:Line>
            </cac:AddressLine>
        </cac:OriginAddress>';

        if (!empty($cabecera['placa_vehiculo'])) {
            $xml .= '
        <cac:TransportHandlingUnit>
            <cbc:ID>1</cbc:ID>
            <cac:TransportEquipment>
                <cbc:ID>' . $cabecera['placa_vehiculo'] . '</cbc:ID>
            </cac:TransportEquipment>
        </cac:TransportHandlingUnit>';
        }

        $xml .= '
    </cac:Shipment>';
        return $xml;
    }

    private function xmlDespatchSupplierParty(array $e): string
    {
        $td = $e['tipo_documento'] ?? 6;
        return '
    <cac:DespatchSupplierParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="' . $td . '" schemeName="Documento de Identidad"
                    schemeAgencyName="PE:SUNAT"
                    schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $e['ruc'] . '</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA[' . $e['razon_social'] . ']]></cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:DespatchSupplierParty>';
    }

    private function xmlDeliveryCustomerParty(array $c): string
    {
        $td     = $c['tipo_documento'] ?? 6;
        $numDoc = $c['numero_doc'] ?? $c['ruc'] ?? '';
        $nombre = $c['razon_social'] ?? $c['nombre'] ?? '';
        return '
    <cac:DeliveryCustomerParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="' . $td . '" schemeName="Documento de Identidad"
                    schemeAgencyName="PE:SUNAT"
                    schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $numDoc . '</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA[' . $nombre . ']]></cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:DeliveryCustomerParty>';
    }

    private function xmlDespatchLine(array $v): string
    {
        $n = fn($val) => number_format((float)($val ?? 0), 2, '.', '');
        return '
    <cac:DespatchLine>
        <cbc:ID>' . $v['item'] . '</cbc:ID>
        <cbc:DeliveredQuantity unitCode="' . ($v['unidad'] ?? 'NIU') . '"
            unitCodeListID="UN/ECE rec 20"
            unitCodeListAgencyName="United Nations Economic Commission for Europe">' . $n($v['cantidad']) . '</cbc:DeliveredQuantity>
        <cac:Item>
            <cbc:Description><![CDATA[' . ($v['nombre'] ?? $v['descripcion'] ?? '') . ']]></cbc:Description>
            <cac:SellersItemIdentification>
                <cbc:ID><![CDATA[' . ($v['codigo_producto'] ?? '001') . ']]></cbc:ID>
            </cac:SellersItemIdentification>
        </cac:Item>
    </cac:DespatchLine>';
    }

    // =========================================================
    // FIRMAR XML — idéntico a clssSunat.php
    // =========================================================
    private function firmar(string $rutaXML, array $emisor): array
    {
        $sucursal = $emisor['sucursal'] ?? '1';
        $base     = $this->base_dir . "sucursales/{$sucursal}/";
        $pass_pfx = $emisor['pass_certificado'] ?? '';

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
            return ['ok' => false, 'mensaje' => "No se encontró certificado en sucursales/{$sucursal}/"];
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
            return ['ok' => false, 'mensaje' => 'No se pudo leer el PFX. OpenSSL: ' . OPENSSL_VERSION_TEXT];
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
    // Las GR usan el endpoint /ol-ti-itcpe/ (distinto al de facturas)
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

        // ⚠ IMPORTANTE: Las GR usan un endpoint DIFERENTE al de facturas/boletas
        $ws = ($emisor['ambiente'] ?? 'beta') === 'produccion'
            ? 'https://e-factura.sunat.gob.pe/ol-ti-itcpe/billService'
            : 'https://e-beta.sunat.gob.pe/ol-ti-itcpe-beta/billService';

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $ws,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $xml_envio,
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
    // HELPERS — idénticos a clssSunat.php
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

    private function crearZip(string $rutaZip, string $rutaArchivo, string $nombreDentroZip): bool
    {
        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            if ($zip->open($rutaZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) return false;
            $zip->addFile($rutaArchivo, $nombreDentroZip);
            $zip->close();
            return true;
        }

        $contenido = file_get_contents($rutaArchivo);
        $crc       = crc32($contenido);
        $len       = strlen($contenido);
        $time      = time();
        $dostime   = (((date('Y',$time)-1980)<<9)|(date('n',$time)<<5)|date('j',$time))<<16
                   | ((date('G',$time)<<11)|(date('i',$time)<<5)|(int)(date('s',$time)/2));
        $nombre    = $nombreDentroZip;

        $lfh  = pack('VvvvVVVVvv', 0x04034b50, 20, 0, 0, $dostime, $crc, $len, $len, strlen($nombre), 0);
        $lfh .= $nombre . $contenido;

        $cdh  = pack('VvvvvVVVVvvvvvVV', 0x02014b50, 20, 20, 0, 0, $dostime, $crc, $len, $len, strlen($nombre), 0, 0, 0, 0, 0, 0);
        $cdh .= $nombre;

        $eocd = pack('VvvvvVVv', 0x06054b50, 0, 0, 1, 1, strlen($cdh), strlen($lfh), 0);

        file_put_contents($rutaZip, $lfh . $cdh . $eocd);
        return true;
    }

    private function _leerPfxLegacy(string $pfx_data, string $pass, array &$certs): bool
    {
        $tmp     = sys_get_temp_dir();
        $pfx_tmp = $tmp . '/pfx_gr_' . getmypid() . '.pfx';
        $pem_tmp = $tmp . '/pem_gr_' . getmypid() . '.pem';

        file_put_contents($pfx_tmp, $pfx_data);

        if (function_exists('proc_open')) {
            $intentos = [
                "openssl pkcs12 -in %s -out %s -passin pass:%s -nodes -legacy 2>&1",
                "openssl pkcs12 -in %s -out %s -passin pass:%s -nodes 2>&1",
                "openssl pkcs12 -in %s -out %s -passin pass:%s -nodes -nomacver 2>&1",
            ];
            foreach ($intentos as $tpl) {
                $cmd  = sprintf($tpl, escapeshellarg($pfx_tmp), escapeshellarg($pem_tmp), escapeshellarg($pass));
                $proc = proc_open($cmd, [['pipe','r'],['pipe','w'],['pipe','w']], $pipes);
                if (is_resource($proc)) proc_close($proc);
                if (file_exists($pem_tmp) && filesize($pem_tmp) > 10) break;
            }
            if (file_exists($pem_tmp) && filesize($pem_tmp) > 10) {
                $pem = file_get_contents($pem_tmp);
                @unlink($pem_tmp); @unlink($pfx_tmp);
                preg_match('/-----BEGIN CERTIFICATE-----.*?-----END CERTIFICATE-----/s', $pem, $certM);
                preg_match('/-----BEGIN (?:RSA )?PRIVATE KEY-----.*?-----END (?:RSA )?PRIVATE KEY-----/s', $pem, $keyM);
                if (!empty($certM) && !empty($keyM)) {
                    $certs['cert'] = $certM[0];
                    $certs['pkey'] = $keyM[0];
                    return true;
                }
            }
        }
        @unlink($pfx_tmp); @unlink($pem_tmp);
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