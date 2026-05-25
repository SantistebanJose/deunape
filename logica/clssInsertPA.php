<?php
ob_start();
include("bd.php");

if (isset($_POST["accion"])) {
    $accion = $_POST["accion"];
    controladorClssInsertPA($accion);
}

function controladorClssInsertPA($accion)
{
    switch ($accion) {
        case 'FINALIZARVENTA':
            if (isset($_POST["jsDatosVenta"])) {
                $jsDatosVenta = $_POST["jsDatosVenta"];
                finalizarVentaReserva($jsDatosVenta);
            }
            break;
        case 'FINALIZARVENTARAPIDO':
            if (isset($_POST["jsDatosVenta"])) {
                $jsDatosVenta = $_POST["jsDatosVenta"];
                $js_articulos = $_POST["js_articulos"];
                $js_detalle_pago = $_POST["js_detalle_pago"];
                finalizarVentaReservaRapido($jsDatosVenta, $js_articulos, $js_detalle_pago);
            }
            break;
        case 'FINALIZARVENTACREDITO':
            if (isset($_POST["jsDatosVenta"])) {
                $jsDatosVenta = $_POST["jsDatosVenta"];
                finalizarVentaReservaCredito($jsDatosVenta);
            }
            break;
        case 'LISTAR_PRESENTACIONES':
            if (isset($_POST["sucursal_id"])) {
                $sucursal_id = $_POST["sucursal_id"];
                listarPresentacionesDisponibles($sucursal_id);
            }
            break;

        case 'OBTENER_PRECIOS_ARTICULO':
            if (isset($_POST["articulo_id"]) && isset($_POST["sucursal_id"])) {
                $articulo_id = $_POST["articulo_id"];
                $sucursal_id = $_POST["sucursal_id"];
                obtenerPreciosArticulo($articulo_id, $sucursal_id);
            }
            break;
        case 'FINALIZARVENTACREDITORAPIDO':
            if (isset($_POST["jsDatosVenta"])) {
                $jsDatosVenta = $_POST["jsDatosVenta"];
                $js_articulos = $_POST["js_articulos"];
                $js_detalle_deuda = $_POST["js_detalle_deuda"];
                finalizarVentaReservaCreditoRapido($jsDatosVenta, $js_articulos, $js_detalle_deuda);
            }
            break;
        case 'ABONARDEUDACLIENTE':
            if (isset($_POST["jsDatosAbono"])) {
                $jsDatos = $_POST["jsDatosAbono"];
                abonarDeuda($jsDatos);
            }
            break;

        case 'REGISTAR_ARTICULO':
            if (isset($_POST["jsDatosArticulo"])) {
                $jsDatos = $_POST["jsDatosArticulo"];
                paRegistrarArticulo($jsDatos);
            }
            break;
        case 'REGISTAR_ARTICULO_COMPLETO':
            if (isset($_POST["jsDatosArticulo"])) {
                $jsDatos = $_POST["jsDatosArticulo"];
                paRegistrarArticuloCompleto($jsDatos);
            }
            break;

        case 'EDITAR_ARTICULO_COMPLETO':
            if (isset($_POST["jsDatosArticulo"])) {
                $jsDatos = $_POST["jsDatosArticulo"];
                paEditarArticuloCompleto($jsDatos);
            }
            break;
        case 'BLOQUEAR_ARTICULO':
            $id = $_POST["id"]; // Decodificar JSON
            toggle_estado_articulo_completo($id, $accion);
            break;
        case 'DESBLOQUEAR_ARTICULO':
            $id = $_POST["id"]; // Decodificar JSON
            toggle_estado_articulo_completo($id, $accion);
            break;
        case 'ELIMINAR_ARTICULO':
            $id = $_POST["id"]; // Decodificar JSON
            fnElimarArticulo($id);
            break;
        //
        case 'REGISTRAR_COMPRA':
            if (isset($_POST["jsDatosCompra"])) {
                $jsDatos = $_POST["jsDatosCompra"];
                paRegistrarCompra($jsDatos);
            }
            break;
        case 'APERTURACAJA':
            //fnAperturaDeCajaChica

            if (isset($_POST["jsDatoCaja"])) {
                $jsDatos = $_POST["jsDatoCaja"];
                fnAperturaDeCajaChica($jsDatos);
            }
            break;
        case 'INSERTDETALLECAJACHICA':
            //fnInsertDetalleCajaChica

            if (isset($_POST["jsDetalleCaja"])) {
                $jsDatos = $_POST["jsDetalleCaja"];
                fnInsertDetalleCajaChica($jsDatos);
            }
            break;
        case 'CIERREDECAJACHICA':
            //fnCierreCajaChica

            if (isset($_POST["caja_id"])) {
                $caja_id = $_POST["caja_id"];
                fnCierreCajaChica($caja_id);
            }
            break;
        case 'INSERTTRABAJADORUSUARIO':
            //fnCierreCajaChica

            if (isset($_POST["jsDatosTrabajador"])) {
                $jsDatosTrabajador = $_POST["jsDatosTrabajador"];
                fnInsertPersonaUsurio($jsDatosTrabajador);
            }
            break;

        case 'INSERTDETALLECAJAGRANDE':
            //fnInsertDetalleCajaGrande
            if (isset($_POST["jsDetalleCajaGrande"])) {
                $jsDetalleCajaGrande = $_POST["jsDetalleCajaGrande"];
                fnInsertDetalleCajaGrande($jsDetalleCajaGrande);
            }
            break;


        case 'INSERTMEDIODEPAGO':
            //fnInsertMedioDePago
            if (isset($_POST["js_datos_medio_pago"])) {
                $js_datos_medio_pago = $_POST["js_datos_medio_pago"];
                fnInsertMedioDePago($js_datos_medio_pago);
            }
            break;

        case 'ALTASANDBAJASMEDIOPAGO':
            //fnDarDeAltaOrBajaMedioDePago
            if (isset($_POST["js_datos_altas_baja"])) {
                $js_datos_altas_baja = $_POST["js_datos_altas_baja"];
                fnDarDeAltaOrBajaMedioDePago($js_datos_altas_baja);
            }
            break;
        case 'VACIARCAJA':
            //fnUpdateEmisor
            if (isset($_POST["jsDatos"])) {
                $jsDatos = $_POST["jsDatos"];
                fnVaciarCaja($jsDatos);
            }
            break;

        case 'INSERTPROVEEDORALMOMENTODECOMPRA':
            //fnInsertProveedorAlMomentCompra
            if (isset($_POST["jsDatosProveedor"])) {
                $jsDatosProveedor = $_POST["jsDatosProveedor"];
                fnInsertProveedorAlMomentCompra($jsDatosProveedor);
            }

            break;

        case 'INSERT_SERVICIOS':
            //fnInsertServicio
            if (isset($_POST["jsDatos"])) {
                $jsDatos = $_POST["jsDatos"];
                fnInsertServicio($jsDatos);
            }

            break;
        //
        case 'EDITAR_SERVICIO':
            //fnEditarServicio
            if (isset($_POST["jsDatos"])) {
                $jsDatos = $_POST["jsDatos"];
                fnEditarServicio($jsDatos);
            }
            break;
        case 'EDITAR_EMISOR':
            fnUpdateEmisor();
            break;

        //case 'EDITAR_EMISOR':
        //fnUpdateEmisor
        // if (isset($_POST["jsDatos"])) {
        //     $jsDatos = $_POST["jsDatos"];
        //      fnUpdateEmisor($jsDatos);
        //  }
        //  break;

        //VACIARCAJA


        case 'CAMBIARCONTRASEÑA':
            // Otros casos si los necesitas

            break;
    }
}

function finalizarVentaReserva($jsDatosVenta)
{
    global $conectar;


    $data = json_decode($jsDatosVenta, true);

    $tipo_comprobante = $data['tipo_comprobante'];
    $venta_id = $data['venta_id'];
    $atencion_final_usuario = $data['atencion_final_usuario'];
    $numUpdateTelefonoPersona = $data['numUpdateTelefonoPersona'];
    $monto_original = $data['monto_original'];
    $monto_venta_final = $data['monto_venta_final'];
    $js_detalles_receptor_factura = json_encode($data['js_detalles_receptor_factura']);
    $json_pagos = json_encode($data['js_detalle_pagos']);

    try {

        $sql = "SELECT fn_finalizar_venta_directa(:venta_id, :atencion_final_usuario, :numUpdateTelefonoPersona, :monto_original, :monto_venta_final,:tipo_comprobante ,:js_detalles_receptor_factura,:js_pagos)";
        $stmt = $conectar->prepare($sql);


        $stmt->bindParam(':venta_id', $venta_id);
        $stmt->bindParam(':atencion_final_usuario', $atencion_final_usuario);
        $stmt->bindParam(':numUpdateTelefonoPersona', $numUpdateTelefonoPersona);
        $stmt->bindParam(':monto_original', $monto_original);
        $stmt->bindParam(':monto_venta_final', $monto_venta_final);
        $stmt->bindParam(':js_pagos', $json_pagos);
        $stmt->bindParam(':js_detalles_receptor_factura', $js_detalles_receptor_factura);
        $stmt->bindParam(':tipo_comprobante', $tipo_comprobante);


        $stmt->execute();


        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $jsonResponse = $row['fn_finalizar_venta_directa'];


        $response = json_decode($jsonResponse, true);


        echo json_encode($response);
    } catch (Exception $e) {

        echo json_encode([
            'estado' => false,
            'mensaje' => 'Error al procesar la venta. Consultar con el Administrador de Sistemas',
            'error' => $e->getMessage()
        ]);
    }
}

