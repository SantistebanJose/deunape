<?php
#boleta.php (muestra de como se declara pero debemos de mejorar para poder usarlo para facturas, boletas, notas de credito y asi)
require_once 'vendor/autoload.php';
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

$emisor = array(
    "sucursal"         => "1",
    "certificado"      => "certificadoVYSAMp12.pfx",
    "pass_certificado" => "VYSAMjose04",
    "tipo_documento"   => 6,
    "ruc"              => "20607599727",
    "razon_social"     => "INSTITUTO INTERNACIONAL DE SOFTWARE S.A.C.",
    "nombre_comercial" => "ACADEMIA DE SOFTWARE",
    "departamento"     => "LAMBAYEQUE",
    "provincia"        => "CHICLAYO",
    "distrito"         => "CHICLAYO",
    "direccion"        => "CALLE OCHO DE OCTUBRE 123",
    "ubigeo"           => "140101",
    "usuario_sol"      => "MODDATOS",
    "clave_sol"        => "MODDATOS"
);

$cliente = array(
    "tipo_documento" => "6",
    "ruc"            => "20605145648",
    "razon_social"   => "AGROINVERSIONES Y SERVICIOS AJINOR S.R.L. - AGROSERVIS AJINOR S.R.L.",
    "direccion"      => "MZA. C LOTE. 46 URB. SAN ISIDRO LA LIBERTAD - TRUJILLO - TRUJILLO"
);

$cabecera = array(
    "tipo_operacion"          => "0101",
    "tipo_comprobante"        => "03",
    "moneda"                  => "PEN",
    "serie"                   => "B001",
    "correlativo"             => 1234,
    "total_op_gravadas"       => 50.17,
    "igv"                     => 9.03,
    "icbper"                  => 0.30,
    "total_op_exoneradas"     => 140.00,
    "total_op_inafectas"      => 270.00,
    "total_antes_impuestos"   => 460.17,
    "total_impuestos"         => 9.33,
    "total_despues_impuestos" => 469.50,
    "total_a_pagar"           => 469.50,
    "fecha_emision"           => "2021-08-24",
    "hora_emision"            => "19:43:00",
    "fecha_vencimiento"       => "2021-08-24"
);

$items = array();

$items[] = array(
    "item"                  => 1,
    "cantidad"              => 1,
    "unidad"                => "NIU",
    "nombre"                => "MOCHILA",
    "valor_unitario"        => 50.00,
    "precio_lista"          => 59.00,
    "valor_total"           => 50.00,
    "igv"                   => 9.00,
    "icbper"                => 0.00,
    "factor_icbper"         => 0.30,
    "total_antes_impuestos" => 50.00,
    "total_impuestos"       => 9.00,
    "codigos"               => array("S","10","1000","IGV","VAT")
);

$items[] = array(
    "item"                  => 2,
    "cantidad"              => 2,
    "unidad"                => "NIU",
    "nombre"                => "LIBRO COQUITO",
    "valor_unitario"        => 70.00,
    "precio_lista"          => 70.00,
    "valor_total"           => 140.00,
    "igv"                   => 0.00,
    "icbper"                => 0.00,
    "factor_icbper"         => 0.30,
    "total_antes_impuestos" => 140.00,
    "total_impuestos"       => 0.00,
    "codigos"               => array("E","20","9997","EXO","VAT")
);

$items[] = array(
    "item"                  => 3,
    "cantidad"              => 3,
    "unidad"                => "NIU",
    "nombre"                => "MANZANA",
    "valor_unitario"        => 90.00,
    "precio_lista"          => 90.00,
    "valor_total"           => 270.00,
    "igv"                   => 0.00,
    "icbper"                => 0.00,
    "factor_icbper"         => 0.30,
    "total_antes_impuestos" => 270.00,
    "total_impuestos"       => 0.00,
    "codigos"               => array("E","30","9998","INA","FRE")
);

$items[] = array(
    "item"                  => 4,
    "cantidad"              => 1,
    "unidad"                => "NIU",
    "nombre"                => "BOLSA PLÁSTICA",
    "valor_unitario"        => 0.17,
    "precio_lista"          => 0.50,
    "valor_total"           => 0.17,
    "igv"                   => 0.03,
    "icbper"                => 0.30,
    "factor_icbper"         => 0.30,
    "total_antes_impuestos" => 0.17,
    "total_impuestos"       => 0.33,
    "codigos"               => array("S","10","1000","IGV","VAT")
);

