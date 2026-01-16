<?php
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
            //fnUpdateEmisor
            if (isset($_POST["jsDatos"])) {
                $jsDatos = $_POST["jsDatos"];
                fnUpdateEmisor($jsDatos);
            }
            break;

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

    $categoria_id = $data['categoria_id'];
    $color = $data['color'];
    $corte = $data['corte'];
    $dimension_id = $data['dimension_id'];
    $escala_id = $data['escala_id'];
    $stock = $data['stock'];
    $precio_venta = $data['precio_venta'];
    $precio_compra = $data['precio_compra'];
    $marca = $data['marca'];
    $nombre = $data['nombre'];
    $tipo_id = $data['tipo_id'];
    $json_url_img = $data['json_url_img'];
    $sucursal_id = $data['sucursal_id'];
    
    // ✅ NUEVO: Obtener precios_json
    $precios_json = isset($data['precios_json']) ? $data['precios_json'] : null;

    try {
        // ✅ ACTUALIZADO: Agregar precios_json a la llamada de función
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
            :precios_json
        )";

        $stmt = $conectar->prepare($sql);

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':categoria_id', $categoria_id);
        $stmt->bindParam(':tipo_id', $tipo_id);
        $stmt->bindParam(':dimension_id', $dimension_id);
        $stmt->bindParam(':escala_id', $escala_id);
        
        $corte_str = $corte ? 'true' : 'false';
        $stmt->bindParam(':corte', $corte_str);
        
        $stmt->bindParam(':color', $color);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':precio_venta', $precio_venta);
        $stmt->bindParam(':precio_compra', $precio_compra);
        $stmt->bindParam(':marca', $marca);
        $stmt->bindParam(':json_url_img', $json_url_img);
        $stmt->bindParam(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
        
        // ✅ NUEVO: Bind del campo precios_json
        // Si es null, se guarda como NULL en la BD
        $stmt->bindParam(':precios_json', $precios_json, PDO::PARAM_STR);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            echo json_encode([
                'estado' => $row['estado'],
                'mensaje' => $row['mensaje'],
                'articulo_id' => $row['articulo_id'] ?? null
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
    
    $id = $data['id'];
    $categoria_id = $data['categoria_id'];
    $color = $data['color'];
    $corte = $data['corte'];
    $dimension_id = $data['dimension_id'];
    $escala_id = $data['escala_id'];
    $stock = $data['stock'];
    $precio_venta = $data['precio_venta'];
    $precio_compra = $data['precio_compra'];
    $marca = $data['marca'];
    $nombre = $data['nombre'];
    $tipo_id = $data['tipo_id'];
    $json_url_img = $data['json_url_img'];
    $sucursal_id = $data['sucursal_id'];
    
    // ✅ NUEVO: Obtener precios_json
    $precios_json = isset($data['precios_json']) ? $data['precios_json'] : null;

    try {
        // ✅ ACTUALIZADO: Agregar precios_json a la llamada de función
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
            :precios_json
        )";

        $stmt = $conectar->prepare($sql);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':categoria_id', $categoria_id);
        $stmt->bindParam(':tipo_id', $tipo_id);
        $stmt->bindParam(':dimension_id', $dimension_id);
        $stmt->bindParam(':escala_id', $escala_id);
        
        $corte_str = $corte ? 'true' : 'false';
        $stmt->bindParam(':corte', $corte_str);
        
        $stmt->bindParam(':color', $color);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':precio_venta', $precio_venta);
        $stmt->bindParam(':precio_compra', $precio_compra);
        $stmt->bindParam(':marca', $marca);
        $stmt->bindParam(':json_url_img', $json_url_img);
        $stmt->bindParam(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
        
        // ✅ NUEVO: Bind del campo precios_json
        // Si es null, se guarda como NULL en la BD
        $stmt->bindParam(':precios_json', $precios_json, PDO::PARAM_STR);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            echo json_encode([
                'estado' => $row['estado'],
                'mensaje' => $row['mensaje']
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
            'mensaje' => 'Error al procesar la actualización. Consultar con el Administrador de Sistemas.',
            'error' => $e->getMessage(),
            'jsonEntrada' => $data
        ]);
    }
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

    try {
        $sql = "SELECT fn_apertura_caja(:responsable_id, :responsable, :monto)";
        $stmt = $conectar->prepare($sql);

        $stmt->bindParam(':responsable_id', $responsable_id);
        $stmt->bindParam(':responsable', $responsable);
        $stmt->bindParam(':monto', $monto);


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

        $conectar->beginTransaction();


        $sql = "
        INSERT INTO forma_pago
        (nombre,color,icon,monto)
        VALUES
        (:forma_pago,:color,:icono,:monto);
        ";


        $stmt = $conectar->prepare($sql);


        $stmt->bindParam(':forma_pago', $forma_pago);
        $stmt->bindParam(':color', $color);
        $stmt->bindParam(':icono', $icono);
        $stmt->bindParam(':monto', $monto);


        $stmt->execute();


        $conectar->commit();


        echo json_encode(['estado' => true, 'mensaje' => 'Metodo de Pago Registrado :)']);
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

function fnUpdateEmisor($jsDatos)
{

    global $conectar;

    try {
        $conectar->beginTransaction();

        $data = json_decode($jsDatos, true);
        $sql = "
        UPDATE emisor SET 
        ruc = :ruc,
        razon_social = :razon_social,
        nombre_comercial = :nombre_comercial,
        departamento = :departamento,
        provincia = :provincia,
        distrito = :distrito,
        direccion = :direccion,
        ubigeo = :ubigeo,
        usuario_sol = :usuario_sol,
        clave_sol = :clave_sol
        ";

        $stmt = $conectar->prepare($sql);
        $stmt->bindParam(':ruc', $data["ruc"]);
        $stmt->bindParam(':razon_social', $data["razon_social"]);
        $stmt->bindParam(':nombre_comercial', $data["nombre_comercial"]);
        $stmt->bindParam(':departamento', $data["departamento"]);
        $stmt->bindParam(':provincia', $data["provincia"]);
        $stmt->bindParam(':distrito', $data["distrito"]);
        $stmt->bindParam(':direccion', $data["direccion"]);
        $stmt->bindParam(':ubigeo', $data["ubigeo"]);
        $stmt->bindParam(':usuario_sol', $data["usuario_sol"]);
        $stmt->bindParam(':clave_sol', $data["clave_sol"]);

        $stmt->execute();

        $conectar->commit();

        echo json_encode(['estado' => true, 'mensaje' => 'Emisor actualizado correctamente']);
    } catch (Exception $e) {
        $conectar->rollBack();

        echo json_encode(['estado' => false, 'mensaje' => 'Error al procesar la solicitud. Detalles: ' . $e->getMessage()]);
    }
}