function finalizarVentaReservaRapido($jsDatosVenta, $js_articulos, $js_detalle_pago)
{
    global $conectar;

    try {

        $sql = "SELECT fn_finalizar_venta_directa_rapida(:json_venta,:json_detalles,:json_pagos)";
        $stmt = $conectar->prepare($sql);


        $stmt->bindParam(':json_venta', $jsDatosVenta);
        $stmt->bindParam(':json_detalles', $js_articulos);
        $stmt->bindParam(':json_pagos', $js_detalle_pago);


        $stmt->execute();


        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $jsonResponse = $row['fn_finalizar_venta_directa_rapida'];


        $response = json_decode($jsonResponse, true);


        echo json_encode($response);
    } catch (Exception $e) {

        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar la venta. Consultar con el Administrador de Sistemas', 'detalles' => $js_articulos]);
    }
}

function finalizarVentaReservaCreditoRapido($jsDatosVenta, $js_articulos, $js_detalle_deuda)
{
    global $conectar;


    try {
        $sql = "SELECT fn_finalizar_venta_credito_rapida(:json_venta,:json_detalles, :json_deudas)";
        $stmt = $conectar->prepare($sql);

        if ($js_detalle_deuda === null || $js_detalle_deuda === "null") {
            $js_detalle_deuda = "[]"; // Reemplaza null por un array vacío en JSON
        }

        $stmt->bindParam(':json_venta', $jsDatosVenta);
        $stmt->bindParam(':json_detalles', $js_articulos);
        $stmt->bindParam(':json_deudas', $js_detalle_deuda);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $jsonResponse = $row['fn_finalizar_venta_credito_rapida'];

        $response = json_decode($jsonResponse, true);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar la venta. Consultar con el Administrador de Sistemas.' . $e]);
    }
}

function finalizarVentaReservaCredito($jsDatosVenta)
{
    global $conectar;

    $data = json_decode($jsDatosVenta, true);

    $venta_id = $data['venta_id'];
    $atencion_final_usuario = $data['atencion_final_usuario'];
    $numUpdateTelefonoPersona = $data['numUpdateTelefonoPersona'];
    $monto_original = $data['monto_original'];
    $monto_venta_final = $data['monto_venta_final'];
    $monto_inicial_deuda = $data['monto_inicial'];
    $json_deuda = $data['js_detalle_deuda'];


    if (is_null($json_deuda) || empty($json_deuda)) {

        $json_deuda = null;
    } else {

        $json_deuda = json_encode($json_deuda);
    }

    try {
        $sql = "SELECT fn_finalizar_venta_credito(:venta_id, :atencion_final_usuario, :numUpdateTelefonoPersona, :monto_original, :monto_venta_final,:monto_ini, :js_deudas)";
        $stmt = $conectar->prepare($sql);

        $stmt->bindParam(':venta_id', $venta_id);
        $stmt->bindParam(':atencion_final_usuario', $atencion_final_usuario);
        $stmt->bindParam(':numUpdateTelefonoPersona', $numUpdateTelefonoPersona);
        $stmt->bindParam(':monto_original', $monto_original);
        $stmt->bindParam(':monto_venta_final', $monto_venta_final);
        $stmt->bindParam(':monto_ini', $monto_inicial_deuda);
        $stmt->bindParam(':js_deudas', $json_deuda);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $jsonResponse = $row['fn_finalizar_venta_credito'];

        $response = json_decode($jsonResponse, true);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar la venta. Consultar con el Administrador de Sistemas.' . $e]);
    }
}

function paRegistrarArticulo($jsDatosArticulo)
{
    global $conectar;

    $data = json_decode($jsDatosArticulo, true);

    $categoria_id = $data['categoria_id'];
    $color = $data['color'];
    $corte = false;
    $dimension_id = $data['dimension_id'];
    $escala_id = $data['escala_id'];
    $marca = $data['marca'];
    $nombre = $data['nombre'];
    $tipo_id = $data['tipo_id'];

    try {
        $sql = "SELECT fn_registrar_articulo(:nombre, :categoria_id, :tipo_id, :dimension_id, :escala_id, :corte, :color, :marca)";

        $stmt = $conectar->prepare($sql);

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':categoria_id', $categoria_id);
        $stmt->bindParam(':tipo_id', $tipo_id);
        $stmt->bindParam(':dimension_id', $dimension_id);
        $stmt->bindParam(':escala_id', $escala_id);
        $stmt->bindParam(':corte', $corte, PDO::PARAM_BOOL); // Usando PDO::PARAM_BOOL para asegurarse que se trata como booleano
        $stmt->bindParam(':color', $color);
        $stmt->bindParam(':marca', $marca);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $jsonResponse = $row['fn_registrar_articulo'];

        $response = json_decode($jsonResponse, true);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar el Registro. Consultar con el Administrador de Sistemas.' . $e, 'jsonEntrada' => $data, 'SQL' => $stmt]);
    }
}


function paRegistrarArticuloCompleto($jsDatosArticulo)
{
    global $conectar;

    $data = json_decode($jsDatosArticulo, true);

    // Validación de sucursal_id
    if (!isset($data['sucursal_id']) || empty($data['sucursal_id'])) {
        echo json_encode([
            'estado' => false,
            'mensaje' => 'Error: sucursal_id no proporcionado o inválido'
        ]);
        return;
    }

    // ✅ ASIGNACIÓN CORRECTA DE VARIABLES
    $nombre = $data['nombre'];
    $categoria_id = $data['categoria_id'] ?? null;
    $tipo_id = $data['tipo_id'] ?? null;
    $dimension_id = $data['dimension_id'] ?? null;
    $escala_id = $data['escala_id'] ?? null;
    $corte = $data['corte'] ?? false;
    $color = $data['color'] ?? null;
    $stock = $data['stock'] ?? 0;
    $precio_venta = $data['precio_venta'] ?? 0;
    $precio_compra = $data['precio_compra'] ?? 0;
    $marca = $data['marca'] ?? null;
    $json_url_img = $data['json_url_img'] ?? null;
    $sucursal_id = $data['sucursal_id'];

    // ✅ CAMPOS DE IMPUESTO Y PRESENTACIONES
    $f_sunat = isset($data['f_sunat']) ? $data['f_sunat'] : 'G';
    $impuesto_id = $data['impuesto_id'] ?? null;
    $precios_json = isset($data['precios_json']) ? $data['precios_json'] : null;

    // Validación de impuesto (obligatorio)
    if (!$impuesto_id) {
        echo json_encode([
            'estado' => false,
            'mensaje' => 'Debe seleccionar un impuesto SUNAT'
        ]);
        return;
    }

    try {
        // ✅ SQL CON TODOS LOS PARÁMETROS EN EL ORDEN CORRECTO
        $sql = "SELECT * FROM fn_registrar_articulo_completo(
            :nombre, 
            :categoria_id, 
            :tipo_id, 
            :dimension_id, 
            :escala_id,
            :corte,
            :color,
            :stock,
            :precio_venta, 
            :precio_compra,
            :marca,
            :json_url_img,
            :sucursal_id,
            :f_sunat,
            :impuesto_id,
            :precios_json
        )";

        $stmt = $conectar->prepare($sql);

        // ✅ BINDING DE TODOS LOS PARÁMETROS
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
        $stmt->bindParam(':tipo_id', $tipo_id, PDO::PARAM_INT);
        $stmt->bindParam(':dimension_id', $dimension_id, PDO::PARAM_INT);
        $stmt->bindParam(':escala_id', $escala_id, PDO::PARAM_INT);

        // Convertir booleano a string para PostgreSQL
        $corte_str = $corte ? 'true' : 'false';
        $stmt->bindParam(':corte', $corte_str, PDO::PARAM_STR);

        $stmt->bindParam(':color', $color, PDO::PARAM_STR);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':precio_venta', $precio_venta);
        $stmt->bindParam(':precio_compra', $precio_compra);
        $stmt->bindParam(':marca', $marca, PDO::PARAM_STR);
        $stmt->bindParam(':json_url_img', $json_url_img, PDO::PARAM_STR);
        $stmt->bindParam(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
        $stmt->bindParam(':f_sunat', $f_sunat, PDO::PARAM_STR);
        $stmt->bindParam(':impuesto_id', $impuesto_id, PDO::PARAM_INT);
        $stmt->bindParam(':precios_json', $precios_json, PDO::PARAM_STR);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $articulo_id_creado = $row['articulo_id'] ?? null;

            // ✅ GUARDAR RELACIONES DE PRESENTACIONES
            if ($articulo_id_creado && $precios_json) {
                try {
                    guardarRelacionesArticuloPresentaciones($articulo_id_creado, $precios_json, $sucursal_id);
                } catch (Exception $e) {
                    error_log("Error al guardar relaciones de presentaciones: " . $e->getMessage());
                    // No detenemos el proceso, solo registramos el error
                }
            }

            echo json_encode([
                'estado' => $row['estado'],
                'mensaje' => $row['mensaje'],
                'articulo_id' => $articulo_id_creado
            ]);
        } else {
            echo json_encode([
                'estado' => false,
                'mensaje' => 'No se recibió respuesta de la base de datos'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'estado' => false,
            'mensaje' => 'Error al procesar el Registro. Consultar con el Administrador de Sistemas.',
            'error' => $e->getMessage(),
            'jsonEntrada' => $data
        ]);
    }
}

