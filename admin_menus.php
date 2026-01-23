<?php
/**
 * admin_menus.php
 * Panel de administración de menús y permisos
 */

include('cabecera.php');

// Verificar que sea administrador
$menuManager->requirePageAccess('admin_menus.php');

?>

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Administración de Menús y Permisos</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="index.php"><i class="icon-home"></i></a>
                </li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item">Administración</li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item">Menús y Permisos</li>
            </ul>
        </div>

        <!-- Tabs -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-pills nav-secondary" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pills-roles-tab" data-bs-toggle="pill" href="#pills-roles" role="tab">
                                    <i class="fas fa-users"></i> Roles
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-permisos-tab" data-bs-toggle="pill" href="#pills-permisos" role="tab">
                                    <i class="fas fa-key"></i> Permisos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-menus-tab" data-bs-toggle="pill" href="#pills-menus" role="tab">
                                    <i class="fas fa-bars"></i> Menús
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-items-tab" data-bs-toggle="pill" href="#pills-items" role="tab">
                                    <i class="fas fa-list"></i> Items de Menú
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-quick-tab" data-bs-toggle="pill" href="#pills-quick" role="tab">
                                    <i class="fas fa-bolt"></i> Accesos Rápidos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-paginas-tab" data-bs-toggle="pill" href="#pills-paginas" role="tab">
                                    <i class="fas fa-file"></i> Páginas
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content mt-2 mb-3" id="pills-tabContent">
                            <!-- Tab Roles -->
                            <div class="tab-pane fade show active" id="pills-roles" role="tabpanel">
                                <?php include('admin/roles_tab.php'); ?>
                            </div>

                            <!-- Tab Permisos -->
                            <div class="tab-pane fade" id="pills-permisos" role="tabpanel">
                                <?php include('admin/permisos_tab.php'); ?>
                            </div>

                            <!-- Tab Menús -->
                            <div class="tab-pane fade" id="pills-menus" role="tabpanel">
                                <?php include('admin/menus_tab.php'); ?>
                            </div>

                            <!-- Tab Items -->
                            <div class="tab-pane fade" id="pills-items" role="tabpanel">
                                <?php include('admin/items_tab.php'); ?>
                            </div>

                            <!-- Tab Accesos Rápidos -->
                            <div class="tab-pane fade" id="pills-quick" role="tabpanel">
                                <?php include('admin/quick_access_tab.php'); ?>
                            </div>

                            <!-- Tab Páginas -->
                            <div class="tab-pane fade" id="pills-paginas" role="tabpanel">
                                <?php include('admin/paginas_tab.php'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('pie.php'); ?>