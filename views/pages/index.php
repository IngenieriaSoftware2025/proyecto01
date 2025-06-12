<?php
// Verificar autenticación
if (!isset($_SESSION['login'])) {
    header('Location: /proyecto01/');
    exit;
}
?>

<div class="container-fluid py-4">
    <!-- Encabezado -->
    <div class="text-center mb-5">
        <h2>Bienvenido, <?= htmlspecialchars($_SESSION['user']) ?></h2>
        <p class="text-muted">Sistema de Gestión de Celulares</p>
        <span class="badge <?= $_SESSION['rol'] === 'ADMIN' ? 'bg-dark' : 'bg-secondary' ?>">
            <?= $_SESSION['rol'] ?>
        </span>
    </div>

    <!-- Menú Principal -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <!-- Opciones básicas -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center p-4">
                            <h5>Clientes</h5>
                            <p class="text-muted small">Gestionar clientes</p>
                            <a href="/proyecto01/clientes" class="btn btn-outline-primary btn-sm">
                                Acceder
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center p-4">
                            <h5>Marcas</h5>
                            <p class="text-muted small">Catálogo de marcas</p>
                            <a href="/proyecto01/marcas" class="btn btn-outline-success btn-sm">
                                Acceder
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center p-4">
                            <h5>Inventario</h5>
                            <p class="text-muted small">Control de stock</p>
                            <a href="/proyecto01/inventario" class="btn btn-outline-warning btn-sm">
                                Acceder
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <?php if($_SESSION['rol'] === 'ADMIN'): ?>
            <!-- Opciones de administrador -->
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card border-0 bg-light">
                        <div class="card-body text-center p-4">
                            <h5>Administración</h5>
                            <p class="text-muted small">Panel de control</p>
                            <a href="/proyecto01/admin" class="btn btn-dark btn-sm">
                                Acceder
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 bg-light">
                        <div class="card-body text-center p-4">
                            <h5>Reportes</h5>
                            <p class="text-muted small">Estadísticas del sistema</p>
                            <a href="/proyecto01/reportes" class="btn btn-secondary btn-sm">
                                Acceder
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Información del sistema -->
    <div class="row justify-content-center mt-5">
        <div class="col-lg-6">
            <div class="card border-0 bg-light">
                <div class="card-body text-center py-3">
                    <small class="text-muted">
                        Última actividad: <?= date('d/m/Y H:i:s') ?><br>
                        Sesión válida hasta: <?= date('H:i:s', $_SESSION['tiempo_limite']) ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="build/js/inicio.js"></script>