function fnElimarArticulo($id)
{
    global $conectar;

    try {
        // ✅ Agregar verificación de sucursal_id
        $sucursal_id = isset($_POST['sucursal_id']) ? $_POST['sucursal_id'] : null;

        if (!$sucursal_id) {
            echo json_encode(['estado' => false, 'mensaje' => 'sucursal_id no proporcionado']);
            return;
        }

        $sql = "UPDATE articulo SET deleted_at = CURRENT_TIMESTAMP 
                WHERE id = :id AND sucursal_id = :sucursal_id";

        $stmt = $conectar->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":sucursal_id", $sucursal_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode(['estado' => true, 'mensaje' => 'Artículo eliminado con éxito']);
        } else {
            echo json_encode(['estado' => false, 'mensaje' => 'No se realizó ninguna actualización. Verifique los datos']);
        }
    } catch (\Throwable $th) {
        echo json_encode(['estado' => false, 'mensaje' => $th->getMessage()]);
    }
}


function paEditarArticuloCompleto($jsDatosArticulo)
{
    global $conectar;

    $data = json_decode($jsDatosArticulo, true);

    // Validación de sucursal_id
    if (!isset($data['sucursal_id']) || empty($data['sucursal_id'])) {
        echo json_encode([
            'estado' => false,
            'mensaje' => 'Error: sucursal_id no proporcionado o inválido'
        ]);
        return;
    }

    // ✅ ASIGNACIÓN CORRECTA DE VARIABLES
    $id = $data['id'];
    $nombre = $data['nombre'];
    $categoria_id = $data['categoria_id'] ?? null;
    $tipo_id = $data['tipo_id'] ?? null;
    $dimension_id = $data['dimension_id'] ?? null;
    $escala_id = $data['escala_id'] ?? null;
    $corte = $data['corte'] ?? false;
    $color = $data['color'] ?? null;
    $stock = $data['stock'] ?? 0;
    $precio_venta = $data['precio_venta'] ?? 0;
    $precio_compra = $data['precio_compra'] ?? 0;
    $marca = $data['marca'] ?? null;
    $json_url_img = $data['json_url_img'] ?? null;
    $sucursal_id = $data['sucursal_id'];

    // ✅ CAMPOS DE IMPUESTO Y PRESENTACIONES
    $f_sunat = isset($data['f_sunat']) ? $data['f_sunat'] : 'G';
    $impuesto_id = $data['impuesto_id'] ?? null;
    $precios_json = isset($data['precios_json']) ? $data['precios_json'] : null;

    // ✅ LOG PARA DEBUG
    error_log("=== EDITAR ARTÍCULO COMPLETO ===");
    error_log("Artículo ID: " . $id);
    error_log("Sucursal ID: " . $sucursal_id);
    error_log("Precios JSON recibido: " . $precios_json);

    // Validación de impuesto (obligatorio)
    if (!$impuesto_id) {
        echo json_encode([
            'estado' => false,
            'mensaje' => 'Debe seleccionar un impuesto SUNAT'
        ]);
        return;
    }

    try {
        // ✅ SQL CON TODOS LOS PARÁMETROS EN EL ORDEN CORRECTO
        $sql = "SELECT * FROM fn_editar_articulo_completo(
            :id,
            :nombre,
            :categoria_id,
            :tipo_id,
            :dimension_id,
            :escala_id,
            :corte,
            :color,
            :stock,
            :precio_venta,
            :precio_compra,
            :marca,
            :json_url_img,
            :sucursal_id,
            :f_sunat,
            :impuesto_id,
            :precios_json
        )";

        $stmt = $conectar->prepare($sql);

        // ✅ BINDING DE TODOS LOS PARÁMETROS
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
        $stmt->bindParam(':tipo_id', $tipo_id, PDO::PARAM_INT);
        $stmt->bindParam(':dimension_id', $dimension_id, PDO::PARAM_INT);
        $stmt->bindParam(':escala_id', $escala_id, PDO::PARAM_INT);

        // Convertir booleano a string para PostgreSQL
        $corte_str = $corte ? 'true' : 'false';
        $stmt->bindParam(':corte', $corte_str, PDO::PARAM_STR);

        $stmt->bindParam(':color', $color, PDO::PARAM_STR);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':precio_venta', $precio_venta);
        $stmt->bindParam(':precio_compra', $precio_compra);
        $stmt->bindParam(':marca', $marca, PDO::PARAM_STR);
        $stmt->bindParam(':json_url_img', $json_url_img, PDO::PARAM_STR);
        $stmt->bindParam(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
        $stmt->bindParam(':f_sunat', $f_sunat, PDO::PARAM_STR);
        $stmt->bindParam(':impuesto_id', $impuesto_id, PDO::PARAM_INT);
        $stmt->bindParam(':precios_json', $precios_json, PDO::PARAM_STR);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        error_log("Resultado de función SQL: " . json_encode($row));

        if ($row) {
            // ✅✅✅ CORRECCIÓN CRÍTICA: Llamar SIEMPRE a guardarRelacionesArticuloPresentaciones
            // Sin importar lo que devuelva la función SQL
            error_log("Llamando a guardarRelacionesArticuloPresentaciones...");

            try {
                guardarRelacionesArticuloPresentaciones($id, $precios_json, $sucursal_id);
                error_log("✅ guardarRelacionesArticuloPresentaciones ejecutado");
            } catch (Exception $e) {
                error_log("❌ Error en guardarRelacionesArticuloPresentaciones: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                // No detener el proceso, solo registrar el error
            }

            echo json_encode([
                'estado' => true,
                'mensaje' => $row['mensaje'] ?? 'Artículo actualizado correctamente'
            ]);
        } else {
            echo json_encode([
                'estado' => false,
                'mensaje' => 'No se recibió respuesta de la base de datos'
            ]);
        }
    } catch (Exception $e) {
        error_log("❌ Error en paEditarArticuloCompleto: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());

        echo json_encode([
            'estado' => false,
            'mensaje' => 'Error al procesar la actualización. Consultar con el Administrador de Sistemas.',
            'error' => $e->getMessage(),
            'jsonEntrada' => $data
        ]);
    }
}
/** Listar presentaciones disponibles de una sucursal
 */
function listarPresentacionesDisponibles($sucursal_id)
{
    global $conectar;

    // ✅ LOGGING INICIAL
    error_log("=== LISTAR_PRESENTACIONES ===");
    error_log("Sucursal ID recibido: " . $sucursal_id);
    error_log("Tipo: " . gettype($sucursal_id));

    try {
        // Validar que sucursal_id no esté vacío
        if (empty($sucursal_id)) {
            error_log("❌ sucursal_id está vacío");
            echo json_encode([
                'estado' => false,
                'mensaje' => 'sucursal_id no proporcionado',
                'datos' => []
            ]);
            return;
        }

        $sql = "SELECT id, codigo, presentacion, cantidad_numero 
                FROM unidadescompra 
                WHERE deleted_at IS NULL 
                AND sucursal_id = :sucursal_id
                ORDER BY presentacion";

        error_log("SQL a ejecutar: " . $sql);

        $stmt = $conectar->prepare($sql);
        $stmt->bindParam(':sucursal_id', $sucursal_id, PDO::PARAM_INT);

        error_log("Ejecutando query...");
        $stmt->execute();

        $presentaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        error_log("Presentaciones encontradas: " . count($presentaciones));
        error_log("Datos: " . json_encode($presentaciones));

        // ✅ SIEMPRE DEVOLVER JSON VÁLIDO
        $response = [
            'estado' => true,
            'datos' => $presentaciones,
            'total' => count($presentaciones),
            'mensaje' => count($presentaciones) > 0
                ? 'Presentaciones cargadas correctamente'
                : 'No hay presentaciones registradas',
            'sucursal_id' => $sucursal_id
        ];

        error_log("Respuesta a enviar: " . json_encode($response));

        // ✅ LIMPIAR BUFFER Y ENVIAR SOLO JSON
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode($response);
    } catch (Exception $e) {
        error_log("❌ Error en listarPresentacionesDisponibles: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());

        ob_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'estado' => false,
            'mensaje' => 'Error al cargar presentaciones: ' . $e->getMessage(),
            'datos' => [],
            'error_detalle' => $e->getMessage()
        ]);
    }
}