// =============================================
// RUTAS DINÁMICAS POR SUCURSAL
// =============================================
$base       = "sucursales/" . $emisor['sucursal'] . "/";
$carpetaxml = $base . "xml/";
$carpetacdr = $base . "cdr/";
$ruta_pfx   = $base . $emisor['certificado'];
$pass_pfx   = $emisor['pass_certificado'];

// Crear carpetas si no existen
if (!is_dir($carpetaxml)) mkdir($carpetaxml, 0777, true);
if (!is_dir($carpetacdr)) mkdir($carpetacdr, 0777, true);

$nombrexml = $emisor['ruc']."-".$cabecera['tipo_comprobante']."-".$cabecera['serie']."-".$cabecera['correlativo'];

// =============================================
// PASO 01 - GENERAR XML
// =============================================
$doc = new DOMDocument();
$doc->formatOutput = FALSE;
$doc->preserveWhiteSpace = TRUE;
$doc->encoding = 'utf-8';

$xml = '<?xml version="1.0" encoding="utf-8"?>
<Invoice xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ccts="urn:un:unece:uncefact:documentation:2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2" xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2">
    <ext:UBLExtensions>
        <ext:UBLExtension>
            <ext:ExtensionContent/>
        </ext:UBLExtension>
    </ext:UBLExtensions>
    <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
    <cbc:CustomizationID schemeAgencyName="PE:SUNAT">2.0</cbc:CustomizationID>
    <cbc:ProfileID schemeName="Tipo de Operacion" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo17">'.$cabecera['tipo_operacion'].'</cbc:ProfileID>
    <cbc:ID>'.$cabecera['serie'].'-'.$cabecera['correlativo'].'</cbc:ID>
    <cbc:IssueDate>'.$cabecera['fecha_emision'].'</cbc:IssueDate>
    <cbc:IssueTime>'.$cabecera['hora_emision'].'</cbc:IssueTime>
    <cbc:DueDate>'.$cabecera['fecha_vencimiento'].'</cbc:DueDate>
    <cbc:InvoiceTypeCode listAgencyName="PE:SUNAT" listName="Tipo de Documento" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01" listID="0101" name="Tipo de Operacion">'.$cabecera['tipo_comprobante'].'</cbc:InvoiceTypeCode>
    <cbc:DocumentCurrencyCode listID="ISO 4217 Alpha" listName="Currency" listAgencyName="United Nations Economic Commission for Europe">'.$cabecera['moneda'].'</cbc:DocumentCurrencyCode>
    <cbc:LineCountNumeric>'.count($items).'</cbc:LineCountNumeric>
    <cac:Signature>
        <cbc:ID>'.$cabecera['serie'].'-'.$cabecera['correlativo'].'</cbc:ID>
        <cac:SignatoryParty>
            <cac:PartyIdentification>
                <cbc:ID>'.$emisor['ruc'].'</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><![CDATA['.$emisor['razon_social'].']]></cbc:Name>
            </cac:PartyName>
        </cac:SignatoryParty>
        <cac:DigitalSignatureAttachment>
            <cac:ExternalReference>
                <cbc:URI>#SignatureSP</cbc:URI>
            </cac:ExternalReference>
        </cac:DigitalSignatureAttachment>
    </cac:Signature>
    <cac:AccountingSupplierParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="'.$emisor['tipo_documento'].'" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$emisor['ruc'].'</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><![CDATA['.$emisor['razon_social'].']]></cbc:Name>
            </cac:PartyName>
            <cac:PartyTaxScheme>
                <cbc:RegistrationName><![CDATA['.$emisor['razon_social'].']]></cbc:RegistrationName>
                <cbc:CompanyID schemeID="'.$emisor['tipo_documento'].'" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$emisor['ruc'].'</cbc:CompanyID>
                <cac:TaxScheme>
                    <cbc:ID schemeID="'.$emisor['tipo_documento'].'" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$emisor['ruc'].'</cbc:ID>
                </cac:TaxScheme>
            </cac:PartyTaxScheme>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA['.$emisor['razon_social'].']]></cbc:RegistrationName>
                <cac:RegistrationAddress>
                    <cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI">'.$emisor['ubigeo'].'</cbc:ID>
                    <cbc:AddressTypeCode listAgencyName="PE:SUNAT" listName="Establecimientos anexos">0000</cbc:AddressTypeCode>
                    <cbc:CityName><![CDATA['.$emisor['provincia'].']]></cbc:CityName>
                    <cbc:CountrySubentity><![CDATA['.$emisor['departamento'].']]></cbc:CountrySubentity>
                    <cbc:District><![CDATA['.$emisor['distrito'].']]></cbc:District>
                    <cac:AddressLine>
                        <cbc:Line><![CDATA['.$emisor['direccion'].']]></cbc:Line>
                    </cac:AddressLine>
                    <cac:Country>
                        <cbc:IdentificationCode listID="ISO 3166-1" listAgencyName="United Nations Economic Commission for Europe" listName="Country">PE</cbc:IdentificationCode>
                    </cac:Country>
                </cac:RegistrationAddress>
            </cac:PartyLegalEntity>
            <cac:Contact>
                <cbc:Name><![CDATA[]]></cbc:Name>
            </cac:Contact>
        </cac:Party>
    </cac:AccountingSupplierParty>
    <cac:AccountingCustomerParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="'.$cliente['tipo_documento'].'" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$cliente['ruc'].'</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><![CDATA['.$cliente['razon_social'].']]></cbc:Name>
            </cac:PartyName>
            <cac:PartyTaxScheme>
                <cbc:RegistrationName><![CDATA['.$cliente['razon_social'].']]></cbc:RegistrationName>
                <cbc:CompanyID schemeID="'.$cliente['tipo_documento'].'" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$cliente['ruc'].'</cbc:CompanyID>
                <cac:TaxScheme>
                    <cbc:ID schemeID="'.$cliente['tipo_documento'].'" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">'.$cliente['ruc'].'</cbc:ID>
                </cac:TaxScheme>
            </cac:PartyTaxScheme>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA['.$cliente['razon_social'].']]></cbc:RegistrationName>
                <cac:RegistrationAddress>
                    <cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI"/>
                    <cbc:CityName><![CDATA[]]></cbc:CityName>
                    <cbc:CountrySubentity><![CDATA[]]></cbc:CountrySubentity>
                    <cbc:District><![CDATA[]]></cbc:District>
                    <cac:AddressLine>
                        <cbc:Line><![CDATA['.$cliente['direccion'].']]></cbc:Line>
                    </cac:AddressLine>
                    <cac:Country>
                        <cbc:IdentificationCode listID="ISO 3166-1" listAgencyName="United Nations Economic Commission for Europe" listName="Country"/>
                    </cac:Country>
                </cac:RegistrationAddress>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingCustomerParty>
    <cac:TaxTotal>
        <cbc:TaxAmount currencyID="'.$cabecera['moneda'].'">'.$cabecera['total_impuestos'].'</cbc:TaxAmount>
        <cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID="'.$cabecera['moneda'].'">'.$cabecera['total_op_gravadas'].'</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="'.$cabecera['moneda'].'">'.$cabecera['igv'].'</cbc:TaxAmount>
            <cac:TaxCategory>
                <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">S</cbc:ID>
                <cac:TaxScheme>
                    <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">1000</cbc:ID>
                    <cbc:Name>IGV</cbc:Name>
                    <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>';

