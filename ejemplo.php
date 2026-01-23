<?php
include("cabecera.php");
?>

<div class="container">
    <div class="page-inner">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4><i class="fas fa-check-circle"></i> Sistema de Permisos - Test</h4>
            </div>
            <div class="card-body">
                <h5>Información del Usuario</h5>
                <table class="table table-bordered">
                    <tr>
                        <th>ID:</th>
                        <td><?php echo $_SESSION['id']; ?></td>
                    </tr>
                    <tr>
                        <th>Usuario:</th>
                        <td><?php echo $_SESSION['usuario']; ?></td>
                    </tr>
                    <tr>
                        <th>Nombre Completo:</th>
                        <td><?php echo $nombre . ' ' . $ape_usuario; ?></td>
                    </tr>
                    <tr>
                        <th>Rol:</th>
                        <td><span class="badge badge-primary"><?php echo $rol; ?></span></td>
                    </tr>
                    <tr>
                        <th>ID Rol:</th>
                        <td><?php echo $_SESSION['id_rol'] ?? 'No definido'; ?></td>
                    </tr>
                </table>

                <hr>

                <h5>Estadísticas del MenuManager</h5>
                <?php if ($menuManager !== null): ?>
                <pre><?php print_r($menuManager->getUserStats()); ?></pre>
                
                <h5>Permisos Asignados</h5>
                <div class="row">
                    <?php foreach ($menuManager->getAllUserPermissions() as $permiso): ?>
                    <div class="col-md-3">
                        <span class="badge badge-success mb-1">
                            <i class="fas fa-check"></i> <?php echo $permiso; ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> MenuManager no está inicializado
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
include("pie.php");
?>