/**
 * Obtener precios de presentaciones de un artículo
 * ✅ VERSIÓN CORREGIDA
 */
function obtenerPreciosArticulo($articulo_id, $sucursal_id)
{
    global $conectar;

    try {
        error_log("=== OBTENER_PRECIOS_ARTICULO (VERSIÓN CORREGIDA) ===");
        error_log("Artículo ID: " . $articulo_id);
        error_log("Sucursal ID: " . $sucursal_id);

        // ✅ NUEVA QUERY CORREGIDA
        // Esta query obtiene los precios directamente del JSON guardado
        // Y los cruza con la información de las presentaciones
        $sql = "
            SELECT 
                t1.id as articulo_id,
                t1.precios_json,
                precio_item->>'unidadescompra_id' as unidadescompra_id,
                CAST(precio_item->>'precio' AS NUMERIC) as precio,
                t2.codigo,
                t2.presentacion,
                t2.cantidad_numero
            FROM articulo t1
            CROSS JOIN LATERAL jsonb_array_elements(
                COALESCE(t1.precios_json::jsonb, '[]'::jsonb)
            ) AS precio_item
            LEFT JOIN unidadescompra t2 
                ON t2.id = CAST(precio_item->>'unidadescompra_id' AS INTEGER)
            WHERE t1.id = :articulo_id
            AND t1.sucursal_id = :sucursal_id
            AND t1.deleted_at IS NULL
            AND (t2.deleted_at IS NULL OR t2.id IS NULL)
            ORDER BY t2.presentacion
        ";

        error_log("SQL ejecutada: " . $sql);

        $stmt = $conectar->prepare($sql);
        $stmt->bindParam(':articulo_id', $articulo_id, PDO::PARAM_INT);
        $stmt->bindParam(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
        $stmt->execute();

        $precios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        error_log("Resultados encontrados: " . count($precios));
        error_log("Datos completos: " . json_encode($precios));

        // ✅ LIMPIAR BUFFER Y ENVIAR JSON LIMPIO
        ob_clean();
        header('Content-Type: application/json');

        $response = [
            'estado' => true,
            'datos' => $precios,
            'total' => count($precios),
            'mensaje' => count($precios) > 0
                ? 'Precios cargados correctamente'
                : 'No hay precios registrados para este artículo',
            'debug' => [
                'articulo_id' => $articulo_id,
                'sucursal_id' => $sucursal_id,
                'query_type' => 'DESDE_JSON'
            ]
        ];

        error_log("Respuesta JSON: " . json_encode($response));

        echo json_encode($response);
    } catch (Exception $e) {
        error_log("❌ Error en obtenerPreciosArticulo: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());

        ob_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'estado' => false,
            'mensaje' => 'Error al cargar precios: ' . $e->getMessage(),
            'datos' => [],
            'error_detalle' => $e->getMessage()
        ]);
    }
}
/**
 * Guardar relaciones de artículo con presentaciones
 * ✅ VERSIÓN CORREGIDA
 */