if($cabecera['total_op_exoneradas'] > 0){
    $xml .= '<cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID="'.$cabecera['moneda'].'">'.$cabecera['total_op_exoneradas'].'</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="'.$cabecera['moneda'].'">0.00</cbc:TaxAmount>
            <cac:TaxCategory>
                <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                <cac:TaxScheme>
                    <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9997</cbc:ID>
                    <cbc:Name>EXO</cbc:Name>
                    <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>';
}

if($cabecera['total_op_inafectas'] > 0){
    $xml .= '<cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID="'.$cabecera['moneda'].'">'.$cabecera['total_op_inafectas'].'</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="'.$cabecera['moneda'].'">0.00</cbc:TaxAmount>
            <cac:TaxCategory>
                <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">O</cbc:ID>
                <cac:TaxScheme>
                    <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9998</cbc:ID>
                    <cbc:Name>INA</cbc:Name>
                    <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>';
}

if($cabecera['icbper'] > 0){
    $xml .= '<cac:TaxSubtotal>
            <cbc:TaxAmount currencyID="'.$cabecera['moneda'].'">0.30</cbc:TaxAmount>
            <cac:TaxCategory>
                <cac:TaxScheme>
                    <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">7152</cbc:ID>
                    <cbc:Name>ICBPER</cbc:Name>
                    <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>';
}

