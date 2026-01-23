<?php include("cabecera.php") ?>

<div class="container">
    <div class="page-inner">
        <div class="card text-start">
            <div class="card-body">

                <h3 class="fw-bold mb-3"><i class="fas fa-reply-all"></i> Acceso Rápido</h3>
                <div class="card-sub">
                    Accede fácilmente a tu proceso de negocio con el menú de acceso rápido. Solo presiona un botón y serás llevado directamente a la acción.
                </div>
                <?php
                //echo json_encode($_SESSION)
                ?>
                <hr>
                <div class="row">
                    <div class="col-sm-6 col-md-4">
                        <a href="venta_rapida_v2.php">
                            <button class="btn btn-primary btn-lg w-100">
                                <div class="icon-big text-center">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h6>Venta Rapida</h6>
                            </button>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <a href="venta_reserva_corte.php">
                            <button class="btn btn-success btn-lg w-100">
                                <div class="icon-big text-center">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                                <h6>Venta Por Reserva</h6>
                            </button>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <a href="venta_corte_material.php">
                            <button class="btn btn-secondary btn-lg w-100">
                                <div class="icon-big text-center">
                                    <i class="fas fa-luggage-cart"></i>
                                    <i class="fab fa-whatsapp"></i>
                                    <i class="fab fa-telegram-plane"></i>

                                </div>
                                <h6>Atender Reserva</h6>
                            </button>
                        </a>

                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="col-sm-6 col-md-4">
                        <a href="cajaChica.php">
                            <button class="btn btn-danger btn-lg w-100">
                                <div class="icon-big text-center">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <h6>Caja Chica</h6>
                            </button>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <a href="manejoCaja.php">
                            <button class="btn btn-warning btn-lg w-100">
                                <div class="icon-big text-center">
                                    <i class="fas fa-toolbox"></i>
                                </div>
                                <h6>Manejo de Caja</h6>
                            </button>
                        </a>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <a href="pagoCredito.php">
                            <button class="btn btn-black btn-lg w-100">
                                <div class="icon-big text-center">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <h6>Pagos al Credito</h6>
                            </button>
                        </a>

                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-6 col-md-4">
                        <a href="generador_etiquetas.php">
                            <button class="btn btn-info btn-lg w-100">
                                <div class="icon-big text-center">
                                    <i class="fas fa-tags"></i>
                                </div>
                                <h6>Etiquetas de precios</h6>
                            </button>
                        </a>
                    </div>
                </div>

            </div>
        </div>


        <hr>



    </div>
</div>



<?php include("pie.php") ?>