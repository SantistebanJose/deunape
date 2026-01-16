<?php
include("bd.php");





if (isset($_POST["accion"])) {
    $accion = $_POST["accion"];
    controladorConsultasPTMRE($accion);
}

function controladorConsultasPTMRE($accion)
{
    switch ($accion) {
        case 'DETALLEVENTA_VENTA_ID':
            if (isset($_POST["venta_id"])) {
                $venta_id_ = $_POST["venta_id"];
                $result = fnListarDetalleVentaID($venta_id_);
                echo json_encode($result);
            }
            break;
        case 'VENTAIDCLIENTEDEMRD':
            if (isset($_POST["cliente_id"])) {
                $cliente_id_ = $_POST["cliente_id"];
                $result = fnListForDeudaPendientes($cliente_id_);
                echo json_encode($result);
            }
            break;
        case 'PAGOS_ABONADOS_CLIENTE_ID':
            if (isset($_POST["cliente_id"])) {
                $cliente_id_ = $_POST["cliente_id"];
                $result = fnListForAbonosConsolidadoCliente($cliente_id_);
                echo json_encode($result);
            }
            break;
        case 'DETALLE_ABONO_DEUDA_CLIENTEDDRMD':
            if (isset($_POST["abono_id"])) {
                $abono_id = $_POST["abono_id"];
                $result = fnListForAbonosClientePorVentaPagadas($abono_id);
                echo json_encode($result);
            }
            break;
        case 'BUSQUEDAD_PROVEEDOR':
            if (isset($_POST["cadenaBusqueda"])) {
                $cadena = $_POST["cadenaBusqueda"];
                $result = fnListadoProveedores($cadena);

                // Si no hay resultados, devuelves un array vacío
                echo json_encode($result ? $result : []);
            }
            break;
        case 'BUSQUEDAD_FILTRO_ARTICULOS':
            if (isset($_POST["cadenaBusqueda"])) {
                $cadena = $_POST["cadenaBusqueda"];
                $result = fnListadoProductos($cadena);

                echo json_encode($result ? $result : []);
            }
            break;
        //EJECUTARETL
        case 'EJECUTARETL':
            if (isset($_POST["EJECUTARETL"])) {
                $cadena = $_POST["EJECUTARETL"];
                $result = fnEjecutarETL();
                echo json_encode($result ? $result : []);
            }
            break;
        
        case 'EJECUTARETLARTICULOSNUBE':
            if (isset($_POST["EJECUTARETLARTICULOSNUBE"])) {
                $cadena = $_POST["EJECUTARETLARTICULOSNUBE"];
                $result = fnEjecutarETLArticulosNube();
                echo json_encode($result ? $result : []);
            }
            break;
        case 'RANKING_CLIENTES':
            $datos = fnRankingClientes();
            echo json_encode($datos);
            break;
        case 'VENTAS_POR_RANGO':
            $fecha_inicio = $_POST['fecha_inicio'];
            $fecha_fin = $_POST['fecha_fin'];
            $sucursal_id = isset($_SESSION['sucursal_id']) ? $_SESSION['sucursal_id'] : null;
            echo json_encode(fnListForVentasPorRango($fecha_inicio, $fecha_fin, $sucursal_id));
            break;
    }
}
function executeQuery(string $query, array $params = []): array
{
    global $conectar;
    try {
        $orden = $conectar->prepare($query);
        $orden->execute($params);
        $datos = $orden->fetchAll(PDO::FETCH_ASSOC);
        $orden->closeCursor();
        return $datos;
    } catch (PDOException $e) {
        echo "Error de conexión: " . $e->getMessage();
        return [];
    }
}
function executeQueryv2($query)
{
    global $conectar;
    try {

        $stmt = $conectar->query($query);

        if (!$stmt) {
            throw new Exception("Error en la consulta SQL");
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error de conexión: " . $e->getMessage();
        return [];
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}
function listarInsumosCompra(): array
{
    $query = "
        SELECT 
            ci.id,
            c.fecha,
            ins.descripcion as nombre,
            ins.parte_postre,
            ci.cantidad,
            u.nombre_corto as medida,
            ci.total as precio
        FROM compra AS c
        JOIN rel_insumo_compra AS ci ON c.id=ci.compra_id
        JOIN insumo AS ins ON ci.insumo_id=ins.id
        JOIN unidad AS u ON u.id=ins.unidad_id
        ORDER BY c.fecha ASC
    ";
    return executeQuery($query);
}

function listarUsuarios(): array
{
    $query = "
        SELECT 
            u.id, 
           u.username, 
            CASE 
                WHEN  u.rol = '1' THEN 'ADMINISTRADOR'
                ELSE 'EMPLEADO'
            END AS rol,
            CONCAT(p.numero_documento, ' - ', p.nombres, ' ', p.apellidos) AS persona_concatenada,
            CASE 
                WHEN u.deleted_at IS NULL THEN 'ACTIVO'
                ELSE 'BLOQUEADO'
            END AS estado
        FROM 
            usuario AS u
        INNER JOIN 
            persona AS p 
        ON 
            u.persona_id = p.id
        order BY u.id;
    ";
    return executeQuery($query);
}

function fnListForVentasPorRango($fecha_inicio, $fecha_fin, $sucursal_id) {
    $query = "
        SELECT 
            v.fecha_fin_transaccion,
            concat('T',LPAD(v.id::TEXT,8,'0'),'-','F',to_char(v.fecha_fin_transaccion::date, 'YYYYMMDD')) as codigo_tiket,
            v.id AS venta_id, 
            CASE 
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 0 THEN UPPER('Domingo')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 1 THEN UPPER('Lunes')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 2 THEN UPPER('Martes')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 3 THEN UPPER('Miércoles')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 4 THEN UPPER('Jueves')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 5 THEN UPPER('Viernes')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 6 THEN UPPER('Sábado')
            END AS dia_nombre,
            CONCAT(p.nombres, ' ', p.apellidos) AS cliente, 
            TO_CHAR(v.fecha_fin_transaccion, 'YYYY-MM-DD') AS fecha, 
            TO_CHAR(v.fecha_fin_transaccion, 'HH12:MI:SS AM') AS hora, 
            p.telefonomovil AS telefonomovil_cliente,
            p.email AS email_cliente, 
            p.numero_documento AS numero_doc_cliente,
            CONCAT(us.id, '-', usua.nombres, ', ', usua.apellidos) AS usuario, 
            v.atencion_final_usuario,
            p.id AS id_persona,
            v.usuario_id,
            v.monto_venta_final,
            v.total, 
            (v.total - v.monto_venta_final) as perdida_utilidad,
            CASE 
                WHEN v.estado_pago = 'P' THEN 'PAGADO'
                WHEN v.estado_pago = 'C' THEN 'CREDITO'
            END AS estado_pago,
            v.estado_final,
            du.acumulado AS acumulado_deuda
        FROM venta AS v
        LEFT JOIN deuda AS du ON v.id=du.id_venta
        INNER JOIN usuario AS us ON v.usuario_id = us.id  
        INNER JOIN persona AS usua ON us.persona_id = usua.id
        LEFT JOIN persona AS p ON v.cliente_id = p.id
        WHERE v.estado_venta = 'VR' 
        AND v.deleted_at IS NULL
        AND us.sucursal_id = :sucursal_id
        AND v.fecha_fin_transaccion::DATE BETWEEN :fecha_inicio AND :fecha_fin
        ORDER BY v.fecha_fin_transaccion DESC
    ";
    
    return executeQuery($query, [
        'fecha_inicio' => $fecha_inicio,
        'fecha_fin' => $fecha_fin,
        'sucursal_id' => $sucursal_id
    ]);
}

function listarPersonas(): array
{
    $query = "
        select id,
        numero_documento, 
        tipo_persona,
        condicion, 
        nombres ,
        apellidos,
        fecha_nacimiento,
        telefonofijo,
        telefonomovil,
        email,
        direccion,
        nombre_comercial, 
        razon_social ,deleted_at
        from persona 
        where deleted_at is null   
        order BY id;
    ";
    return executeQuery($query);
}


function listarPostres(): array
{
    $query = "SELECT id, nombre, descripcion FROM postre WHERE deleted_at IS NULL";
    return executeQuery($query);
}
function listarCategoria($sucursal_id): array
{
    $query = "SELECT id, abreviatura FROM categoria 
              WHERE deleted_at IS NULL AND sucursal_id = :sucursal_id 
              ORDER BY 1";
    return executeQuery($query, ["sucursal_id" => $sucursal_id]);
}

function listarImpuestos(): array
{
    $query = "SELECT id, nombre FROM impuesto 
              WHERE deleted_at IS NULL
              ORDER BY 1";
    return executeQuery($query);
}

function listarDimension($sucursal_id): array
{
    $query = "SELECT id, medida FROM dimension 
              WHERE deleted_at IS NULL AND sucursal_id = :sucursal_id 
              ORDER BY 1";
    return executeQuery($query, ["sucursal_id" => $sucursal_id]);
}
function listarEscala($sucursal_id): array
{
    $query = "SELECT id, abreviatura FROM escala 
              WHERE deleted_at IS NULL AND sucursal_id = :sucursal_id 
              ORDER BY 1";
    return executeQuery($query, ["sucursal_id" => $sucursal_id]);
}
function listarTipoArticulos($sucursal_id): array
{
    $query = "SELECT id, abreviatura FROM tipo 
              WHERE deleted_at IS NULL AND sucursal_id = :sucursal_id 
              ORDER BY 1";
    return executeQuery($query, ["sucursal_id" => $sucursal_id]);
}

function listarMovimientos($sucursal_id): array
{
    $query = "SELECT * FROM movimiento WHERE deleted_at IS NULL AND sucursal_id = :sucursal_id AND id NOT IN (1,4,6,15) ORDER BY 1";
    return executeQuery($query, ["sucursal_id" => $sucursal_id]);
}

function listarProductosVenta1($sucursal_id): array
{
    $query = "SELECT * FROM view_articulos WHERE precio_venta is not null AND sucursal_id = :sucursal_id; ";

    return executeQuery($query, ["sucursal_id" => $sucursal_id]);
}

function listarTipoArticuloMantenimiento($sucursal_id): array
{
    $query = "select * from tipo where sucursal_id = :xxx";
    return executeQuery($query, ["xxx" => $sucursal_id]);
}

function listarDimensionArticuloMantenimiento($sucursal_id): array
{
    $query = "select * from dimension where sucursal_id = :sucursal_id";
    return executeQuery($query, ["sucursal_id" => $sucursal_id]);
}

function listarEscalaArticuloMantenimiento($sucursal_id): array
{
    $query = "select * from escala where sucursal_id = :sucursal_id";
    return executeQuery($query, ["sucursal_id" => $sucursal_id]);
}

function listarCategoriaArticuloMantenimiento($sucursal_id): array
{
    $query = "SELECT * FROM categoria 
              WHERE sucursal_id = :sucursal_id 
              ORDER BY id";
    return executeQuery($query, ["sucursal_id" => $sucursal_id]);
}

function listarArticuloSinview($sucursal_id = null): array
{
    $query = "
    SELECT 
    a.id as articulo_id,
    a.nombre as articulo,
    a.precio_venta,
    a.stock,
    a.deleted_at,
    a.corte,
    a.marca, 
    d.medida as dimension, 
    t.abreviatura as tipo,
    e.abreviatura as escala,
    c.abreviatura as categoria, 
    a.disponibilidad_venta_fh,
    a.color,
    CASE 
        WHEN a.color IS NULL THEN 'SIN COLOR'
        ELSE a.color
    END color_v2,
    a.*
    FROM articulo a
    LEFT JOIN categoria c ON c.id = a.categoria_id 
    LEFT JOIN dimension d ON d.id = a.dimension_id 
    LEFT JOIN tipo t ON t.id = a.tipo_id
    LEFT JOIN escala e ON e.id = a.escala_id
    WHERE a.deleted_at IS NULL
    AND a.sucursal_id = :sucursal_id
    ORDER BY a.id DESC";
    
    return executeQuery($query, ['sucursal_id' => $sucursal_id]);
}
function listarVentaReservaCorte($sucursal_id = null): array
{
    $query = "
        SELECT 
            v.id AS venta_id, 
            TO_CHAR(v.created_at, 'YYYY-MM-DD') AS fecha, 
            TO_CHAR(v.created_at, 'HH12:MI:SS AM') AS hora, 
            CONCAT(p.nombres, ' ', p.apellidos) AS cliente, 
            p.telefonomovil as telefonomovil_cliente,
            p.email as email_cliente, 
            p.numero_documento as numero_doc_cliente,
            CONCAT(us.id,'-',usua.nombres, ', ', usua.apellidos) AS usuario, 
            p.id as id_persona,
            v.usuario_id,
            v.total, 
            CASE 
                WHEN v.estado_venta = 'VR' THEN 'VENTA REALIZADA'
                WHEN v.estado_venta = 'R' THEN 'RESERVA'
                ELSE 'Estado Desconocido'
            END AS estado_venta
        FROM venta AS v
        INNER JOIN usuario AS us ON v.usuario_id = us.id
        INNER JOIN persona AS usua ON us.persona_id = usua.id
        INNER JOIN persona AS p ON v.cliente_id = p.id
        WHERE v.deleted_at IS NULL
        AND us.sucursal_id = :sucursal_id
        AND v.estado_venta <> 'VR';
    ";
    return executeQuery($query, ['sucursal_id' => $sucursal_id]);
}

function listarVentasNoDeclaradas()
{
    $sql = "
        --select * from emisor ;
        --update emisor set usuario_sol = 'FACVYSAM', clave_sol = 'Jose04_42696143'

        --delete from comprobante
        --select * from comprobante
        -- COMPROBANTES NO DECLARADOS
        SELECT 
        --cb.*,
        -- otros campos que ya tienes
        v.id as venta_id,
        concat(SUBSTRING(v.tipo_comprobante,1,1),'001') as serie,

        v.id as correlativo,
        concat(SUBSTRING(v.tipo_comprobante,1,1),'001-',lpad(v.id::text, 6, '0')) as serie_correltavio_referencial,
        concat('P', LPAD(p.id::TEXT, 6, '0'), 'F', to_char(p.created_at::date, 'YYYYMMDD')) as codigo_pago,
        case
            WHEN p1.tipo_persona = 'JURIDICA' and v.js_detalles_receptor_factura is null then '6'
            WHEN p1.tipo_persona = 'NATURAL' and v.js_detalles_receptor_factura is null  then '1'
            WHEN v.tipo_comprobante = 'FACTURA' AND v.js_detalles_receptor_factura is not null THEN '6'
            else ''
        end ca_cliente_tipo_documento_sunat,
        p1.direccion as ca_cliente_direccion_sunat,
        case
            WHEN p1.numero_documento = '999999999' THEN ''
            WHEN v.tipo_comprobante = 'FACTURA' AND v.js_detalles_receptor_factura is not null THEN v.js_detalles_receptor_factura->>'ruc'
            else p1.numero_documento
        end ca_cliente_numero_documento_sunat,
        p.monto_venta_final,
        case
            WHEN v.tipo_comprobante = 'FACTURA' AND v.js_detalles_receptor_factura is not null THEN v.js_detalles_receptor_factura->>'razon_social'
            
            WHEN p1.tipo_persona = 'JURIDICA' then p1.razon_social
            WHEN p1.tipo_persona = 'NATURAL' then CONCAT(p1.nombres, ' ', p1.apellidos)
            else 'CLIENTE VARIOS'
        end AS ca_cliente_cliente_sunat, 
        TO_CHAR(p.created_at, 'YYYY-MM-DD') as fecha,
        p.created_at::TIME as hora,
        TO_CHAR(p.created_at, 'HH12:MI:SS AM') as hora_formateada,
        p.monto_venta_original,
        p.monto_venta_final,
        v.tipo_comprobante,
        -- Cálculo del descuento directamente en SQL
        (p.monto_venta_original - p.monto_venta_final) AS descuento,  -- Calculamos el descuento en la consulta
        (
            SELECT jsonb_agg(
                jsonb_build_object(
                    'rel_venta_articulo_id', rva.id,
                    'venta_id', rva.venta_id,
                    'articulo_id', rva.articulo_id,
                    'descripcion_movimiento', m.descripcion,
                    'descripcion_articulo', CASE 
                        WHEN ar.dimension_id IS NOT NULL THEN CONCAT(ar.nombre, ' (', dim.medida, ')')
                        WHEN ar.nombre IS NULL THEN m.descripcion
                        ELSE ar.nombre 
                    END,
                    'cantidad_sunat', CASE 
                        WHEN m.id = 1 THEN rva.cantidad
                        else 1
                    END,
                    'cantidad_real', rva.cantidad,
                    'precio_unitario_articulo', rva.precio_unitario_articulo,
                    'minutos', rva.minutos,
                    'costo_por_minuto', rva.costo_por_minuto,
                    'pu_con_igv', rva.sub_total,
                    'afectacion', 'SI',
                    'pu_sin_igv', (rva.sub_total / 1.18),
                    'IGV', ((rva.sub_total) - (rva.sub_total / 1.18)),
                    'unidad_medida', 'NIU',
                    'codigo_igv', 1000,
                    'afecto_igv_sunat', 'S',
                    'codigo_afectación', 10,
                    'valor_agregado', 'VAT',
                    'factor_icbper', 0.30,
                    'icbper', 0
                )
            ) AS resultado_json
            FROM rel_venta_articulo AS rva
            JOIN movimiento AS m ON rva.movimiento_id = m.id 
            LEFT JOIN articulo AS ar ON rva.articulo_id = ar.id
            LEFT JOIN dimension AS dim ON ar.dimension_id = dim.id
            WHERE rva.venta_id = p.id_venta
        ) AS js_detalle_venta,
        (
            SELECT 
            json_agg(
                jsonb_build_object(
                    'ID_DETALLE', dfp.id,
                    'FORMA_PAGO', fp.nombre,
                    'MONTO', dfp.monto,
                    'COLOR', fp.color
                )
            ) AS resultado
            FROM detalle_forma_pago dfp
            JOIN forma_pago fp ON dfp.id_forma_pago = fp.id
            WHERE dfp.id_venta = p.id_venta
        ) as js_detalle_forma_pago
        FROM pago p
        JOIN venta v ON p.id_venta = v.id AND v.tipo_comprobante IN ('BOLETA','FACTURA')
        JOIN persona p1 ON p1.id = v.cliente_id
        LEFT JOIN comprobante cb ON v.id = cb.venta_id
        WHERE cb.venta_id is null 
        --AND 
        --WHERE p.created_at >= (CURRENT_TIMESTAMP - INTERVAL '2 days')
        order by 1 desc
    ";
    return executeQuery($sql);
}
function listarVentasPagadasParaComprobantes(): array
{
    $query = "
        SELECT 
            -- otros campos que ya tienes
            v.id as venta_id,
            concat(SUBSTRING(v.tipo_comprobante,1,1),'001') as serie,
            
            v.id as correlativo,
            concat(SUBSTRING(v.tipo_comprobante,1,1),'001-',lpad(v.id::text, 6, '0')) as serie_correltavio_referencial,
            concat('P', LPAD(p.id::TEXT, 6, '0'), 'F', to_char(p.created_at::date, 'YYYYMMDD')) as codigo_pago,
            case
                WHEN p1.tipo_persona = 'JURIDICA' and v.js_detalles_receptor_factura is null then '6'
                WHEN p1.tipo_persona = 'NATURAL' and v.js_detalles_receptor_factura is null  then '1'
                WHEN v.tipo_comprobante = 'FACTURA' AND v.js_detalles_receptor_factura is not null THEN '6'
                else ''
            end ca_cliente_tipo_documento_sunat,
            p1.direccion as ca_cliente_direccion_sunat,
            case
                WHEN p1.numero_documento = '999999999' THEN ''
                WHEN v.tipo_comprobante = 'FACTURA' AND v.js_detalles_receptor_factura is not null THEN v.js_detalles_receptor_factura->>'ruc'
                else p1.numero_documento
            end ca_cliente_numero_documento_sunat,
            p.monto_venta_final,
            case
                WHEN v.tipo_comprobante = 'FACTURA' AND v.js_detalles_receptor_factura is not null THEN v.js_detalles_receptor_factura->>'razon_social'
                
                WHEN p1.tipo_persona = 'JURIDICA' then p1.razon_social
                WHEN p1.tipo_persona = 'NATURAL' then CONCAT(p1.nombres, ' ', p1.apellidos)
                else 'CLIENTE VARIOS'
            end AS ca_cliente_cliente_sunat, 
            TO_CHAR(p.created_at, 'YYYY-MM-DD') as fecha,
            p.created_at::TIME as hora,
            TO_CHAR(p.created_at, 'HH12:MI:SS AM') as hora_formateada,
            p.monto_venta_original,
            p.monto_venta_final,
            v.tipo_comprobante,
            -- Cálculo del descuento directamente en SQL
            (p.monto_venta_original - p.monto_venta_final) AS descuento,  -- Calculamos el descuento en la consulta
            (
                SELECT jsonb_agg(
                    jsonb_build_object(
                        'rel_venta_articulo_id', rva.id,
                        'venta_id', rva.venta_id,
                        'articulo_id', rva.articulo_id,
                        'descripcion_movimiento', m.descripcion,
                        'descripcion_articulo', CASE 
                            WHEN ar.dimension_id IS NOT NULL THEN CONCAT(ar.nombre, ' (', dim.medida, ')')
                            WHEN ar.nombre IS NULL THEN m.descripcion
                            ELSE ar.nombre 
                        END,
                        'cantidad_sunat', CASE 
                            WHEN m.id = 1 THEN rva.cantidad
                            else 1
                        END,
                        'cantidad_real', rva.cantidad,
                        'precio_unitario_articulo', rva.precio_unitario_articulo,
                        'minutos', rva.minutos,
                        'costo_por_minuto', rva.costo_por_minuto,
                        'pu_con_igv', rva.sub_total,
                        'afectacion', 'SI',
                        'pu_sin_igv', (rva.sub_total / 1.18),
                        'IGV', ((rva.sub_total) - (rva.sub_total / 1.18)),
                        'unidad_medida', 'NIU',
                        'codigo_igv', 1000,
                        'afecto_igv_sunat', 'S',
                        'codigo_afectación', 10,
                        'valor_agregado', 'VAT',
                        'factor_icbper', 0.30,
                        'icbper', 0
                    )
                ) AS resultado_json
                FROM rel_venta_articulo AS rva
                JOIN movimiento AS m ON rva.movimiento_id = m.id 
                LEFT JOIN articulo AS ar ON rva.articulo_id = ar.id
                LEFT JOIN dimension AS dim ON ar.dimension_id = dim.id
                WHERE rva.venta_id = p.id_venta
            ) AS js_detalle_venta,
            (
                SELECT 
                json_agg(
                    jsonb_build_object(
                        'ID_DETALLE', dfp.id,
                        'FORMA_PAGO', fp.nombre,
                        'MONTO', dfp.monto,
                        'COLOR', fp.color
                    )
                ) AS resultado
                FROM detalle_forma_pago dfp
                JOIN forma_pago fp ON dfp.id_forma_pago = fp.id
                WHERE dfp.id_venta = p.id_venta
            ) as js_detalle_forma_pago
        FROM pago p
        JOIN venta v ON p.id_venta = v.id AND v.tipo_comprobante IN ('BOLETA','FACTURA')
        JOIN persona p1 ON p1.id = v.cliente_id
        LEFT JOIN comprobante cb ON v.id = cb.venta_id
        WHERE cb.venta_id is null 
        AND p.created_at >= (CURRENT_TIMESTAMP - INTERVAL '2 days')
        order by 1 desc

    ";
    return executeQuery($query);
}
function listComprobantesDeclarados(): array
{
    $query = "select * from comprobante where estado_envio = true order by 1";
    return executeQuery($query);
}
function listarFormaPago(): array
{
    $query = "SELECT *,updated_at::date as fecha, TO_CHAR(updated_at, 'HH12:MI:SS AM') as hora FROM forma_pago WHERE deleted_at IS NULL AND unsubscribe IS NULL  order by orden";
    return executeQuery($query);
}
function listarFormaPago_v2($sucursal_id = null): array
{
    if ($sucursal_id === null) {
        $query = "SELECT *,updated_at::date as fecha, TO_CHAR(updated_at, 'HH12:MI:SS AM') as hora FROM forma_pago WHERE deleted_at IS NULL order by id";
        return executeQuery($query);
    }
    $query = "SELECT *,updated_at::date as fecha, TO_CHAR(updated_at, 'HH12:MI:SS AM') as hora FROM forma_pago WHERE deleted_at IS NULL AND sucursal_id = :sucursal_id order by id";
    return executeQuery($query, ['sucursal_id' => $sucursal_id]);
}
function listarEmpleados(): array
{
    $query = "
    SELECT 
        p.id, 
        p.numero_documento, 
        CASE 
            WHEN p.nombres IS NOT NULL AND p.apellidos IS NOT NULL THEN CONCAT(nombres, ' ', apellidos)
            WHEN p.razon_social IS NOT NULL THEN p.razon_social
            ELSE '-'
        END AS empleado, 
        COALESCE(p.condicion, '-') AS condicion, 
        COALESCE(p.telefonomovil, COALESCE(p.telefonofijo, '-')) AS telefono, 
        p.deleted_at 
    FROM persona p
    JOIN usuario u on p.id = u.persona_id 
    
    WHERE p.deleted_at IS NULL AND u.deleted_at_v2 is null;
    ";
    return executeQuery($query);
}
function fnListarEmisor()
{
    $sql = "
        select * from emisor limit 1
    ";
    return executeQuery($sql);
}
function fnSiguienteCorrelativo($tipo_comprobante)
{
    $sql = "
        select 
        coalesce(max(correlativo),0)+1 as correlativo_siguiente,
        LPAD((coalesce(max(correlativo),0)+1)::text,6,0::text) as correlativo_texto
        from comprobante where tipo_comprobante=:tipo_comprobante AND estado_envio=true
         and mensaje_sunat='Ok';;
    ";
    return executeQuery($sql, [":tipo_comprobante" => $tipo_comprobante]);
}
function fnListForVentasDiarias($sucursal_id = null): array
{
    $query = "
            SELECT 
            v.fecha_fin_transaccion,
            concat('T',LPAD(v.id::TEXT,8,'0'),'-','F',to_char(v.fecha_fin_transaccion::date, 'YYYYMMDD')) as codigo_tiket,
            v.id AS venta_id, 
            CASE 
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 0 THEN UPPER('Domingo')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 1 THEN UPPER('Lunes')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 2 THEN UPPER('Martes')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 3 THEN UPPER('Miércoles')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 4 THEN UPPER('Jueves')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 5 THEN UPPER('Viernes')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 6 THEN UPPER('Sábado')
            END AS dia_nombre,
            CONCAT(p.nombres, ' ', p.apellidos) AS cliente, 
            TO_CHAR(v.fecha_fin_transaccion, 'YYYY-MM-DD') AS fecha, 
            TO_CHAR(v.fecha_fin_transaccion, 'HH12:MI:SS AM') AS hora, 
            
            p.telefonomovil AS telefonomovil_cliente,
            p.email AS email_cliente, 
            p.numero_documento AS numero_doc_cliente,
            CONCAT(us.id, '-', usua.nombres, ', ', usua.apellidos) AS usuario, 
            v.atencion_final_usuario,
            p.id AS id_persona,
            v.usuario_id,
            v.monto_venta_final,
            v.total, 
            (v.total - v.monto_venta_final)as perdida_utilidad,
            CASE 
                WHEN v.estado_pago = 'P' THEN 'PAGADO'
                WHEN v.estado_pago = 'C' THEN 'CREDITO'
            END AS estado_pago,
            v.estado_final,
            du.acumulado AS acumulado_deuda
            FROM venta AS v
        LEFT JOIN deuda AS du ON v.id=du.id_venta
        INNER JOIN usuario AS us ON v.usuario_id = us.id  
        INNER JOIN persona AS usua ON us.persona_id = usua.id
        LEFT JOIN persona AS p ON v.cliente_id = p.id
        WHERE v.estado_venta = 'VR' 
        AND v.deleted_at IS NULL
        AND us.sucursal_id = :sucursal_id
        AND v.fecha_fin_transaccion::DATE = current_date
        --AND v.fecha_fin_transaccion >= date_trunc('week', CURRENT_DATE)
        --AND v.fecha_fin_transaccion < CURRENT_DATE + INTERVAL '1 day'
        ORDER BY v.fecha_fin_transaccion;
    ";
    return executeQuery($query, ['sucursal_id' => $sucursal_id]);
}


function fnListForVentasSemanales($sucursal_id = null): array
{
    $query = "
            SELECT 
            v.fecha_fin_transaccion,
            concat('T',LPAD(v.id::TEXT,8,'0'),'-','F',to_char(v.fecha_fin_transaccion::date, 'YYYYMMDD')) as codigo_tiket,
            v.id AS venta_id, 
            CASE 
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 0 THEN UPPER('Domingo')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 1 THEN UPPER('Lunes')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 2 THEN UPPER('Martes')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 3 THEN UPPER('Miércoles')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 4 THEN UPPER('Jueves')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 5 THEN UPPER('Viernes')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 6 THEN UPPER('Sábado')
            END AS dia_nombre,
            CONCAT(p.nombres, ' ', p.apellidos) AS cliente, 
            TO_CHAR(v.fecha_fin_transaccion, 'YYYY-MM-DD') AS fecha, 
            TO_CHAR(v.fecha_fin_transaccion, 'HH12:MI:SS AM') AS hora, 
            
            p.telefonomovil AS telefonomovil_cliente,
            p.email AS email_cliente, 
            p.numero_documento AS numero_doc_cliente,
            CONCAT(us.id, '-', usua.nombres, ', ', usua.apellidos) AS usuario, 
            v.atencion_final_usuario,
            p.id AS id_persona,
            v.usuario_id,
            v.monto_venta_final,
            v.total, 
            (v.total - v.monto_venta_final)as perdida_utilidad,
            CASE 
                WHEN v.estado_pago = 'P' THEN 'PAGADO'
                WHEN v.estado_pago = 'C' THEN 'CREDITO'
            END AS estado_pago,
            v.estado_final,
            du.acumulado AS acumulado_deuda
            FROM venta AS v
        LEFT JOIN deuda AS du ON v.id=du.id_venta
        INNER JOIN usuario AS us ON v.usuario_id = us.id  
        INNER JOIN persona AS usua ON us.persona_id = usua.id
        LEFT JOIN persona AS p ON v.cliente_id = p.id
        WHERE v.estado_venta = 'VR' 
        AND v.deleted_at IS NULL
        AND us.sucursal_id = :sucursal_id
        AND v.fecha_fin_transaccion >= date_trunc('week', CURRENT_DATE)
        AND v.fecha_fin_transaccion < CURRENT_DATE + INTERVAL '1 day'
        ORDER BY v.fecha_fin_transaccion;
    ";
    return executeQuery($query, ['sucursal_id' => $sucursal_id]);
}
function fnListForVentasTodasLasVentas($sucursal_id = null): array
{
    $query = "
            SELECT 
            v.fecha_fin_transaccion,
            concat('T',LPAD(v.id::TEXT,8,'0'),'-','F',to_char(v.fecha_fin_transaccion::date, 'YYYYMMDD')) as codigo_tiket,
            v.id AS venta_id, 
            CASE 
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 0 THEN UPPER('Domingo')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 1 THEN UPPER('Lunes')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 2 THEN UPPER('Martes')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 3 THEN UPPER('Miércoles')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 4 THEN UPPER('Jueves')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 5 THEN UPPER('Viernes')
                WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 6 THEN UPPER('Sábado')
            END AS dia_nombre,
            CONCAT(p.nombres, ' ', p.apellidos) AS cliente, 
            TO_CHAR(v.fecha_fin_transaccion, 'YYYY-MM-DD') AS fecha, 
            TO_CHAR(v.fecha_fin_transaccion, 'HH12:MI:SS AM') AS hora, 
            
            p.telefonomovil AS telefonomovil_cliente,
            p.email AS email_cliente, 
            p.numero_documento AS numero_doc_cliente,
            CONCAT(us.id, '-', usua.nombres, ', ', usua.apellidos) AS usuario, 
            v.atencion_final_usuario,
            p.id AS id_persona,
            v.usuario_id,
            v.monto_venta_final,
            v.total, 
            (v.total - v.monto_venta_final)as perdida_utilidad,
            CASE 
                WHEN v.estado_pago = 'P' THEN 'PAGADO'
                WHEN v.estado_pago = 'C' THEN 'CREDITO'
            END AS estado_pago,
            v.estado_final,
            du.acumulado AS acumulado_deuda
            FROM venta AS v
        LEFT JOIN deuda AS du ON v.id=du.id_venta
        INNER JOIN usuario AS us ON v.usuario_id = us.id  
        INNER JOIN persona AS usua ON us.persona_id = usua.id
        LEFT JOIN persona AS p ON v.cliente_id = p.id
        WHERE v.estado_venta = 'VR' 
        AND v.deleted_at IS NULL
        AND us.sucursal_id = :sucursal_id
        --AND v.fecha_fin_transaccion >= CURRENT_DATE - INTERVAL '3 months'
        --AND v.fecha_fin_transaccion < CURRENT_DATE + INTERVAL '1 day'
        ORDER BY v.fecha_fin_transaccion;
    ";
    return executeQuery($query, ['sucursal_id' => $sucursal_id]);
}

function fnUltimaVentaPorIdVenta($id_venta): array
{
    $query = "
    WITH with_detalle AS (
        SELECT 
            rva.id AS rel_venta_articulo_id,
            rva.venta_id,
            rva.articulo_id,
            m.descripcion,
            CASE 
                WHEN ar.dimension_id IS NOT NULL THEN
                    CONCAT(ar.nombre, ' (', dim.medida, ')')
                WHEN ar.nombre IS NULL THEN
                    m.descripcion
                ELSE
                    ar.nombre 
            END as descripcion_2,
            rva.cantidad,
            rva.precio_unitario_articulo,
            rva.minutos,
            rva.costo_por_minuto,
            rva.sub_total
        FROM rel_venta_articulo AS rva
        JOIN movimiento as m ON rva.movimiento_id = m.id
        LEFT JOIN articulo AS ar ON rva.articulo_id = ar.id
        LEFT JOIN dimension AS dim ON ar.dimension_id = dim.id
    ),
    with_detalle_pago AS(
        SELECT 
            fpu.id_venta,
            fpu.id as ID_DETALLE,
            fp.nombre as FORMA_PAGO,
            fpu.monto
        FROM detalle_forma_pago fpu
        JOIN forma_pago fp ON fpu.id_forma_pago = fp.id
    )
    SELECT 
    v.tipo_comprobante,

    concat(SUBSTRING(v.tipo_comprobante,1,1),'001 - ',LPAD(v.id::TEXT,6,'0')) as codigo_tiket,
    v.fecha_fin_transaccion,
    v.id AS venta_id, 
    CONCAT(p.nombres, ' ', p.apellidos) AS cliente, 
    TO_CHAR(v.fecha_fin_transaccion, 'YYYY-MM-DD') AS fecha, 
    TO_CHAR(v.fecha_fin_transaccion, 'HH12:MI:SS AM') AS hora, 
    
    p.telefonomovil AS telefonomovil_cliente,
    p.email AS email_cliente, 
    p.numero_documento AS numero_doc_cliente,
    CONCAT(usua.nombres, ', ', usua.apellidos) AS usuario, 
    v.atencion_final_usuario,
    p.id AS id_persona,
    v.usuario_id,
    v.monto_venta_final,
    v.total, 
    (v.total - v.monto_venta_final)as perdida_utilidad,
    CASE 
        WHEN v.estado_pago = 'P' THEN 'PAGADO'
        WHEN v.estado_pago = 'C' THEN 'CREDITO'
    END AS estado_pago,
    v.estado_final,
    (
        SELECT jsonb_agg(
            jsonb_build_object(
                'rel_venta_articulo_id', wf.rel_venta_articulo_id,
                'venta_id', wf.venta_id,
                'articulo_id',wf.articulo_id,
                'descripcion',wf.descripcion,
                'descripcion_2',wf.descripcion_2,
                'cantidad',wf.cantidad,
                'precio_unitario_articulo',wf.precio_unitario_articulo,
                'minutos',wf.minutos,
                'costo_por_minuto',wf.costo_por_minuto,
                'sub_total',wf.sub_total
            )
        )
        FROM with_detalle as wf
        WHERE wf.venta_id = v.id
    ) AS js_detalle,
    (
        SELECT jsonb_agg(
            jsonb_build_object(
                'id_venta', id_venta,
                'id_detalle',wdf.ID_DETALLE,
                'forma_pago',wdf.FORMA_PAGO,
                'monto',wdf.monto
            )
        )
        FROM with_detalle_pago wdf
        WHERE wdf.id_venta = v.id
    )as js_detalle_forma_pago
    FROM venta AS v
    LEFT JOIN deuda AS du ON v.id=du.id_venta
    INNER JOIN usuario AS us ON v.usuario_id = us.id  
    INNER JOIN persona AS usua ON us.persona_id = usua.id
    LEFT JOIN persona AS p ON v.cliente_id = p.id
    WHERE v.estado_venta = 'VR' 
    AND v.id = :idVenta
    AND v.deleted_at IS NULL
    --AND v.fecha_fin_transaccion::DATE = current_date 
    ORDER BY v.fecha_fin_transaccion;
    ";
    return executeQuery($query, ['idVenta' => $id_venta]);
}
function fnListarDetalleVentaID($idVenta): array
{
    $query = "
        SELECT 
        rva.id AS rel_venta_articulo_id,
        rva.venta_id,
        rva.articulo_id,
        m.descripcion,
        CASE 
            WHEN ar.dimension_id IS NOT NULL THEN
                CONCAT(ar.nombre, ' (', dim.medida, ')')
            WHEN ar.nombre IS NULL THEN
                m.descripcion
            ELSE
                ar.nombre 
        END as descripcion,
        rva.cantidad,
        rva.precio_unitario_articulo,
        rva.minutos,
        rva.costo_por_minuto,
        rva.sub_total
        FROM rel_venta_articulo AS rva
        JOIN movimiento as m ON rva.movimiento_id = m.id
        LEFT JOIN articulo AS ar ON rva.articulo_id = ar.id
        LEFT JOIN dimension AS dim ON ar.dimension_id = dim.id
        WHERE rva.venta_id = :idVenta;";
    return executeQuery($query, ['idVenta' => $idVenta]);
}

function fnListForClientesDeuda($sucursal_id = null): array
{
    $query = "
    SELECT 
        DISTINCT
        cliente.id AS cliente_id,
        cliente.numero_documento,
        cliente.nombres,
        cliente.apellidos,
        cliente.telefonofijo,
        cliente.telefonomovil,
        cliente.email,
        CONCAT(cliente.nombres, ' ', cliente.apellidos) AS cliente, 
        (SELECT SUM (monto-acumulado) FROM deuda WHERE cliente_id=cliente.id AND id_venta IN (SELECT id FROM venta WHERE usuario_id IN (SELECT id FROM usuario WHERE sucursal_id = :sucursal_id))) as monto_deuda_pendiente
    FROM 
    persona as cliente
    JOIN deuda as d ON d.cliente_id = cliente.id
    JOIN venta as v ON v.id = d.id_venta
    JOIN usuario as us ON us.id = v.usuario_id
    WHERE d.estado='PENDIENTE'
    AND us.sucursal_id = :sucursal_id;
    ";
    return executeQuery($query, ['sucursal_id' => $sucursal_id]);
}
function fnListForClientesDeudaPagasAndNoPagadas($sucursal_id = null): array
{
    $query = "
    SELECT 
        DISTINCT
        cliente.id AS cliente_id,
        cliente.numero_documento,
        cliente.nombres,
        cliente.apellidos,
        cliente.telefonofijo,
        cliente.telefonomovil,
        cliente.email,
        CONCAT(cliente.nombres, ' ', cliente.apellidos) AS cliente, 
        (SELECT SUM (monto-acumulado) FROM deuda WHERE cliente_id=cliente.id AND id_venta IN (SELECT id FROM venta WHERE usuario_id IN (SELECT id FROM usuario WHERE sucursal_id = :sucursal_id))) as monto_deuda_pendiente
    FROM 
    persona as cliente
    JOIN deuda as d ON d.cliente_id = cliente.id
    JOIN venta as v ON v.id = d.id_venta
    JOIN usuario as us ON us.id = v.usuario_id
    WHERE us.sucursal_id = :sucursal_id;
    --WHERE d.estado='PENDIENTE';
    ";
    return executeQuery($query, ['sucursal_id' => $sucursal_id]);
}
function fnListForDeudaPendientes($idCliente): array
{
    $query = "
    SELECT 
    id_venta,
    created_at::date,
    monto,
    CONCAT(created_at::date,' [S/',(monto-acumulado),'] ','<b>',estado,'</b>')as formato,
    acumulado,
    (monto-acumulado) AS deuda_pendiente
    FROM deuda 
    WHERE estado <>'PAGADO'
    AND cliente_id= :id_clientedemrd;
    ";
    return executeQuery($query, ['id_clientedemrd' => $idCliente]);
}
function fnListForAbonosConsolidadoCliente($idCliente): array
{
    $query = "
    SELECT 
        d.id_venta AS id_general,
        'id_venta' as nombre_id,
        'PAGO INICIAL' as estacion,
        CONCAT('<b>[Pago Inicial] </b>',' ID VENTA: ',d.id_venta,' - VENTA de ',d.created_at::date,' [S/',monto_inicial,']')as formato,
        CONCAT(c.nombres, ' ', c.apellidos) AS cliente, 

        d.created_at::DATE as fecha,
        TO_CHAR(d.created_at, 'HH12:MI AM') AS hora,
        d.created_at AS fecha_general,
        (SELECT SUM(monto) FROM detalle_forma_pago_deuda WHERE id_deuda=d.id) AS monto,
        (
            SELECT 
            json_agg
            (
                jsonb_build_object(
                    'ID_DETALLE',fpu.id,
                    'FORMA_PAGO', fp.nombre,
                    'MONTO', fpu.monto,
                    'COLOR',fp.color
                )
            ) AS resultado
            FROM detalle_forma_pago_deuda fpu
            JOIN forma_pago fp ON fpu.id_forma_pago = fp.id
            WHERE fpu.id_deuda=d.id
        )::JSON AS js_detalle_forma_pago,
        d.estado as estado_deuda,
        d.monto as monto_deuda
    FROM deuda d
    JOIN persona as c ON c.id=d.cliente_id AND d.monto_inicial>0
    WHERE d.cliente_id=:idCliente

    UNION ALL
    -- ABONOS DE CLIENTES 
    SELECT 
        a.id as id_general,
        'abono_id',
        'ABONO' as estacion,

        --CONCAT('ID A: ',a.id,' - ', a.created_at::DATE,' [',c.nombres, ' ', c.apellidos,'] - S/',a.monto) AS formato,
        CONCAT('<b>[Pago de Abono] </b>',' ID ABONO: ',a.id,' - ABONO de ', a.created_at::DATE,' [S/',a.monto,']') AS formato2,
        CONCAT(c.nombres, ' ', c.apellidos) AS cliente, 
        a.created_at::date AS fecha,
        TO_CHAR(a.created_at, 'HH12:MI AM') AS hora,
        a.created_at AS fecha_general,
        a.monto,
        (
            SELECT 
            json_agg
            (
                jsonb_build_object(
                    'ID_DETALLE',fpa.id,
                    'FORMA_PAGO', fp.nombre,
                    'MONTO', fpa.monto,
                    'COLOR',fp.color
                )
            ) AS resultado
            FROM detalle_forma_pago_abono fpa
            JOIN forma_pago fp ON fpa.forma_pago_id = fp.id
            WHERE fpa.id_abono=a.id
        )::JSON as js_detalle_forma_pago,
        'ABONADO' AS estad_,
        0 as monto_deuda
    FROM abono AS a
    JOIN persona as c ON c.id=a.cliente_id
    WHERE c.id=:idCliente 
    order by 8 desc
    limit 10;
    ";
    return executeQuery($query, ['idCliente' => $idCliente]);
}
function fnListForAbonosCliente($idCliente): array
{
    $query = "
    SELECT 
    a.id as abono_id,
    CONCAT('ID A: ',a.id,' - ', a.created_at::DATE,' [',c.nombres, ' ', c.apellidos,'] - S/',a.monto) AS formato,
    CONCAT('ID A: ',a.id,' - ', a.created_at::DATE,' [S/',a.monto,']') AS formato2,
    CONCAT(c.nombres, ' ', c.apellidos) AS cliente, 
    a.created_at::date AS fecha,
    TO_CHAR(a.created_at, 'HH12:MI AM') AS hora,
    a.monto
    FROM
    abono AS a
    JOIN persona as c ON c.id=a.cliente_id
    WHERE c.id=:id_clientedemrd ;

    ";
    return executeQuery($query, ['id_clientedemrd' => $idCliente]);
}

function fnListForAbonosClientePorVentaPagadas($idAbono): array
{
    $query = "
    SELECT 
    --d.id AS id_deuda,
    d.id_venta,
    ad.abono_id,
    d.created_at::date as fecha,
    ad.monto,
    CONCAT('<b>ID VENTA: ',d.id_venta,'</b> - VENTA de ',d.created_at::date,' [S/',ad.monto,'] ','',ad.estado_momento,'')as formato,
    (d.monto-d.acumulado) AS deuda_pendiente
    FROM deuda as d
    JOIN abono_deuda AS ad ON ad.deuda_id=d.id
    WHERE ad.abono_id=:abono_id
    ORDER BY 1;
    ";
    return executeQuery($query, ['abono_id' => $idAbono]);
}

function fnListForPagos($sucursal_id = null): array
{
    $query = "   
            SELECT 
            p.created_at,
            p.id as pago_id,
            v.id as venta_id,
            v.tipo_comprobante,
            concat(SUBSTRING(v.tipo_comprobante,1,1),'001-',lpad(v.id::text, 6, '0')) as serie_correltavio_referencial,
            concat('P',LPAD(p.id::TEXT,10,'0'),'F',to_char(p.created_at::date, 'YYYYMMDD')) as codigo,
            CASE 
                WHEN EXTRACT(DOW FROM p.created_at) = 0 THEN UPPER('Domingo')
                WHEN EXTRACT(DOW FROM p.created_at) = 1 THEN UPPER('Lunes')
                WHEN EXTRACT(DOW FROM p.created_at) = 2 THEN UPPER('Martes')
                WHEN EXTRACT(DOW FROM p.created_at) = 3 THEN UPPER('Miércoles')
                WHEN EXTRACT(DOW FROM p.created_at) = 4 THEN UPPER('Jueves')
                WHEN EXTRACT(DOW FROM p.created_at) = 5 THEN UPPER('Viernes')
                WHEN EXTRACT(DOW FROM p.created_at) = 6 THEN UPPER('Sábado')
            END AS dia_nombre,
            
            TO_CHAR(p.created_at, 'YYYY-MM-DD') as fecha,
            TO_CHAR(p.created_at, 'HH12:MI:SS AM') as hora,
            p.monto_venta_original,
            p.monto_venta_final,
            (p.monto_venta_original - p.monto_venta_final) AS utilidad,
            (
                SELECT 
                    jsonb_build_object(
                        'venta_id', v.id,
                        'fecha_fin_transaccion', v.fecha_fin_transaccion,
                        'codigo_tiket',concat('T',LPAD(v.id::TEXT,8,'0'),'-','F',to_char(v.fecha_fin_transaccion::date, 'YYYYMMDD')),
                        'dia_nombre', CASE 
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 0 THEN UPPER('Domingo')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 1 THEN UPPER('Lunes')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 2 THEN UPPER('Martes')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 3 THEN UPPER('Miércoles')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 4 THEN UPPER('Jueves')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 5 THEN UPPER('Viernes')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 6 THEN UPPER('Sábado')
                        END,
                        'cliente', CONCAT(ci.nombres, ' ', ci.apellidos),
                        'fecha', TO_CHAR(v.fecha_fin_transaccion, 'YYYY-MM-DD'),
                        'hora', TO_CHAR(v.fecha_fin_transaccion, 'HH12:MI:SS AM'),
                        'telefonomovil_cliente', ci.telefonomovil,
                        'email_cliente', ci.email,
                        'numero_doc_cliente', ci.numero_documento,
                        'usuario', CONCAT(us.id, '-', usua.nombres, ', ', usua.apellidos),
                        'id_persona', ci.id,
                        'usuario_id', v.usuario_id,
                        'monto_venta_final', v.monto_venta_final,
                        'total', v.total,
                        'perdida_utilidad', (v.monto_venta_final - v.total),
                        'estado_pago', CASE 
                            WHEN v.estado_pago = 'P' THEN 'PAGADO'
                            WHEN v.estado_pago = 'C' THEN 'CREDITO'
                        END,
                        'estado_final', v.estado_final,
                        'acumulado_deuda', du.acumulado
                ) AS resultado_json
                FROM venta AS v
                LEFT JOIN deuda AS du ON v.id = du.id_venta 
                INNER JOIN usuario AS us ON v.usuario_id = us.id  
                INNER JOIN persona AS usua ON us.persona_id = usua.id
                INNER JOIN persona AS ci ON v.cliente_id = ci.id
                WHERE v.id = p.id_venta
                AND v.deleted_at IS NULL
            ) as js_venta,
            (
                SELECT jsonb_agg(
                            jsonb_build_object(
                                'rel_venta_articulo_id', rva.id,
                                'venta_id', rva.venta_id,
                                'articulo_id', rva.articulo_id,
                                'descripcion_movimiento', m.descripcion,
                                'descripcion_articulo', CASE 
                                    WHEN ar.dimension_id IS NOT NULL THEN CONCAT(ar.nombre, ' (', dim.medida, ')')
                                    WHEN ar.nombre IS NULL THEN m.descripcion
                                    ELSE ar.nombre 
                                END,
                                'cantidad', rva.cantidad,
                                'precio_unitario_articulo', rva.precio_unitario_articulo,
                                'minutos', rva.minutos,
                                'costo_por_minuto', rva.costo_por_minuto,
                                'sub_total', rva.sub_total
                            )
                        ) AS resultado_json
                FROM rel_venta_articulo AS rva
                JOIN movimiento AS m ON rva.movimiento_id = m.id 
                LEFT JOIN articulo AS ar ON rva.articulo_id = ar.id
                LEFT JOIN dimension AS dim ON ar.dimension_id = dim.id
                WHERE rva.venta_id = p.id_venta
            )as js_detalle_venta,
            (
                SELECT 
                json_agg
                (
                    jsonb_build_object(
                        'ID_DETALLE',dfp.id,
                        'FORMA_PAGO', fp.nombre,
                        'MONTO', dfp.monto,
                        'COLOR',fp.color
                    )
                ) AS resultado
                FROM detalle_forma_pago dfp
                JOIN forma_pago fp ON dfp.id_forma_pago = fp.id
                WHERE dfp.id_venta = p.id_venta
            ) as js_detalle_forma_pago
            FROM pago p
            JOIN venta as v ON v.id = p.id_venta
            INNER JOIN usuario AS us ON v.usuario_id = us.id
            WHERE us.sucursal_id = :sucursal_id
            AND p.created_at::date = CURRENT_DATE
            order by p.created_at desc;
    ";
    return executeQuery($query, ['sucursal_id' => $sucursal_id]);  // ← AGREGADO EL PARÁMETRO
}


function fnListForPagosSemanales($sucursal_id = null): array
{

    $query = "   
            SELECT 
            p.created_at,
            p.id as pago_id,
            v.id as venta_id,
            v.tipo_comprobante,
            concat(SUBSTRING(v.tipo_comprobante,1,1),'001-',lpad(v.id::text, 6, '0')) as serie_correltavio_referencial,
            concat('P',LPAD(p.id::TEXT,10,'0'),'F',to_char(p.created_at::date, 'YYYYMMDD')) as codigo,
            CASE 
                WHEN EXTRACT(DOW FROM p.created_at) = 0 THEN UPPER('Domingo')
                WHEN EXTRACT(DOW FROM p.created_at) = 1 THEN UPPER('Lunes')
                WHEN EXTRACT(DOW FROM p.created_at) = 2 THEN UPPER('Martes')
                WHEN EXTRACT(DOW FROM p.created_at) = 3 THEN UPPER('Miércoles')
                WHEN EXTRACT(DOW FROM p.created_at) = 4 THEN UPPER('Jueves')
                WHEN EXTRACT(DOW FROM p.created_at) = 5 THEN UPPER('Viernes')
                WHEN EXTRACT(DOW FROM p.created_at) = 6 THEN UPPER('Sábado')
            END AS dia_nombre,
            
            TO_CHAR(p.created_at, 'YYYY-MM-DD') as fecha,
            TO_CHAR(p.created_at, 'HH12:MI:SS AM') as hora,
            p.monto_venta_original,
            p.monto_venta_final,
            (p.monto_venta_original - p.monto_venta_final) AS utilidad,
            (
                SELECT 
                    jsonb_build_object(
                        'venta_id', v.id,
                        'codigo_tiket',concat('T',LPAD(v.id::TEXT,8,'0'),'-','F',to_char(v.fecha_fin_transaccion::date, 'YYYYMMDD')),
                        'fecha_fin_transaccion', v.fecha_fin_transaccion,
                        'dia_nombre', CASE 
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 0 THEN UPPER('Domingo')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 1 THEN UPPER('Lunes')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 2 THEN UPPER('Martes')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 3 THEN UPPER('Miércoles')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 4 THEN UPPER('Jueves')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 5 THEN UPPER('Viernes')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 6 THEN UPPER('Sábado')
                        END,
                        'cliente', CONCAT(ci.nombres, ' ', ci.apellidos),
                        'fecha', TO_CHAR(v.fecha_fin_transaccion, 'YYYY-MM-DD'),
                        'hora', TO_CHAR(v.fecha_fin_transaccion, 'HH12:MI:SS AM'),
                        'telefonomovil_cliente', ci.telefonomovil,
                        'email_cliente', ci.email,
                        'numero_doc_cliente', ci.numero_documento,
                        'usuario', CONCAT(us.id, '-', usua.nombres, ', ', usua.apellidos),
                        'id_persona', ci.id,
                        'usuario_id', v.usuario_id,
                        'monto_venta_final', v.monto_venta_final,
                        'total', v.total,
                        'perdida_utilidad', (v.monto_venta_final - v.total),
                        'estado_pago', CASE 
                            WHEN v.estado_pago = 'P' THEN 'PAGADO'
                            WHEN v.estado_pago = 'C' THEN 'CREDITO'
                        END,
                        'estado_final', v.estado_final,
                        'acumulado_deuda', du.acumulado
                ) AS resultado_json
                FROM venta AS v
                LEFT JOIN deuda AS du ON v.id = du.id_venta 
                INNER JOIN usuario AS us ON v.usuario_id = us.id  
                INNER JOIN persona AS usua ON us.persona_id = usua.id
                INNER JOIN persona AS ci ON v.cliente_id = ci.id
                WHERE v.id = p.id_venta
                --WHERE v.estado_venta = 'VR' 
                AND v.deleted_at IS NULL
            ) as js_venta,
            (
                SELECT jsonb_agg(
                            jsonb_build_object(
                                'rel_venta_articulo_id', rva.id,
                                'venta_id', rva.venta_id,
                                'articulo_id', rva.articulo_id,
                                'descripcion_movimiento', m.descripcion,
                                'descripcion_articulo', CASE 
                                    WHEN ar.dimension_id IS NOT NULL THEN CONCAT(ar.nombre, ' (', dim.medida, ')')
                                    WHEN ar.nombre IS NULL THEN m.descripcion
                                    ELSE ar.nombre 
                                END,
                                'cantidad', rva.cantidad,
                                'precio_unitario_articulo', rva.precio_unitario_articulo,
                                'minutos', rva.minutos,
                                'costo_por_minuto', rva.costo_por_minuto,
                                'sub_total', rva.sub_total
                            )
                        ) AS resultado_json
                FROM rel_venta_articulo AS rva
                JOIN movimiento AS m ON rva.movimiento_id = m.id 
                LEFT JOIN articulo AS ar ON rva.articulo_id = ar.id
                LEFT JOIN dimension AS dim ON ar.dimension_id = dim.id
                WHERE rva.venta_id = p.id_venta
            )as js_detalle_venta,
            (
                SELECT 
                json_agg
                (
                    jsonb_build_object(
                        'ID_DETALLE',dfp.id,
                        'FORMA_PAGO', fp.nombre,
                        'MONTO', dfp.monto,
                        'COLOR',fp.color
                    )
                ) AS resultado
                FROM detalle_forma_pago dfp
                JOIN forma_pago fp ON dfp.id_forma_pago = fp.id
                WHERE dfp.id_venta = p.id_venta
            ) as js_detalle_forma_pago
            FROM pago p
            JOIN venta as v ON v.id = p.id_venta
            INNER JOIN usuario AS us ON v.usuario_id = us.id
            -- WHERE p.id = 2
            WHERE us.sucursal_id = :sucursal_id
            AND p.created_at::date >= date_trunc('week', CURRENT_DATE)
            AND p.created_at::date < CURRENT_DATE + INTERVAL '1 day'
            order by p.created_at desc;
    ";
    return executeQuery($query, ['sucursal_id' => $sucursal_id]);
}

function fnListForAllPagos($sucursal_id = null): array
{
    $query = "   
            SELECT 
            p.created_at,
            p.id as pago_id,
            v.id as venta_id,
            v.tipo_comprobante,
            concat(SUBSTRING(v.tipo_comprobante,1,1),'001-',lpad(v.id::text, 6, '0')) as serie_correltavio_referencial,
            concat('P',LPAD(p.id::TEXT,10,'0'),'F',to_char(p.created_at::date, 'YYYYMMDD')) as codigo,
            CASE 
                WHEN EXTRACT(DOW FROM p.created_at) = 0 THEN UPPER('Domingo')
                WHEN EXTRACT(DOW FROM p.created_at) = 1 THEN UPPER('Lunes')
                WHEN EXTRACT(DOW FROM p.created_at) = 2 THEN UPPER('Martes')
                WHEN EXTRACT(DOW FROM p.created_at) = 3 THEN UPPER('Miércoles')
                WHEN EXTRACT(DOW FROM p.created_at) = 4 THEN UPPER('Jueves')
                WHEN EXTRACT(DOW FROM p.created_at) = 5 THEN UPPER('Viernes')
                WHEN EXTRACT(DOW FROM p.created_at) = 6 THEN UPPER('Sábado')
            END AS dia_nombre,
            
            TO_CHAR(p.created_at, 'YYYY-MM-DD') as fecha,
            TO_CHAR(p.created_at, 'HH12:MI:SS AM') as hora,
            p.monto_venta_original,
            p.monto_venta_final,
            (p.monto_venta_original - p.monto_venta_final) AS utilidad,
            (
                SELECT 
                    jsonb_build_object(
                        'venta_id', v.id,
                        'codigo_tiket',concat('T',LPAD(v.id::TEXT,8,'0'),'-','F',to_char(v.fecha_fin_transaccion::date, 'YYYYMMDD')),
                        'fecha_fin_transaccion', v.fecha_fin_transaccion,
                        'dia_nombre', 
                        CASE 
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 0 THEN UPPER('Domingo')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 1 THEN UPPER('Lunes')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 2 THEN UPPER('Martes')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 3 THEN UPPER('Miércoles')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 4 THEN UPPER('Jueves')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 5 THEN UPPER('Viernes')
                            WHEN EXTRACT(DOW FROM v.fecha_fin_transaccion) = 6 THEN UPPER('Sábado')
                        END,
                        'cliente', CONCAT(ci.nombres, ' ', ci.apellidos),
                        'fecha', TO_CHAR(v.fecha_fin_transaccion, 'YYYY-MM-DD'),
                        'hora', TO_CHAR(v.fecha_fin_transaccion, 'HH12:MI:SS AM'),
                        'telefonomovil_cliente', ci.telefonomovil,
                        'email_cliente', ci.email,
                        'numero_doc_cliente', ci.numero_documento,
                        'usuario', CONCAT(us.id, '-', usua.nombres, ', ', usua.apellidos),
                        'id_persona', ci.id,
                        'usuario_id', v.usuario_id,
                        'monto_venta_final', v.monto_venta_final,
                        'total', v.total,
                        'perdida_utilidad', (v.monto_venta_final - v.total),
                        'estado_pago', CASE 
                            WHEN v.estado_pago = 'P' THEN 'PAGADO'
                            WHEN v.estado_pago = 'C' THEN 'CREDITO'
                        END,
                        'estado_final', v.estado_final,
                        'acumulado_deuda', du.acumulado
                ) AS resultado_json
                FROM venta AS v
                LEFT JOIN deuda AS du ON v.id = du.id_venta 
                INNER JOIN usuario AS us ON v.usuario_id = us.id  
                INNER JOIN persona AS usua ON us.persona_id = usua.id
                INNER JOIN persona AS ci ON v.cliente_id = ci.id
                WHERE v.id = p.id_venta
                AND v.deleted_at IS NULL
            ) as js_venta,
            (
                SELECT jsonb_agg(
                            jsonb_build_object(
                                'rel_venta_articulo_id', rva.id,
                                'venta_id', rva.venta_id,
                                'articulo_id', rva.articulo_id,
                                'descripcion_movimiento', m.descripcion,
                                'descripcion_articulo', CASE 
                                    WHEN ar.dimension_id IS NOT NULL THEN CONCAT(ar.nombre, ' (', dim.medida, ')')
                                    WHEN ar.nombre IS NULL THEN m.descripcion
                                    ELSE ar.nombre 
                                END,
                                'cantidad', rva.cantidad,
                                'precio_unitario_articulo', rva.precio_unitario_articulo,
                                'minutos', rva.minutos,
                                'costo_por_minuto', rva.costo_por_minuto,
                                'sub_total', rva.sub_total
                            )
                        ) AS resultado_json
                FROM rel_venta_articulo AS rva
                JOIN movimiento AS m ON rva.movimiento_id = m.id 
                LEFT JOIN articulo AS ar ON rva.articulo_id = ar.id
                LEFT JOIN dimension AS dim ON ar.dimension_id = dim.id
                WHERE rva.venta_id = p.id_venta
            )as js_detalle_venta,
            (
                SELECT 
                json_agg
                (
                    jsonb_build_object(
                        'ID_DETALLE',dfp.id,
                        'FORMA_PAGO', fp.nombre,
                        'MONTO', dfp.monto,
                        'COLOR',fp.color
                    )
                ) AS resultado
                FROM detalle_forma_pago dfp
                JOIN forma_pago fp ON dfp.id_forma_pago = fp.id
                WHERE dfp.id_venta = p.id_venta
            ) as js_detalle_forma_pago
            FROM pago p
            JOIN venta as v ON v.id = p.id_venta
            INNER JOIN usuario AS us ON v.usuario_id = us.id
            WHERE us.sucursal_id = :sucursal_id
            order by p.created_at desc;
    ";
    return executeQuery($query, ['sucursal_id' => $sucursal_id]);  // ← AGREGADO EL PARÁMETRO
}

function fnListadoProveedores($cadena): array
{
    // La consulta SQL para buscar proveedores
    $query = "   
    SELECT id, numero_documento, tipo_persona, condicion, nombre_comercial, razon_social 
    FROM persona 
    WHERE condicion = 'PROVEEDOR' 
    AND (
        upper(nombre_comercial) LIKE upper(:busqueda) OR 
        upper(razon_social) LIKE upper(:busqueda) OR
        numero_documento LIKE :busqueda
    )
     
    AND deleted_at IS NULL LIMIT 10;
    ";

    // Ejecuta la consulta con el parámetro de búsqueda
    return executeQuery($query, ['busqueda' => '%' . $cadena . '%']);
}

function fnListadoProductos($cadena): array
{
    // La consulta SQL para buscar proveedores
    $query = "   
    SELECT 
    CASE escala
        WHEN '-' THEN
            CONCAT(articulo,' | Tipo: ',tipo,' | Dimension: ',dimension,' |  STOCK: ',stock::INTEGER,' | ', 'Precio de Venta: S/ ',precio_venta)
        ELSE
            CONCAT(articulo,' | Tipo:',tipo,' | Dimension: ',dimension,' | ',escala,' - STOCK: ',stock::INTEGER,' | ', 'Precio de Venta: S/ ',precio_venta)
    END articulo_formato ,
    *
    FROM view_articulos 
    WHERE articulo LIKE UPPER(:busqueda) LIMIT 10;
    ";

    // Ejecuta la consulta con el parámetro de búsqueda
    return executeQuery($query, ['busqueda' => '%' . $cadena . '%']);
}
function fnListadoCompras(): array
{
    // La consulta SQL para buscar proveedores
    $query = "   
    SELECT 
    c.id as compra_id, 
    --c.usuario_id, --AS realizada_por,
    CONCAT (us.nombres,' ',us.apellidos) AS realizada_por,
    CASE 
        WHEN c.proveedor_id IS NOT null THEN
            CONCAT(proveedor.numero_documento,' - ', UPPER(proveedor.nombre_comercial))
        ELSE	
            'SIN REGISTRO DE PROVEEDOR'
    END proveedor,
    proveedor.numero_documento as proveedor_num_doc,
    UPPER(proveedor.nombre_comercial) as nombre_comercial_proveedor,
    --c.proveedor_id,
    CASE 
        WHEN c.fecha IS NULL THEN
            'SIN REGISTRO'
        ELSE
            TO_CHAR(c.fecha, 'YYYY-MM-DD')
    END fecha_compra,
    --c.fecha as fecha_compra,
    c.numero_comprobante,
    CASE 
        WHEN c.total IS NULL THEN
            'SIN REGISTRO'
        ELSE
            CONCAT('S/',' ',c.total)
            ----TO_CHAR(c.total, '999999999.00')
    END total,
    --c.total,
    --js_detalle_compra
    c.created_at::DATE as fecha_registro,
    TO_CHAR(c.created_at, 'HH12:MI:SS AM') as hora,
    js_detalle_compra,
    --c.created_at::TIME as hora,
    c.created_at as fecha_hora_registro
    FROM compra c
    JOIN usuario u ON u.id = c.usuario_id AND c.created_at::DATE >= CURRENT_DATE - INTERVAL '3 months'
    JOIN persona us ON u.persona_id = us.id
    LEFT JOIN persona proveedor ON c.proveedor_id = proveedor.id;
    ";

    // Ejecuta la consulta con el parámetro de búsqueda
    return executeQuery($query);
}

//
function fnListadoCajaChica(): array
{
    // La consulta SQL para buscar proveedores
    $query = "   
    WITH with_detalle_caja AS
    (
        SELECT 
        dc.id as detalle_caja_id,
        dc.caja_id,
        dc.responsable,
        c.titulo as concepto,
        dc.monto,
        dc.created_at,
        dc.created_at::DATE fecha_registro,
        TO_CHAR(dc.created_at, 'HH12:MI:SS AM') as hora_registro,
        dc.tipo_movimiento,
        dc.nota
        FROM detalle_caja_chica dc
        JOIN concepto c ON c.id = dc.concepto_id
        order by dc.id
    )
    SELECT *, 
    CASE
		WHEN saldo IS NULL THEN
			0
		ELSE
			(monto-saldo)
	END as egresos_de_caja,
    COALESCE(saldo,monto) as saldo_v2,
    COALESCE(((monto-saldo)/monto)*100,0)::INTEGER as porcentaje,
    apertura::date as fecha_apertura,
    TO_CHAR(apertura, 'HH12:MI:SS AM') as hora_apertura,
    (
        SELECT 
            json_agg(
                json_build_object(
                    'detalle_caja_id',d.detalle_caja_id,
                    'caja_id', d.detalle_caja_id,
                    'responsable', d.responsable,
                    'concepto', d.concepto,
                    'monto', d.monto,
                    'created_at',d.created_at,
                    'fecha_registro', d.fecha_registro,
                    'hora_registro', d.hora_registro,
                    'tipo_movimiento',d.tipo_movimiento,
                    'nota', d.nota
                )
            )
        FROM with_detalle_caja d WHERE d.caja_id = c.id
    ) as js_detalle_caja
    FROM caja c where cierre IS NULL ORDER BY 1 DESC LIMIT 1;  
    ";

    // Ejecuta la consulta con el parámetro de búsqueda
    return executeQuery($query);
}

function fnListadoCajaChicaCerradas(): array
{
    // La consulta SQL para buscar proveedores
    $query = "   
    WITH with_detalle_caja AS
    (
        SELECT 
        dc.id as detalle_caja_id,
        dc.caja_id,
        dc.responsable,
        c.titulo as concepto,
        dc.monto,
        dc.created_at,
        dc.created_at::DATE fecha_registro,
        TO_CHAR(dc.created_at, 'HH12:MI:SS AM') as hora_registro,
        dc.tipo_movimiento,
        dc.nota
        FROM detalle_caja_chica dc
        JOIN concepto c ON c.id = dc.concepto_id
        order by dc.id
    )
    SELECT *, 
    CASE 
        WHEN EXTRACT(DOW FROM c.apertura) = 0 THEN UPPER('Domingo')
        WHEN EXTRACT(DOW FROM c.apertura) = 1 THEN UPPER('Lunes')
        WHEN EXTRACT(DOW FROM c.apertura) = 2 THEN UPPER('Martes')
        WHEN EXTRACT(DOW FROM c.apertura) = 3 THEN UPPER('Miércoles')
        WHEN EXTRACT(DOW FROM c.apertura) = 4 THEN UPPER('Jueves')
        WHEN EXTRACT(DOW FROM c.apertura) = 5 THEN UPPER('Viernes')
        WHEN EXTRACT(DOW FROM c.apertura) = 6 THEN UPPER('Sábado')
    END dia_semana,
    CASE
        WHEN saldo IS NULL THEN
            0
        ELSE
            (monto-saldo)
    END as egresos_de_caja,
    COALESCE(saldo,monto) saldo_v2,
    COALESCE(((monto-saldo)/monto)*100,0)::INTEGER as porcentaje,
    apertura::date as fecha_apertura,
    cierre::date as fecha_cierre,

    TO_CHAR(apertura, 'HH12:MI:SS AM') as hora_apertura,
    TO_CHAR(cierre, 'HH12:MI:SS AM') as hora_cierre,
    (
        SELECT 
            json_agg(
                json_build_object(
                    'detalle_caja_id',d.detalle_caja_id,
                    'caja_id', d.caja_id,
                    'responsable', d.responsable,
                    'concepto', d.concepto,
                    'monto', d.monto,
                    'created_at',d.created_at,
                    'fecha_registro', d.fecha_registro,
                    'hora_registro', d.hora_registro,
                    'tipo_movimiento',d.tipo_movimiento,
                    'nota', d.nota
                )
            )
        FROM with_detalle_caja d WHERE d.caja_id = c.id
    ) as js_detalle_caja
    FROM caja c 
    where cierre IS NOT NULL AND deleted_at IS null
    ORDER BY 1 DESC;  

    ";

    // Ejecuta la consulta con el parámetro de búsqueda
    return executeQuery($query);
}

function fnListadoConceptosEgresos($tipoCaja, $sucursal_id = null): array
{

    $query = "     
    SELECT 
    * 
    FROM 
    concepto 
    WHERE id NOT IN (1) AND tipo_caja IN (:tipo_caja,'A')
    AND deleted_at IS null
    " . ($sucursal_id !== null ? "AND sucursal_id = :sucursal_id" : "") . "
    ORDER BY orden
    ";

    $params = ["tipo_caja" => $tipoCaja];
    if ($sucursal_id !== null) {
        $params['sucursal_id'] = $sucursal_id;
    }
    return executeQuery($query, $params);
}
function fnListadoMovimientoCajaGrande($sucursal_id = null): array
{

    $query = "     
    SELECT 
    dc.*,
    case 
        when dc.responsable is null then
            dc.movimiento_caja_v2
        else
            dc.responsable
    end accionado,
    fp.nombre forma_pago,
    CASE 
        WHEN EXTRACT(DOW FROM dc.created_at) = 0 THEN UPPER('Domingo')
        WHEN EXTRACT(DOW FROM dc.created_at) = 1 THEN UPPER('Lunes')
        WHEN EXTRACT(DOW FROM dc.created_at) = 2 THEN UPPER('Martes')
        WHEN EXTRACT(DOW FROM dc.created_at) = 3 THEN UPPER('Miércoles')
        WHEN EXTRACT(DOW FROM dc.created_at) = 4 THEN UPPER('Jueves')
        WHEN EXTRACT(DOW FROM dc.created_at) = 5 THEN UPPER('Viernes')
        WHEN EXTRACT(DOW FROM dc.created_at) = 6 THEN UPPER('Sábado')
    END dia_semana,
    dc.created_at::date as fecha,
    TO_CHAR(dc.created_at, 'HH12:MI:SS AM') as hora
    FROM 
    detalle_caja_grande dc 
    JOIN forma_pago fp ON fp.id=dc.forma_pago_id
    LEFT JOIN usuario us ON us.id = dc.responsable_id
    where dc.deleted_at is null AND (us.sucursal_id = :sucursal_id OR :sucursal_id IS NULL) -- and dc.tipo_movimiento = 'EGRESO'
    ORDER by 1;
    ";

    return executeQuery($query, ['sucursal_id' => $sucursal_id]);
}


function fnVerificarUsarioSession($id): int
{
    $query = "     
    SELECT 
    COUNT(*) as cantidad
    FROM usuario
    WHERE id = :idUsuario AND deleted_at IS NULL
    ";

    // Ejecutar la consulta
    $result = executeQuery($query, ['idUsuario' => $id]);

    // Verificar si el usuario existe
    if ($result[0]['cantidad'] > 0) {
        // Si el usuario existe y no está eliminado, devolver 1
        return 1;
    } else {
        // Si el usuario no existe o está eliminado, devolver 0
        return 0;
    }
}
function fnEjecutarETL(): array
{
    $query = "SELECT * FROM fn_etl_vysam();";
    $result = executeQuery($query);
    if ($result) {

        return ['respuesta' => $result[0]['fn_etl_vysam']];
    } else {

        return ['respuesta' => 'ERROR'];
    }
}


function fnEjecutarETLArticulosNube(): array
{
    $query = "SELECT * FROM fn_etl_articulo();";
    $result = executeQuery($query);
    if ($result) {

        return ['respuesta' => $result[0]['fn_etl_articulo']];
    } else {

        return ['respuesta' => 'ERROR'];
    }
}



function fnListadoDeReservasWeb(): array
{
    $query = "
    select * from view_foreingdatabase_reservas_web where estado = 'pendiente'
    ";

    return executeQuery($query);
}


function fnListadoDeEmisor(): array
{
    $query = "SELECT * FROM emisor";


    $result = executeQuery($query);

    //var_dump($result);


    return $result;
}


function fnGenerarTicket($idVenta): void
{
    $datosprueba = fnUltimaVentaPorIdVenta($idVenta)[0];

    // Datos de la venta obtenidos de la consulta
    $datoEmisor = fnListadoDeEmisor()[0];
    $datosVenta = [
        "codigo_tiket" => $datosprueba["codigo_tiket"],
        "tipo_comprobante" => $datosprueba["tipo_comprobante"],
        "fecha" => $datosprueba["fecha"],
        "hora" => $datosprueba["hora"],
        "cliente" => $datosprueba["cliente"],
        "numero_doc_cliente" => $datosprueba["numero_doc_cliente"],
        "usuario_inicial" => $datosprueba["usuario"],
        "usuario_final" => $datosprueba["atencion_final_usuario"],
        "total" => $datosprueba["total"],
        "monto_venta_final" => $datosprueba["monto_venta_final"],
        "estado_pago" => $datosprueba["estado_pago"],
        "estado_final" => $datosprueba["estado_final"],
        "descuento" => $datosprueba["perdida_utilidad"],
        "js_detalle" => $datosprueba["js_detalle"],
        "js_detalle_forma_pago" => $datosprueba["js_detalle_forma_pago"]
    ];

    // Decodificar productos vendidos y formas de pago
    $productos = json_decode($datosVenta["js_detalle"], true);
    $pagos = json_decode($datosVenta["js_detalle_forma_pago"], true);

    // Limpiar salida previa
    ob_clean();

    // Crear PDF en formato ticket térmico (80mm de ancho)
    $pdf = new FPDF('P', 'mm', array(80, 200));
    $pdf->AddPage();
    
    $logoPath = 'logica/logo.jpeg';

    // Verificar si la imagen existe
    if (file_exists($logoPath)) {
        $logoWidth = 20; // Ancho del logo
        $centerX = (80 - $logoWidth) / 2; // Centrado en una página de 80mm de ancho
        
        $pdf->Image($logoPath, $centerX, 5, $logoWidth); // Ajustar la posición y tamaño del logo
        $pdf->Ln(20);
    } else {
        $pdf->Cell(60, 4, 'Logo no disponible', 0, 1, 'C');
    }


    //$pdf->Image('logo.jpg', 10, 5, 20); // Ruta a tu logo, ajuste de posición (x=10, y=5) y tamaño (ancho=20)
    //$pdf->Ln(20); // Deja un espacio de 20mm después del logo
    // TÍTULO: TICKET DE VENTA

    $pdf->SetFont('Arial', 'B', size: 7);
    $pdf->Cell(60, 4, $datoEmisor["razon_social"], 0, 1, 'C');
    $pdf->Cell(60, 4, "RUC: " . $datoEmisor["ruc"], 0, 1, 'C');

    $pdf->SetFont('Arial', size: 7);

    $pdf->SetFont('Arial', '', 6);
    $pdf->MultiCell(60, 4, $datoEmisor["direccion"], 0, 'C');
    $pdf->SetFont('Arial', 'B', 8); // Restaurar estilo después

    //$pdf->Cell(60, 4, $datoEmisor["direccion"], 0, 1, 'C');
    //$pdf->SetFont('Arial', 'B', 8);



    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(60, 4, $datosprueba["tipo_comprobante"] . ' DE VENTA ELECTRONICA', 0, 1, 'C');
    $pdf->SetFont('Arial', 'B', 8);


    $pdf->Cell(60, 4, $datosVenta["codigo_tiket"], 0, 1, 'C');

    $pdf->Ln(1);

    // DATOS DEL CLIENTE
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(60, 4, 'Cliente: ' . $datosVenta["cliente"], 0, 1, 'L');
    $pdf->Cell(60, 4, 'DNI/RUC: ' . $datosVenta["numero_doc_cliente"], 0, 1, 'L');
    $pdf->Cell(60, 4, 'Fecha: ' . $datosVenta["fecha"] . ' ' . $datosVenta["hora"], 0, 1, 'L');
    $pdf->Ln(1);

    // SEPARADOR
    $pdf->Cell(60, 3, str_repeat('_', 25), 0, 1, 'C');
    $pdf->Ln(1);

    // ENCABEZADO DE PRODUCTOS
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(30, 3, 'DESCRIPCION', 0, 0, 'L');
    $pdf->Cell(8, 3, 'CANT.', 0, 0, 'C');
    $pdf->Cell(12, 3, 'P.U', 0, 0, 'C');
    $pdf->Cell(10, 3, 'TOTAL', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Ln(1);

    // LISTADO DE PRODUCTOS
    foreach ($productos as $producto) {
        $yInicial = $pdf->GetY();

        // Nombre del producto en varias líneas
        $pdf->MultiCell(30, 3, $producto["descripcion_2"], 0, 'L');

        $yFinal = $pdf->GetY();
        $alturaFila = $yFinal - $yInicial;

        // Alinear las demás columnas en la misma altura
        $pdf->SetY($yInicial);
        $pdf->SetX(40);

        $pdf->Cell(8, $alturaFila, $producto["cantidad"], 0, 0, 'C');
        $pdf->Cell(12, $alturaFila, 'S/ ' . number_format($producto["precio_unitario_articulo"], 2), 0, 0, 'C');
        $pdf->Cell(10, $alturaFila, 'S/ ' . number_format($producto["sub_total"], 2), 0, 1, 'C');
    }

    // SEPARADOR
    $pdf->Ln(1);
    $pdf->Cell(60, 3, str_repeat('_', 25), 0, 1, 'C');
    $pdf->Ln(1);

    // ESTADO DE PAGO Y ESTADO FINAL
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(20, 3, 'Estado:', 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(15, 3, $datosVenta["estado_pago"], 0, 1, 'L');
    $pdf->Ln(1);

    // ESTADO DE PAGO Y ESTADO FINAL
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(20, 3, 'Descuento:', 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(15, 3, "S/ " . $datosVenta["descuento"], 0, 1, 'L');
    $pdf->Ln(1);



    // ENCABEZADO DE PAGOS
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(30, 3, 'Forma de Pago', 0, 0, 'L');
    $pdf->Cell(20, 3, 'Monto', 0, 1, 'R');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Ln(1);

    // LISTADO DE PAGOS
    foreach ($pagos as $x) {
        $pdf->Cell(30, 3, $x["forma_pago"], 0, 0, 'L');
        $pdf->Cell(20, 3, 'S/ ' . number_format($x["monto"], 2), 0, 1, 'R');
    }

    // SEPARADOR
    $pdf->Ln(1);
    $pdf->Cell(60, 3, str_repeat('_', 25), 0, 1, 'C');
    $pdf->Ln(1);

    // TOTAL DE VENTA
    $pdf->SetFont('Arial', 'B', 5);
    $pdf->Cell(60, 4, 'TOTAL DE VENTA: S/ ' . number_format($datosVenta["total"], 2), 0, 1, 'C');
    $pdf->Ln(1);
    // TOTAL DE VENTA REAL
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(60, 4, 'TOTAL DE VENTA REAL: S/ ' . number_format($datosVenta["monto_venta_final"], 2), 0, 1, 'C');
    $pdf->Ln(1);



    // TOTAL EN LETRAS
    $total_letras = strtoupper(number_format($datosVenta["total"], 2) . " /100 PEN");
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(60, 3, $total_letras, 0, 1, 'C');
    $pdf->Ln(1);

    // VENDEDOR
    $pdf->SetFont('Arial', 'B', 6);
    $pdf->Cell(60, 3, 'ATENDIDO POR: ' . $datosVenta["usuario_final"], 0, 1, 'C');
    $pdf->Ln(1);

    // MENSAJE DE AGRADECIMIENTO
    $pdf->SetFont('Arial', '', 7);
    $pdf->MultiCell(60, 3, 'Representacion Impresa de la ' . $datosprueba["tipo_comprobante"] . ' DE VENTA ELECTRONICA', 0, 'C');
    $pdf->MultiCell(60, 3, 'Gracias por su preferencia', 0, 'C');

    // FIRMA DE LA EMPRESA
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(60, 3, ' ', 0, 1, 'C');
    $pdf->Cell(60, 3, 'LIBRERIA BAZAR RODRI', 0, 1, 'C');
    $pdf->Cell(60, 3, 'DESARROLLADO POR CARACOL SOFT', 0, 1, 'C');
    $pdf->Ln(4);





    // GENERAR PDF
    ob_clean();
    $pdf->Output('I', 'ticket_venta.pdf');
}

function fnRankingClientes() {
    $query = "
        SELECT
            p.nombres || ' ' || p.apellidos AS nombre_cliente,
            SUM(v.monto_venta_final) AS total_compras_acumulado
        FROM
            public.venta v
        INNER JOIN
            public.persona p ON v.cliente_id = p.id
        WHERE
            v.estado_venta = 'VR'
            AND v.deleted_at IS NULL
        GROUP BY
            p.id, p.nombres, p.apellidos
        ORDER BY
            total_compras_acumulado DESC
    ";
    
    return executeQuery($query);
}