$xml .= '</cac:TaxTotal>
    <cac:LegalMonetaryTotal>
        <cbc:LineExtensionAmount currencyID="'.$cabecera['moneda'].'">'.$cabecera['total_antes_impuestos'].'</cbc:LineExtensionAmount>
        <cbc:TaxInclusiveAmount currencyID="'.$cabecera['moneda'].'">'.$cabecera['total_despues_impuestos'].'</cbc:TaxInclusiveAmount>
        <cbc:PayableAmount currencyID="'.$cabecera['moneda'].'">'.$cabecera['total_a_pagar'].'</cbc:PayableAmount>
    </cac:LegalMonetaryTotal>';

foreach ($items as $k => $v){
    $xml .= '<cac:InvoiceLine>
        <cbc:ID>'.$v['item'].'</cbc:ID>
        <cbc:InvoicedQuantity unitCode="'.$v['unidad'].'" unitCodeListID="UN/ECE rec 20" unitCodeListAgencyName="United Nations Economic Commission for Europe">'.$v['cantidad'].'</cbc:InvoicedQuantity>
        <cbc:LineExtensionAmount currencyID="'.$cabecera['moneda'].'">'.$v['total_antes_impuestos'].'</cbc:LineExtensionAmount>
        <cac:PricingReference>
            <cac:AlternativeConditionPrice>
                <cbc:PriceAmount currencyID="'.$cabecera['moneda'].'">'.$v['precio_lista'].'</cbc:PriceAmount>
                <cbc:PriceTypeCode listName="Tipo de Precio" listAgencyName="PE:SUNAT" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo16">01</cbc:PriceTypeCode>
            </cac:AlternativeConditionPrice>
        </cac:PricingReference>
        <cac:TaxTotal>
            <cbc:TaxAmount currencyID="'.$cabecera['moneda'].'">'.$v['total_impuestos'].'</cbc:TaxAmount>
            <cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID="'.$cabecera['moneda'].'">'.$v['valor_total'].'</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID="'.$cabecera['moneda'].'">'.$v['igv'].'</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">'.$v['codigos'][0].'</cbc:ID>
                    <cbc:Percent>18</cbc:Percent>
                    <cbc:TaxExemptionReasonCode listAgencyName="PE:SUNAT" listName="Afectacion del IGV" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo07">'.$v['codigos'][1].'</cbc:TaxExemptionReasonCode>
                    <cac:TaxScheme>
                        <cbc:ID schemeID="UN/ECE 5153" schemeName="Codigo de tributos" schemeAgencyName="PE:SUNAT">'.$v['codigos'][2].'</cbc:ID>
                        <cbc:Name>'.$v['codigos'][3].'</cbc:Name>
                        <cbc:TaxTypeCode>'.$v['codigos'][4].'</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>';

    if($v['icbper'] > 0){
        $xml .= '<cac:TaxSubtotal>
                <cbc:TaxAmount currencyID="'.$cabecera['moneda'].'">'.$v['icbper'].'</cbc:TaxAmount>
                <cbc:BaseUnitMeasure unitCode="'.$v['unidad'].'">'.$v['cantidad'].'</cbc:BaseUnitMeasure>
                <cac:TaxCategory>
                    <cbc:PerUnitAmount currencyID="'.$cabecera['moneda'].'">'.$v['factor_icbper'].'</cbc:PerUnitAmount>
                    <cac:TaxScheme>
                        <cbc:ID>7152</cbc:ID>
                        <cbc:Name>ICBPER</cbc:Name>
                        <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>';
    }

    $xml .= '</cac:TaxTotal>
        <cac:Item>
            <cbc:Description><![CDATA['.$v['nombre'].']]></cbc:Description>
            <cac:SellersItemIdentification>
                <cbc:ID><![CDATA[195]]></cbc:ID>
            </cac:SellersItemIdentification>
            <cac:CommodityClassification>
                <cbc:ItemClassificationCode listID="UNSPSC" listAgencyName="GS1 US" listName="Item Classification">10191509</cbc:ItemClassificationCode>
            </cac:CommodityClassification>
        </cac:Item>
        <cac:Price>
            <cbc:PriceAmount currencyID="'.$cabecera['moneda'].'">'.$v['valor_unitario'].'</cbc:PriceAmount>
        </cac:Price>
    </cac:InvoiceLine>';
}

$xml .= '</Invoice>';

$doc->loadXML($xml);
$doc->save($carpetaxml.$nombrexml.'.XML');


// =============================================
// PASO 02 - FIRMAR EL XML
// =============================================

$pfx   = file_get_contents($ruta_pfx);
$certs = [];

// Primero intento normal
$resultado = openssl_pkcs12_read($pfx, $certs, $pass_pfx);