function guardarRelacionesArticuloPresentaciones($articulo_id, $precios_json, $sucursal_id)
{
    global $conectar;

    try {
        error_log("=== GUARDAR RELACIONES (DEBUG) ===");
        error_log("Artículo ID: " . $articulo_id);
        error_log("Sucursal ID: " . $sucursal_id);
        error_log("JSON recibido: " . $precios_json);
        error_log("Tipo de JSON: " . gettype($precios_json));

        // ✅ PASO 1: ELIMINAR RELACIONES ANTERIORES
        $sqlDelete = "DELETE FROM rel_articulounidadescompra 
                     WHERE articulo_id = :articulo_id";

        $stmtDelete = $conectar->prepare($sqlDelete);
        $stmtDelete->bindParam(':articulo_id', $articulo_id, PDO::PARAM_INT);
        $stmtDelete->execute();

        $eliminados = $stmtDelete->rowCount();
        error_log("✅ Relaciones eliminadas: " . $eliminados);

        // ✅ PASO 2: Si no hay precios, retornar
        if (empty($precios_json) || $precios_json === 'null' || $precios_json === null) {
            error_log("⚠️ No hay precios para guardar");

            // También limpiar el JSON en la tabla articulo
            $sqlUpdateArticulo = "UPDATE articulo SET precios_json = NULL WHERE id = :articulo_id";
            $stmtUpdate = $conectar->prepare($sqlUpdateArticulo);
            $stmtUpdate->bindParam(':articulo_id', $articulo_id, PDO::PARAM_INT);
            $stmtUpdate->execute();

            return true;
        }

        // ✅ PASO 3: Decodificar JSON
        $precios = json_decode($precios_json, true);

        error_log("JSON decodificado: " . print_r($precios, true));
        error_log("Es array: " . (is_array($precios) ? 'SI' : 'NO'));
        error_log("Cantidad de elementos: " . (is_array($precios) ? count($precios) : 0));

        if (!is_array($precios) || empty($precios)) {
            error_log("⚠️ JSON no válido o vacío");
            return true;
        }

        error_log("📊 Cantidad de presentaciones a insertar: " . count($precios));

        // ✅ PASO 4: Insertar nuevas relaciones
        $sqlInsert = "INSERT INTO rel_articulounidadescompra 
                     (articulo_id, unidadescompra_id, created_at) 
                     VALUES (:articulo_id, :unidadescompra_id, CURRENT_TIMESTAMP)";

        $stmtInsert = $conectar->prepare($sqlInsert);
        $insertados = 0;
        $errores = [];

        foreach ($precios as $index => $precio) {
            error_log("--- Procesando presentación " . ($index + 1) . " ---");
            error_log("Datos: " . json_encode($precio));

            if (!isset($precio['unidadescompra_id'])) {
                error_log("  ⚠️ Falta unidadescompra_id");
                $errores[] = "Falta unidadescompra_id en índice " . $index;
                continue;
            }

            $unidadescompra_id = intval($precio['unidadescompra_id']);
            error_log("  unidadescompra_id: " . $unidadescompra_id);

            // ✅ VERIFICAR que la presentación existe y pertenece a la sucursal
            $sqlCheck = "SELECT COUNT(*) as existe, presentacion 
                        FROM unidadescompra 
                        WHERE id = :unidadescompra_id 
                        AND sucursal_id = :sucursal_id 
                        AND deleted_at IS NULL";

            $stmtCheck = $conectar->prepare($sqlCheck);
            $stmtCheck->bindParam(':unidadescompra_id', $unidadescompra_id, PDO::PARAM_INT);
            $stmtCheck->bindParam(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
            $stmtCheck->execute();

            $checkResult = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if ($checkResult && $checkResult['existe'] > 0) {
                error_log("  ✓ Presentación válida: " . ($checkResult['presentacion'] ?? 'N/A'));

                // ✅ INSERTAR
                $stmtInsert->bindParam(':articulo_id', $articulo_id, PDO::PARAM_INT);
                $stmtInsert->bindParam(':unidadescompra_id', $unidadescompra_id, PDO::PARAM_INT);

                if ($stmtInsert->execute()) {
                    $insertados++;
                    error_log("  ✓ Insertado correctamente (Total: $insertados)");
                } else {
                    $errorInfo = $stmtInsert->errorInfo();
                    error_log("  ✗ Error al insertar: " . json_encode($errorInfo));
                    $errores[] = "Error al insertar presentación " . $unidadescompra_id;
                }
            } else {
                error_log("  ✗ Presentación NO válida o no pertenece a la sucursal");
                $errores[] = "Presentación inválida: " . $unidadescompra_id;
            }
        }

        error_log("=== RESUMEN ===");
        error_log("Total insertados: " . $insertados);
        error_log("Total errores: " . count($errores));
        if (!empty($errores)) {
            error_log("Errores: " . json_encode($errores));
        }
        error_log("===============");

        // ✅ PASO 5: Actualizar el campo precios_json en la tabla articulo
        $sqlUpdateArticulo = "UPDATE articulo SET precios_json = :precios_json WHERE id = :articulo_id";
        $stmtUpdateArticulo = $conectar->prepare($sqlUpdateArticulo);
        $stmtUpdateArticulo->bindParam(':precios_json', $precios_json, PDO::PARAM_STR);
        $stmtUpdateArticulo->bindParam(':articulo_id', $articulo_id, PDO::PARAM_INT);
        $stmtUpdateArticulo->execute();

        error_log("✅ Campo precios_json actualizado en tabla articulo");

        return true;
    } catch (Exception $e) {
        error_log("❌ ERROR en guardarRelacionesArticuloPresentaciones: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        throw $e;
    }
}
/**

 * Función auxiliar para validar el JSON de precios
 */
function validarPreciosJson($precios_json)
{
    if (empty($precios_json) || $precios_json === 'null') {
        return ['valido' => false, 'mensaje' => 'JSON vacío'];
    }

    $precios = json_decode($precios_json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['valido' => false, 'mensaje' => 'Error al decodificar JSON: ' . json_last_error_msg()];
    }

    if (!is_array($precios)) {
        return ['valido' => false, 'mensaje' => 'El JSON no contiene un array'];
    }

    foreach ($precios as $index => $precio) {
        if (!isset($precio['unidadescompra_id'])) {
            return ['valido' => false, 'mensaje' => 'Falta unidadescompra_id en índice ' . $index];
        }
        if (!isset($precio['precio'])) {
            return ['valido' => false, 'mensaje' => 'Falta precio en índice ' . $index];
        }
    }

    return ['valido' => true, 'mensaje' => 'JSON válido', 'datos' => $precios];
}

function toggle_estado_articulo_completo($id, $accion)
{
    global $conectar;

    try {
        // ✅ Agregar verificación de sucursal_id
        $sucursal_id = isset($_POST['sucursal_id']) ? $_POST['sucursal_id'] : null;

        if (!$sucursal_id) {
            echo json_encode(["error" => true, "message" => "sucursal_id no proporcionado."]);
            return;
        }

        // Verificar si el artículo existe EN LA SUCURSAL
        $verificarArticulo = $conectar->prepare("SELECT COUNT(*) FROM articulo WHERE id = :id AND sucursal_id = :sucursal_id");
        $verificarArticulo->bindParam(":id", $id);
        $verificarArticulo->bindParam(":sucursal_id", $sucursal_id, PDO::PARAM_INT);
        $verificarArticulo->execute();
        $articuloExistente = $verificarArticulo->fetchColumn();

        if ($articuloExistente == 0) {
            echo json_encode(["error" => true, "message" => "Artículo no encontrado en esta sucursal."]);
            return;
        }

        // Determinar la acción
        if ($accion == "BLOQUEAR_ARTICULO") {
            $sql = "UPDATE articulo SET disponibilidad_venta_fh = NOW(), disponibilidad_venta = TRUE 
                    WHERE id = :id AND sucursal_id = :sucursal_id";
        } elseif ($accion == "DESBLOQUEAR_ARTICULO") {
            $sql = "UPDATE articulo SET disponibilidad_venta_fh = NULL, disponibilidad_venta = FALSE 
                    WHERE id = :id AND sucursal_id = :sucursal_id";
        } else {
            echo json_encode(["error" => true, "message" => "Acción no válida."]);
            return;
        }

        $orden = $conectar->prepare($sql);
        $orden->bindParam(":id", $id);
        $orden->bindParam(":sucursal_id", $sucursal_id, PDO::PARAM_INT);
        $orden->execute();

        echo json_encode(["success" => true, "message" => "Estado del artículo actualizado."]);
    } catch (\Throwable $th) {
        error_log("Error en toggle_estado_articulo: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}


function abonarDeuda($jsDatosAbono)
{
    global $conectar;


    $data = json_decode($jsDatosAbono, true);


    $cliente_id = $data['cliente_id'];
    $usuario_id = $data['usuario_id'];
    $montoAbono = $data['montoAbono'];
    $json_pagos_abono = json_encode($data['js_detalle_pagos']);


    try {

        $sql = "SELECT fn_pagar_deuda(:p_cliente_id,:usuario_id_p,:monto_abono_p,:json_pagos_p);";
        $stmt = $conectar->prepare($sql);


        $stmt->bindParam(':p_cliente_id', $cliente_id);
        $stmt->bindParam(':usuario_id_p', $usuario_id);
        $stmt->bindParam(':monto_abono_p', $montoAbono);
        $stmt->bindParam(':json_pagos_p', $json_pagos_abono);

        $stmt->execute();


        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $jsonResponse = $row['fn_pagar_deuda'];

        $response = json_decode($jsonResponse, true);

        echo json_encode($response);
    } catch (Exception $e) {

        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar la venta. Consultar con el Administrador de Sistemas']);
    }
}

function paRegistrarCompra($jsDatosCompra)
{
    global $conectar;

    $data = json_decode($jsDatosCompra, true);

    try {

        $sql = "SELECT fn_registrar_compra(:jsDatosCompra);";
        $stmt = $conectar->prepare($sql);


        $stmt->bindParam(':jsDatosCompra', $jsDatosCompra);

        $stmt->execute();


        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $jsonResponse = $row['fn_registrar_compra'];

        $response = json_decode($jsonResponse, true);

        echo json_encode($response);
    } catch (Exception $e) {

        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar la venta. Consultar con el Administrador de Sistemas']);
    }
}
function fnAperturaDeCajaChica($jsDatoCaja)
{
    global $conectar;

    $data = json_decode($jsDatoCaja, true);

    $responsable_id = $data['responsable_id'];
    $responsable = $data['responsable'];
    $monto = $data['monto'];
    $sucursal_id = $data['sucursal_id'];
    

    try {
        $sql = "SELECT fn_apertura_caja(:responsable_id, :responsable, :monto, :sucursal_id)";
        $stmt = $conectar->prepare($sql);

        $stmt->bindParam(':responsable_id', $responsable_id);
        $stmt->bindParam(':responsable', $responsable);
        $stmt->bindParam(':monto', $monto);
        $stmt->bindParam(':sucursal_id',$sucursal_id);


        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $jsonResponse = $row['fn_apertura_caja'];

        $response = json_decode($jsonResponse, true);
        echo json_encode($response);
    } catch (Exception $e) {
        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar la venta. Consultar con el Administrador de Sistemas.' . $e]);
    }
}

function fnInsertDetalleCajaChica($jsDatosDetalleCaja)
{
    global $conectar;

    try {

        $data = json_decode($jsDatosDetalleCaja, true);

        $caja_id = $data['caja_id'];
        $responsable_id = $data['responsable_id'];
        $responsable = $data['responsable'];
        $tipo_movimiento = $data['tipo_movimiento'];
        $monto_caja_chica = $data['monto_caja_chica'];
        $concepto_id = $data['concepto_id'];
        $concepto_egreso = $data['concepto_egreso'];
        $nota_caja_chica = $data['nota_caja_chica'];


        $conectar->beginTransaction();


        $sql = "
            INSERT INTO detalle_caja_chica
            (caja_id, concepto_id, monto, responsable_id, responsable, tipo_movimiento, concepto_egreso, nota)
            VALUES
            (:caja_id, :concepto_id, :monto_caja_chica, :responsable_id, :responsable, :tipo_movimiento, :concepto_egreso, :nota_caja_chica)
        ";


        $stmt = $conectar->prepare($sql);


        $stmt->bindParam(':caja_id', $caja_id);
        $stmt->bindParam(':responsable_id', $responsable_id);
        $stmt->bindParam(':responsable', $responsable);
        $stmt->bindParam(':tipo_movimiento', $tipo_movimiento);
        $stmt->bindParam(':monto_caja_chica', $monto_caja_chica);
        $stmt->bindParam(':concepto_id', $concepto_id);
        $stmt->bindParam(':concepto_egreso', $concepto_egreso);
        $stmt->bindParam(':nota_caja_chica', $nota_caja_chica, PDO::PARAM_STR); // Usar PDO::PARAM_STR para las notas, en caso de que sea null


        $stmt->execute();


        $conectar->commit();


        echo json_encode(['estado' => true, 'mensaje' => 'Egreso Registrado']);
    } catch (Exception $e) {

        $conectar->rollBack();


        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar la solicitud. Detalles: ' . $e->getMessage()]);
    }
}
function fnCierreCajaChica($idCaja)
{
    global $conectar;

    try {

        $conectar->beginTransaction();

        $sql = " UPDATE caja set cierre=CURRENT_TIMESTAMP where id = :idCaja ;";

        $stmt = $conectar->prepare($sql);
        $stmt->bindParam(':idCaja', $idCaja, PDO::PARAM_INT);

        $stmt->execute();

        $conectar->commit();


        echo json_encode(['estado' => true, 'mensaje' => 'Caja Chica CERRADA!!!']);
    } catch (Exception $e) {

        $conectar->rollBack();


        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar la solicitud. Detalles: ' . $e->getMessage()]);
    }
}


function fnInsertPersonaUsurio($jsDatos)
{
    global $conectar;

    try {

        $data = json_decode($jsDatos, true);


        $idPersonaEncontrada = $data['idPersonaEncontrada'];
        $numero_documento = $data['numero_documento'];
        $tipo_persona = 'NATURAL';
        $condicion = 'EMPLEADO';
        $nombres = $data['nombres'];
        $apellidos = $data['apellidos'];
        $telefonomovil = $data['numero_telefono_movil'];

        $tipo_empleado = 'VENTA';
        $rol = '2';
        $sueldo = $data['sueldo'];
        $horas_trabajo = $data['horas_trabajo'];
        $dias_trabajo_semana = $data['dias_trabajo_semana'];
        $usuario = $data['usuario'];
        $contrasenia = $data['contrasenia'];
        $hashedPassword = '';
        if ($contrasenia !== null && $contrasenia !== '') {
            $hashedPassword = password_hash($contrasenia, PASSWORD_BCRYPT);
        } else {
            $hashedPassword = '';
        }


        $sqlCheck = "SELECT COUNT(*) FROM persona WHERE numero_documento = :numero_documento";
        $stmtCheck = $conectar->prepare($sqlCheck);
        $stmtCheck->bindParam(':numero_documento', $numero_documento);
        $stmtCheck->execute();

        $count = $stmtCheck->fetchColumn();

        if ($count > 0) {
            echo json_encode(['estado' => false, 'mensaje' => 'El número de documento ya está registrado en el sistema.']);
            return;
        } else {
            $conectar->beginTransaction();


            if ($idPersonaEncontrada == "#") {

                $sqlInsert = "
                INSERT INTO persona
                (numero_documento, tipo_persona, condicion, nombres, apellidos, telefonomovil)
                VALUES
                (:numero_documento, :tipo_persona, :condicion, :nombres, :apellidos, :telefonomovil)
            ";

                $stmt = $conectar->prepare($sqlInsert);

                $stmt->bindParam(':numero_documento', $numero_documento);
                $stmt->bindParam(':tipo_persona', $tipo_persona);
                $stmt->bindParam(':condicion', $condicion);
                $stmt->bindParam(':nombres', $nombres);
                $stmt->bindParam(':apellidos', $apellidos);
                $stmt->bindParam(':telefonomovil', $telefonomovil);

                $stmt->execute();


                $ultimoIdPersona = $conectar->lastInsertId();


                $sqlInsertUsuario = "
                INSERT INTO usuario
                (persona_id, username, password, rol, tipo_empleado, sueldo, cantidad_horas_trabajo, cantidad_dias_semana,deleted_at)
                VALUES
                (:persona_id, :username, :password, :rol, :tipo_empleado, :sueldo, :cantidad_horas_trabajo, :cantidad_dias_semana,CURRENT_TIMESTAMP)
            ";
                $stmt = $conectar->prepare($sqlInsertUsuario);

                $stmt->bindParam(':persona_id', $ultimoIdPersona);
                $stmt->bindParam(':username', $usuario);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->bindParam(':rol', $rol);
                $stmt->bindParam(':tipo_empleado', $tipo_empleado);
                $stmt->bindParam(':sueldo', $sueldo);
                $stmt->bindParam(':cantidad_horas_trabajo', $horas_trabajo);
                $stmt->bindParam(':cantidad_dias_semana', $dias_trabajo_semana);

                $stmt->execute();


                $conectar->commit();
            } else {
                $sqlUpdatePersona = "
                UPDATE persona
                SET 
                    numero_documento = :numero_documento,
                    nombres = :nombres,
                    apellidos = :apellidos,
                    telefonomovil = :telefonomovil
                WHERE id = :idPersona
            ";
                $stmt = $conectar->prepare($sqlUpdatePersona);
                $stmt->bindParam(':numero_documento', $numero_documento);
                $stmt->bindParam(':nombres', $nombres);
                $stmt->bindParam(':apellidos', $apellidos);
                $stmt->bindParam(':telefonomovil', $telefonomovil);
                $stmt->bindParam(':idPersona', $idPersonaEncontrada);

                $stmt->execute();


                $sqlInsertUsuario = "
                INSERT INTO usuario
                (persona_id, username, password, rol, tipo_empleado, sueldo, cantidad_horas_trabajo, cantidad_dias_semana)
                VALUES
                (:persona_id, :username, :password, :rol, :tipo_empleado, :sueldo, :cantidad_horas_trabajo, :cantidad_dias_semana)
            ";

                $stmt = $conectar->prepare($sqlInsertUsuario);

                $stmt->bindParam(':persona_id', $idPersonaEncontrada);
                $stmt->bindParam(':username', $usuario);

                $stmt->bindParam(':password', $hashedPassword);
                $stmt->bindParam(':rol', $rol);
                $stmt->bindParam(':tipo_empleado', $tipo_empleado);
                $stmt->bindParam(':sueldo', $sueldo);
                $stmt->bindParam(':cantidad_horas_trabajo', $horas_trabajo);
                $stmt->bindParam(':cantidad_dias_semana', $dias_trabajo_semana);

                $stmt->execute();

                $conectar->commit();
            }


            echo json_encode(['estado' => true, 'mensaje' => 'Registrado :)']);
        }
    } catch (Exception $e) {

        $conectar->rollBack();
        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar la solicitud. Detalles: ' . $e->getMessage()]);
    }
}


function fnInsertDetalleCajaGrande($jsDatosDetalleCaja)
{
    global $conectar;

    try {

        $data = json_decode($jsDatosDetalleCaja, true);
        $forma_pago_id = $data['forma_pago_id'];
        $responsable_id = $data['responsable_id'];
        $responsable = $data['responsable'];
        $tipo_movimiento = $data['tipo_movimiento'];
        $monto_caja_grande = $data['monto_caja_grande'];
        $concepto_id = $data['concepto_id'];
        $concepto_egreso = $data['concepto_egreso'];
        $nota_caja_grande = $data['nota_caja_grande'];
        $movimiento_caja_v2 = "";
        if ($tipo_movimiento === "INGRESO") {
            $movimiento_caja_v2 = 'INGRESO DE CAJA';
        } else {
            $movimiento_caja_v2 = 'EGRESO DE CAJA';
        }


        $conectar->beginTransaction();


        $sql = "
        INSERT INTO detalle_caja_grande
        (forma_pago_id,concepto_id,tipo_movimiento,monto,responsable_id,responsable,concepto,nota,movimiento_caja_v2)
        VALUES
        (:forma_pago_id,:concepto_id,:tipo_movimiento ,:monto_caja_grande, :responsable_id, :responsable, :concepto_egreso, :nota_caja_grande,:movimiento_caja_v2)
        ";


        $stmt = $conectar->prepare($sql);


        $stmt->bindParam(':forma_pago_id', $forma_pago_id);
        $stmt->bindParam(':responsable_id', $responsable_id);
        $stmt->bindParam(':responsable', $responsable);
        $stmt->bindParam(':tipo_movimiento', $tipo_movimiento);
        $stmt->bindParam(':monto_caja_grande', $monto_caja_grande);
        $stmt->bindParam(':concepto_id', $concepto_id);
        $stmt->bindParam(':concepto_egreso', $concepto_egreso);
        $stmt->bindParam(':nota_caja_grande', $nota_caja_grande, PDO::PARAM_STR);
        $stmt->bindParam(':movimiento_caja_v2', $movimiento_caja_v2);

        $stmt->execute();


        $conectar->commit();


        echo json_encode(['estado' => true, 'mensaje' => 'Egreso de Caja Registrado :)']);
    } catch (Exception $e) {

        $conectar->rollBack();


        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar la solicitud. Detalles: ' . $e->getMessage()]);
    }
}

function fnInsertMedioDePago($jsDatos)
{
    global $conectar;

    try {
        $data = json_decode($jsDatos, true);
        $forma_pago = $data['medio_pago'];
        $color = $data['color'];
        $icono = $data['icono'];
        $monto = $data['monto'];
        $sucursal_id = isset($data['sucursal_id']) ? $data['sucursal_id'] : null; // *** NUEVO ***

        // Validar que sucursal_id no sea null
        if ($sucursal_id === null) {
            echo json_encode(['estado' => false, 'mensaje' => 'Error: No se proporcionó el ID de sucursal']);
            return;
        }

        $conectar->beginTransaction();

        // *** SQL CORREGIDO ***
        $sql = "
        INSERT INTO forma_pago
        (nombre, color, icon, monto, sucursal_id)
        VALUES
        (:forma_pago, :color, :icono, :monto, :sucursal_id);
        ";

        $stmt = $conectar->prepare($sql);

        $stmt->bindParam(':forma_pago', $forma_pago);
        $stmt->bindParam(':color', $color);
        $stmt->bindParam(':icono', $icono);
        $stmt->bindParam(':monto', $monto);
        $stmt->bindParam(':sucursal_id', $sucursal_id); // *** NUEVO ***

        $stmt->execute();

        $conectar->commit();

        echo json_encode(['estado' => true, 'mensaje' => 'Método de Pago Registrado correctamente para la sucursal :)']);
    } catch (Exception $e) {
        $conectar->rollBack();
        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar la solicitud. Detalles: ' . $e->getMessage()]);
    }
}

function fnInsertServicio($jsDatos)
{
    global $conectar;

    try {
        $data = json_decode($jsDatos, true);

        // Convertir el array de medidas en una cadena que PostgreSQL entiende
        // Esto es necesario para insertar correctamente el array en PostgreSQL
        $medidas = '{' . implode(',', $data['medidas']) . '}';

        $conectar->beginTransaction();

        $sql = "
        insert into movimiento
        (descripcion, medidas)
        VALUES
        (:descripcion, :medidas);
        ";

        $stmt = $conectar->prepare($sql);

        $stmt->bindParam(':descripcion', $data['descripcion']);
        $stmt->bindParam(':medidas', $medidas);  // Usamos la cadena formateada

        $stmt->execute();

        $conectar->commit();

        echo json_encode(['estado' => true, 'mensaje' => 'Servicio Registrado :)']);
    } catch (Exception $e) {
        $conectar->rollBack();

        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar la solicitud. Detalles: ' . $e->getMessage()]);
    }
}

function fnEditarServicio($jsDatos)
{
    global $conectar;

    try {
        $data = json_decode($jsDatos, true);

        $medidas = '{' . implode(',', $data['medidas']) . '}';

        $conectar->beginTransaction();

        $sql = "
        UPDATE movimiento
        set
        descripcion = :descripcion,
        medidas = :medidas
        where id = :id
        ";

        $stmt = $conectar->prepare($sql);

        $stmt->bindParam(':descripcion', $data['descripcion']);
        $stmt->bindParam(':medidas', $medidas);
        $stmt->bindParam(':id', $data['id']);


        $stmt->execute();

        $conectar->commit();

        echo json_encode(['estado' => true, 'mensaje' => 'Servicio Registrado :)']);
    } catch (Exception $e) {
        $conectar->rollBack();

        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar la solicitud. Detalles: ' . $e->getMessage()]);
    }
}

function fnVaciarCaja($jsDatos)
{
    global $conectar;

    try {
        $data = json_decode($jsDatos, true);

        $id_medio_pago = $data["id"];
        $responsable_id = $data["responsable_id"];
        $responsable = $data["responsable"];

        // Inicia la transacción
        $conectar->beginTransaction();

        // Primero, actualizamos el monto a 0
        $sql_update = "
            UPDATE forma_pago 
            SET monto = 0
            WHERE id = :id_medio_pago
        ";
        $stmt_update = $conectar->prepare($sql_update);
        $stmt_update->bindParam(':id_medio_pago', $id_medio_pago);
        $stmt_update->execute();

        // Luego, insertamos en detalle_caja_grande
        $sql_insert = "
            INSERT INTO detalle_caja_grande
            (forma_pago_id, concepto_id, tipo_movimiento, monto, responsable_id, responsable, nota, movimiento_caja_v2)
            VALUES
            (:id_medio_pago, 25, 'CAJA EN S/ 00.00', 0, :responsable_id, :responsable, 'SE DEJO LA CAJA EN 0', 'CAJA EN 0')
        ";
        $stmt_insert = $conectar->prepare($sql_insert);
        $stmt_insert->bindParam(':id_medio_pago', $id_medio_pago);
        $stmt_insert->bindParam(':responsable_id', $responsable_id);
        $stmt_insert->bindParam(':responsable', $responsable);
        $stmt_insert->execute();

        // Confirmamos la transacción
        $conectar->commit();

        echo json_encode(['estado' => true, 'mensaje' => 'Método de Pago vaciado correctamente :)']);
    } catch (Exception $e) {
        $conectar->rollBack();
        echo json_encode([
            'estado' => false,
            'mensaje' => 'Error al procesar la solicitud. Detalles: ' . $e->getMessage()
        ]);
    }
}

function fnDarDeAltaOrBajaMedioDePago($jsDatos)
{
    global $conectar;

    try {

        $data = json_decode($jsDatos, true);
        $sql = "";
        if ($data['estado'] === "BAJA") {
            $sql = "
            UPDATE forma_pago set unsubscribe = CURRENT_TIMESTAMP
            WHERE id = :id_medio_pago;
            ";
        } else {
            $sql = "
            UPDATE forma_pago set unsubscribe = null
            WHERE id = :id_medio_pago;
            ";
        }
        $stmt = $conectar->prepare($sql);
        $stmt->bindParam(':id_medio_pago', $data["id"]);
        $stmt->execute();

        $conectar->commit();


        echo json_encode(['estado' => true, 'mensaje' => 'Metodo de Pago dado de Baja :)']);
    } catch (Exception $e) {

        $conectar->rollBack();


        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar la solicitud. Detalles: ' . $e->getMessage()]);
    }
}
function fnInsertProveedorAlMomentCompra($jsDatos)
{
    global $conectar;

    try {

        $conectar->beginTransaction();

        $data = json_decode($jsDatos, true);

        $sqlCheck = "SELECT COUNT(*) FROM persona WHERE numero_documento = :numero_documento";
        $stmtCheck = $conectar->prepare($sqlCheck);
        $stmtCheck->bindParam(':numero_documento', $data["numero_documento"]);
        $stmtCheck->execute();

        $count = $stmtCheck->fetchColumn();
        if ($count === 0) {
            $sql = "
            INSERT INTO persona
            (
                numero_documento,
                tipo_persona,
                condicion,
                nombre_comercial,
                razon_social,
                telefonofijo,
                telefonomovil,
                email
            ) 
            VALUES
            (
                :numero_documento,
                :tipo_persona,
                :condicion,
                upper(:nombre_comercial),
                upper(:razon_social),
                :telefonofijo,
                :telefonomovil,
                :email
            )
        ";


            $stmt = $conectar->prepare($sql);
            $stmt->bindParam(':numero_documento', $data["numero_documento"]);
            $stmt->bindParam(':tipo_persona', $data["tipo_persona"]);
            $stmt->bindParam(':condicion', $data["condicion"]);
            $stmt->bindParam(':nombre_comercial', $data["nombre_comercial"]);
            $stmt->bindParam(':razon_social', $data["razon_social"]);
            $stmt->bindParam(':telefonofijo', $data["telefonofijo"]);
            $stmt->bindParam(':telefonomovil', $data["telefonomovil"]);
            $stmt->bindParam(':email', $data["email"]);
            $stmt->execute();


            $ultimoIdPersona = $conectar->lastInsertId();


            $sql_proveedor_ultimo = "SELECT nombre_comercial FROM persona WHERE id = :idUltimo";
            $stmt = $conectar->prepare($sql_proveedor_ultimo);
            $stmt->bindParam(':idUltimo', $ultimoIdPersona);
            $stmt->execute();


            $proveedor = $stmt->fetch(PDO::FETCH_ASSOC);


            $conectar->commit();


            echo json_encode([
                'estado' => true,
                'mensaje' => 'Proveedor Registrado :)',
                'ultimo_id_proveedor' => $ultimoIdPersona,
                'proveedor' => $proveedor['nombre_comercial']
            ]);
        } else {
            echo json_encode([
                'estado' => false,
                'mensaje' => 'Proveedor Ya existe :)',
                'ultimo_id_proveedor' => -1,
                'proveedor' => 'Ya Existe'
            ]);
        }
    } catch (Exception $e) {

        if ($conectar->inTransaction()) {
            $conectar->rollBack();
        }
        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar la solicitud. Detalles: ' . $e->getMessage()]);
    }
}

function fnUpdateEmisor($jsDatos = null) {
    global $conectar;

    try {
        $conectar->beginTransaction();

        if ($jsDatos !== null) {
            $data = json_decode($jsDatos, true);
        } else {
            $data = $_POST;
        }

        if (!isset($data['sucursal_id']) || empty($data['sucursal_id'])) {
            ob_clean();
            echo json_encode(['estado' => false, 'mensaje' => 'Error: sucursal_id no proporcionado']);
            return;
        }

        $sucursal_id = $data['sucursal_id'];
        $ambiente    = in_array($data['ambiente'] ?? '', ['beta', 'produccion'])
                       ? $data['ambiente'] : 'beta';

        $serie_boleta  = preg_match('/^[A-Z]{1}[0-9]{3}$/', $data['serie_boleta']  ?? '')
                         ? strtoupper($data['serie_boleta'])  : 'B001';
        $serie_factura = preg_match('/^[A-Z]{1}[0-9]{3}$/', $data['serie_factura'] ?? '')
                         ? strtoupper($data['serie_factura']) : 'F001';

        // ── Carpeta base de la sucursal ────────────────────────────────
        // __DIR__ = .../caracol_saas/logica
        // dirname(__DIR__) = .../caracol_saas
        $base_dir = dirname(__DIR__) . '/sucursales/' . $sucursal_id . '/';

        if (!is_dir($base_dir)) {
            if (!@mkdir($base_dir, 0777, true)) {
                throw new Exception('No se pudo crear el directorio: ' . $base_dir);
            }
        }

        // ── Logo ───────────────────────────────────────────────────────
        $ruta_logo = null;

        if (isset($_FILES['logo_sucursal']) && $_FILES['logo_sucursal']['error'] === UPLOAD_ERR_OK) {

            if ($_FILES['logo_sucursal']['size'] > 2 * 1024 * 1024) {
                throw new Exception('El logo no debe superar los 2MB');
            }

            $ext = strtolower(pathinfo($_FILES['logo_sucursal']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                throw new Exception('Solo se permiten JPG, JPEG o PNG para el logo');
            }

            $nombre  = 'logo_' . $sucursal_id . '.' . $ext;
            $destino = $base_dir . $nombre;

            error_log("=== DEBUG LOGO ===");
            error_log("tmp_name: " . $_FILES['logo_sucursal']['tmp_name']);
            error_log("destino: " . $destino);
            error_log("base_dir existe: " . (is_dir($base_dir) ? 'SI' : 'NO'));
            error_log("base_dir writable: " . (is_writable($base_dir) ? 'SI' : 'NO'));

            $resultado = move_uploaded_file($_FILES['logo_sucursal']['tmp_name'], $destino);
            error_log("resultado move_uploaded_file logo: " . ($resultado ? 'OK' : 'FALLÓ'));

            if (!$resultado) {
                throw new Exception('Error al mover el logo. Ruta: ' . $destino . ' | writable: ' . (is_writable($base_dir) ? 'SI' : 'NO'));
            }

            // ✅ Ruta relativa para la BD
            $ruta_logo = 'sucursales/' . $sucursal_id . '/' . $nombre;

        } else {
            $ruta_logo = !empty($data['ruta_logo_actual']) ? $data['ruta_logo_actual'] : null;
        }

        // ── Firma digital (PFX / P12) ──────────────────────────────────
        $ruta_firma = null;

        if (isset($_FILES['firma_digital']) && $_FILES['firma_digital']['error'] === UPLOAD_ERR_OK) {

            if ($_FILES['firma_digital']['size'] > 5 * 1024 * 1024) {
                throw new Exception('El certificado digital no debe superar los 5MB');
            }

            $ext = strtolower(pathinfo($_FILES['firma_digital']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['pfx', 'p12'])) {
                throw new Exception('Solo se permiten PFX o P12 para el certificado digital');
            }

            $nombre  = ($data['ruc'] ?? $sucursal_id) . 'Mp12.' . $ext;
            $destino = $base_dir . $nombre;

            error_log("=== DEBUG FIRMA ===");
            error_log("tmp_name: " . $_FILES['firma_digital']['tmp_name']);
            error_log("destino: " . $destino);
            error_log("base_dir existe: " . (is_dir($base_dir) ? 'SI' : 'NO'));
            error_log("base_dir writable: " . (is_writable($base_dir) ? 'SI' : 'NO'));
            error_log("tmp readable: " . (is_readable($_FILES['firma_digital']['tmp_name']) ? 'SI' : 'NO'));

            $resultado = move_uploaded_file($_FILES['firma_digital']['tmp_name'], $destino);
            error_log("resultado move_uploaded_file firma: " . ($resultado ? 'OK' : 'FALLÓ'));

            if (!$resultado) {
                throw new Exception('Error al mover la firma. Ruta: ' . $destino . ' | writable: ' . (is_writable($base_dir) ? 'SI' : 'NO'));
            }

            // ✅ Ruta relativa para la BD
            $ruta_firma = 'sucursales/' . $sucursal_id . '/' . $nombre;

        } else {
            $archivo_error = isset($_FILES['firma_digital']) ? $_FILES['firma_digital']['error'] : 'NO LLEGÓ EL ARCHIVO';
            error_log("⚠️ firma_digital no llegó o tiene error: " . $archivo_error);
            $ruta_firma = !empty($data['ruta_firma_actual']) ? $data['ruta_firma_actual'] : null;
        }

        // ── ¿Existe ya un emisor para esta sucursal? ───────────────────
        $stmtCheck = $conectar->prepare(
            "SELECT COUNT(*) as total FROM emisor WHERE sucursal_id = :sucursal_id"
        );
        $stmtCheck->bindParam(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
        $stmtCheck->execute();
        $existe = $stmtCheck->fetch(PDO::FETCH_ASSOC)['total'] > 0;

        if ($existe) {
            $sql = "UPDATE emisor SET
                        tipo_documento           = :tipo_documento,
                        ruc                      = :ruc,
                        razon_social             = :razon_social,
                        nombre_comercial         = :nombre_comercial,
                        departamento             = :departamento,
                        provincia                = :provincia,
                        distrito                 = :distrito,
                        direccion                = :direccion,
                        ubigeo                   = :ubigeo,
                        usuario_sol              = :usuario_sol,
                        clave_sol                = :clave_sol,
                        contraseña_firma_digital = :password_firma,
                        ambiente                 = :ambiente,
                        serie_boleta             = :serie_boleta,
                        serie_factura            = :serie_factura,
                        email    = :email,
                        telefono = :telefono

                        "
                . ($ruta_logo  !== null ? ", ruta_logo               = :ruta_logo"              : "")
                . ($ruta_firma !== null ? ", direccion_firma_digital = :direccion_firma_digital" : "")
                . " WHERE sucursal_id = :sucursal_id";
        } else {
            $sql = "INSERT INTO emisor (
                        sucursal_id, tipo_documento, ruc, razon_social, nombre_comercial,
                        departamento, provincia, distrito, direccion, ubigeo,
                        usuario_sol, clave_sol, contraseña_firma_digital, ambiente,
                        serie_boleta, serie_factura, ruta_logo, direccion_firma_digital,email, telefono
                    ) VALUES (
                        :sucursal_id, :tipo_documento, :ruc, :razon_social, :nombre_comercial,
                        :departamento, :provincia, :distrito, :direccion, :ubigeo,
                        :usuario_sol, :clave_sol, :password_firma, :ambiente,
                        :serie_boleta, :serie_factura, :ruta_logo, :direccion_firma_digital,:email, :telefono
                    )";
            if ($ruta_logo  === null) $ruta_logo  = '';
            if ($ruta_firma === null) $ruta_firma = '';
        }

        $stmt = $conectar->prepare($sql);
        $stmt->bindParam(':sucursal_id',      $sucursal_id,           PDO::PARAM_INT);
        $stmt->bindParam(':tipo_documento',   $data['tipo_documento']);
        $stmt->bindParam(':ruc',              $data['ruc']);
        $stmt->bindParam(':razon_social',     $data['razon_social']);
        $stmt->bindParam(':nombre_comercial', $data['nombre_comercial']);
        $stmt->bindParam(':departamento',     $data['departamento']);
        $stmt->bindParam(':provincia',        $data['provincia']);
        $stmt->bindParam(':distrito',         $data['distrito']);
        $stmt->bindParam(':direccion',        $data['direccion']);
        $stmt->bindParam(':ubigeo',           $data['ubigeo']);
        $stmt->bindParam(':usuario_sol',      $data['usuario_sol']);
        $stmt->bindParam(':clave_sol',        $data['clave_sol']);
        $stmt->bindParam(':password_firma',   $data['password_firma']);
        $stmt->bindParam(':ambiente',         $ambiente);
        $stmt->bindParam(':serie_boleta',     $serie_boleta);
        $stmt->bindParam(':serie_factura',    $serie_factura);
        $email    = $data['email']    ?? '';
        $telefono = $data['telefono'] ?? '[]';

        // Validar que sea JSON válido
        if (json_decode($telefono) === null) $telefono = '[]';

        $stmt->bindParam(':email',    $email);
        $stmt->bindParam(':telefono', $telefono);

        if ($existe) {
            if ($ruta_logo  !== null) $stmt->bindParam(':ruta_logo',              $ruta_logo);
            if ($ruta_firma !== null) $stmt->bindParam(':direccion_firma_digital', $ruta_firma);
        } else {
            $stmt->bindParam(':ruta_logo',              $ruta_logo);
            $stmt->bindParam(':direccion_firma_digital', $ruta_firma);
        }

        $stmt->execute();
        $conectar->commit();

        ob_clean();
        echo json_encode([
            'estado'        => true,
            'mensaje'       => 'Emisor ' . ($existe ? 'actualizado' : 'registrado') . ' correctamente — sucursal #' . $sucursal_id,
            'tiene_logo'    => !empty($ruta_logo),
            'tiene_firma'   => !empty($ruta_firma),
            'ambiente'      => $ambiente,
            'serie_boleta'  => $serie_boleta,
            'serie_factura' => $serie_factura,
        ]);

    } catch (Exception $e) {
        if ($conectar->inTransaction()) $conectar->rollBack();
        error_log("❌ Error en fnUpdateEmisor: " . $e->getMessage());
        ob_clean();
        echo json_encode(['estado' => false, 'mensaje' => 'Error al guardar emisor: ' . $e->getMessage()]);
    }
}