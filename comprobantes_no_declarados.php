<?php
include("cabecera.php");
$sucursal_id = isset($_SESSION['sucursal_id']) ? $_SESSION['sucursal_id'] : null;
?>

<div
    class="container">
    <div class="page-inner">
        <div class="card text-start">

            <div class="card-body">
                <h4 class="card-title"> <i class="fab fa-staylinked"></i> Ventas Pagadas <span style="color: red;"> NO Declaradas a SUNAT </span></h4>
                <div class="card-sub">
                    Estas ventas no han sidos declarados a <strong>SUNAT.</strong>
                </div>

                <div class="tablita-responsive">
                    <div class="table-responsive">
                        <table id="tabla_boletas" class="dataTable table table-striped table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Marcado como</th>
                                    <th>N° Documento</th>
                                    <th>CLIENTE</th>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach (listarVentasNoDeclaradas($sucursal_id) as $datos) {
                                    $datosJSON = ($datos);
                                    $js_detalle = ($datos["js_detalle_venta"]);
                                    $datosEmisorJSON = (fnListarEmisor($sucursal_id)[0]);

                                    $datax_completo = array("datos_query" => $datosJSON, "js_detalle" => $js_detalle, "emisor" => $datosEmisorJSON);

                                    $datosFunctionEvio = json_encode($datax_completo);
                                ?>
                                    <tr>
                                        <td><?php echo $datos["venta_id"] ?></td>
                                        <td><?php echo $datos["tipo_comprobante"] ?></td>
                                        <td><?php echo $datos["ca_cliente_numero_documento_sunat"] ?></td>
                                        <td><?php echo $datos["ca_cliente_cliente_sunat"] ?></td>
                                        <td><?php echo "S/ " . number_format($datos["monto_venta_final"], 2) ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($datos["fecha"])) ?></td>
                                        
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <br>
                <!-- 
                <a
                    name=""
                    id=""
                    onclick='fn_enviar_sunat(<?php echo $datosEmisorJSON ?>)'
                    class="btn btn-secondary btn-round btn-md mx-1"
                    role="button">
                    <i class="fas fa-paper-plane"></i> Enivar a SUNAT
                </a>
                -->

            </div>
        </div>
    </div>
</div>


<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $(".dataTable").DataTable({
            language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sPrevious": "Anterior",
                    "sNext": "Siguiente",
                    "sLast": "Último"
                }
            }
        });
    });
</script>

<?php
include("pie.php");
?>