// Si falla, convertir usando carpeta del proyecto (tiene permisos)
if (!$resultado) {
    $pfx_path  = realpath($ruta_pfx);
    $pem_path  = realpath(dirname(__FILE__)) . '/cert_temp.pem';
    $pfx_nuevo = realpath(dirname(__FILE__)) . '/cert_nuevo.pfx';

    $cmd1 = "openssl pkcs12 -in \"{$pfx_path}\" -out \"{$pem_path}\" -passin pass:{$pass_pfx} -passout pass:{$pass_pfx} 2>&1";
    $out1 = shell_exec($cmd1);

    $cmd2 = "openssl pkcs12 -export -in \"{$pem_path}\" -out \"{$pfx_nuevo}\" -passin pass:{$pass_pfx} -passout pass:{$pass_pfx} 2>&1";
    $out2 = shell_exec($cmd2);

    echo "CMD1: " . $out1 . "<br>";
    echo "CMD2: " . $out2 . "<br>";

    if (file_exists($pfx_nuevo)) {
        $resultado = openssl_pkcs12_read(file_get_contents($pfx_nuevo), $certs, $pass_pfx);
    }

    if (!$resultado) {
        echo "❌ No se pudo leer el PFX.<br>";
        while ($msg = openssl_error_string()) echo $msg . "<br>";
        die();
    }

    // Limpiar archivos temporales
    @unlink($pem_path);
    @unlink($pfx_nuevo);
}

$docFirma = new DOMDocument();
$docFirma->load($carpetaxml.$nombrexml.'.XML');

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
    die("ERROR: No se encontró ext:ExtensionContent en el XML.");
}

$objDSig->appendSignature($extensionContent);
$docFirma->save($carpetaxml.$nombrexml.'.XML');
echo "XML firmado correctamente<br>";


// =============================================
// PASO 03 - COMPRIMIR EN ZIP
// =============================================
$zip       = new ZipArchive();
$nombrezip = $nombrexml.".ZIP";
$rutazip   = $carpetaxml.$nombrexml.".ZIP";

if($zip->open($rutazip, ZIPARCHIVE::CREATE) === true){
    $zip->addFile($carpetaxml.$nombrexml.'.XML', $nombrexml.'.XML');
    $zip->close();
}


// =============================================
// PASO 04 - PREPARAR ENVÍO A SUNAT
// =============================================
$contenido_del_zip = base64_encode(file_get_contents($rutazip));
$xml_envio = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
        xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" 
        xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
     <soapenv:Header>
            <wsse:Security>
                <wsse:UsernameToken>
                    <wsse:Username>'.$emisor['ruc'].$emisor['usuario_sol'].'</wsse:Username>
                    <wsse:Password>'.$emisor['clave_sol'].'</wsse:Password>
                </wsse:UsernameToken>
           </wsse:Security>
    </soapenv:Header>
    <soapenv:Body>
        <ser:sendBill>
            <fileName>'.$nombrezip.'</fileName>
            <contentFile>'.$contenido_del_zip.'</contentFile>
        </ser:sendBill>
    </soapenv:Body>
</soapenv:Envelope>';


// =============================================
// PASO 05 - ENVIAR A SUNAT
// =============================================
$ws = "https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService";
$header = array(
    "Content-type: text/xml; charset=\"utf-8\"",
    "Accept: text/xml",
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "SOAPAction: ",
    "Content-length: ".strlen($xml_envio)
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $ws);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_envio);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");
$response = curl_exec($ch);


// =============================================
// PASO 06 - PROCESAR RESPUESTA (CDR)
// =============================================
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if($httpcode == 200){
    $doc = new DOMDocument();
    $doc->loadXML($response);
    if(isset($doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue)){
        $cdr = $doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue;
        $cdr = base64_decode($cdr);
        file_put_contents($carpetacdr."R-".$nombrezip, $cdr);
        $zip = new ZipArchive;
        if($zip->open($carpetacdr."R-".$nombrezip) === true){
            $zip->extractTo($carpetacdr.'R-'.$nombrexml);
            $zip->close();
        }
        echo "FACTURA ENVIADA CORRECTAMENTE";
    } else {
        $codigo  = $doc->getElementsByTagName("faultcode")->item(0)->nodeValue;
        $mensaje = $doc->getElementsByTagName("faultstring")->item(0)->nodeValue;
        echo "error ".$codigo.": ".$mensaje;
    }
} else {
    echo curl_error($ch);
    echo "Problema de conexión";
}
curl_close($ch);

?>