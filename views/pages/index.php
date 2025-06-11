<?php
// Verificar autenticación
if (!isset($_SESSION['login'])) {
    header('Location: /proyecto01/');
    exit;
}
?>

<div class="row mb-4">
    <div class="col text-center">
        <h1 class="display-4">
            ¡Bienvenido, <?= htmlspecialchars($_SESSION['user']) ?>!
        </h1>
        <p class="lead">
            Sistema de Gestión de Celulares - Rol: 
            <span class="badge bg-<?= $_SESSION['rol'] === 'ADMIN' ? 'danger' : 'primary' ?> fs-6">
                <?= $_SESSION['rol'] ?>
            </span>
        </p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="row g-4">
            
            <!-- Card Clientes -->
            <div class="col-md-4">
                <div class="card border-primary shadow h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-people display-1 text-primary mb-3"></i>
                        <h5 class="card-title">Gestión de Clientes</h5>
                        <p class="card-text">Administre la información de sus clientes</p>
                        <a href="/proyecto01/clientes" class="btn btn-primary">
                            <i class="bi bi-person-add me-2"></i>Ir a Clientes
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card Marcas -->
            <div class="col-md-4">
                <div class="card border-success shadow h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-phone display-1 text-success mb-3"></i>
                        <h5 class="card-title">Gestión de Marcas</h5>
                        <p class="card-text">Catalogue las marcas y modelos disponibles</p>
                        <a href="/proyecto01/marcas" class="btn btn-success">
                            <i class="bi bi-phone me-2"></i>Ir a Marcas
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card Inventario -->
            <div class="col-md-4">
                <div class="card border-warning shadow h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-boxes display-1 text-warning mb-3"></i>
                        <h5 class="card-title">Control de Inventario</h5>
                        <p class="card-text">Controle el stock de dispositivos</p>
                        <a href="/proyecto01/inventario" class="btn btn-warning">
                            <i class="bi bi-boxes me-2"></i>Ir a Inventario
                        </a>
                    </div>
                </div>
            </div>

            <?php if($_SESSION['rol'] === 'ADMIN'): ?>
            <!-- Cards adicionales solo para administradores -->
            <div class="col-md-6">
                <div class="card border-danger shadow h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-gear-fill display-1 text-danger mb-3"></i>
                        <h5 class="card-title">Panel de Administración</h5>
                        <p class="card-text">Configuraciones y gestión avanzada del sistema</p>
                        <a href="/proyecto01/admin" class="btn btn-danger">
                            <i class="bi bi-gear me-2"></i>Panel Admin
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-info shadow h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-graph-up display-1 text-info mb-3"></i>
                        <h5 class="card-title">Reportes y Estadísticas</h5>
                        <p class="card-text">Visualice reportes de ventas e inventario</p>
                        <a href="/proyecto01/reportes" class="btn btn-info">
                            <i class="bi bi-file-earmark-text me-2"></i>Ver Reportes
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<div class="row justify-content-center mt-5">
    <div class="col-lg-8">
        <div class="alert alert-info text-center">
            <h6 class="alert-heading">
                <i class="bi bi-info-circle me-2"></i>Información del Sistema
            </h6>
            <p class="mb-0">
                Última actividad: <?= date('d/m/Y H:i:s') ?><br>
                Sesión válida hasta: <?= date('H:i:s', $_SESSION['tiempo_limite']) ?>
            </p>
        </div>
    </div>
</div>

<script src="build/js/inicio.js"></script>