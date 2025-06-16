<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: /proyecto01/');
    exit;
}
$esAdmin = $_SESSION['rol'] === 'ADMIN';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Reportes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container-fluid">
    <div class="row justify-content-center p-3">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h2 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Dashboard de Reportes - Sistema de Gestión de Celulares
                    </h2>
                </div>
                <div class="card-body p-4">
     
                    <div class="row mb-4">
                        <div class="col-12 text-center">
                            <button type="button" class="btn btn-success btn-lg" id="BtnActualizarDashboard">
                                <i class="bi bi-arrow-clockwise me-2"></i>
                                Actualizar Dashboard
                            </button>
                            <small class="d-block text-muted mt-2">
                                Última actualización: <span id="ultimaActualizacion">--</span>
                            </small>
                        </div>
                    </div>

                    <div class="row mb-5">
                        <div class="col-12">
                            <h4 class="text-center mb-4 text-primary">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Métricas Principales
                            </h4>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card border-left-primary shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Ventas Hoy
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-ventas-hoy">
                                                0
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="bi bi-cart-check fs-2 text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card border-left-success shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Ingresos Hoy
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="ingresos-hoy">
                                                Q0.00
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <span class="fs-2 text-success fw-bold">Q</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card border-left-info shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Clientes Activos
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-clientes">
                                                0
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="bi bi-people fs-2 text-info"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card border-left-warning shadow h-100">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Reparaciones Pendientes
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="reparaciones-pendientes">
                                                0
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="bi bi-tools fs-2 text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h4 class="text-center mb-4 text-primary">
                                <i class="bi bi-bar-chart me-2"></i>
                                Análisis Visual
                            </h4>
                        </div>

                        <div class="col-lg-6 mb-4">
                            <div class="card shadow">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="bi bi-bar-chart-line me-2"></i>
                                        Ventas por Mes
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container" style="position: relative; height: 300px;">
                                        <canvas id="graficoVentasMes"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 mb-4">
                            <div class="card shadow">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="bi bi-pie-chart me-2"></i>
                                        Estado de Reparaciones
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container" style="position: relative; height: 300px;">
                                        <canvas id="graficoEstadoReparaciones"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 mb-4">
                            <div class="card shadow">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="bi bi-bar-chart me-2"></i>
                                        Top 5 Marcas Más Vendidas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container" style="position: relative; height: 300px;">
                                        <canvas id="graficoMarcasVendidas"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 mb-4">
                            <div class="card shadow">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0">
                                        <i class="bi bi-pie-chart-fill me-2"></i>
                                        Inventario por Estado
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container" style="position: relative; height: 300px;">
                                        <canvas id="graficoInventarioEstado"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Resumen de Inventario
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-6 mb-3">
                                            <div class="h4 text-primary" id="total-dispositivos">0</div>
                                            <div class="small text-muted">Total Dispositivos</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="h4 text-info" id="stock-total">0</div>
                                            <div class="small text-muted">Stock Total</div>
                                        </div>
                                        <div class="col-12">
                                            <hr>
                                            <div class="h5 text-success" id="valor-inventario">Q0.00</div>
                                            <div class="small text-muted">Valor Total del Inventario</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 mb-4">
                            <div class="card shadow">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="mb-0">
                                        <i class="bi bi-clock me-2"></i>
                                        Estado del Sistema
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center">
                                        <div class="mb-3">
                                            <span class="badge bg-success fs-6">Sistema Operativo</span>
                                        </div>
                                        <div class="small text-muted">
                                            <p><strong>Usuario:</strong> <?php echo $_SESSION['nombre']; ?></p>
                                            <p><strong>Rol:</strong> <?php echo $_SESSION['rol']; ?></p>
                                            <p><strong>Fecha:</strong> <span id="fechaActual"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-left-primary {
        border-left: 4px solid #007bff !important;
    }
    .border-left-success {
        border-left: 4px solid #28a745 !important;
    }
    .border-left-info {
        border-left: 4px solid #17a2b8 !important;
    }
    .border-left-warning {
        border-left: 4px solid #ffc107 !important;
    }
    
    .card {
        transition: transform 0.2s ease-in-out;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .chart-container {
        position: relative;
        overflow: hidden;
    }
    
    canvas {
        max-width: 100%;
        height: auto !important;
    }
    
    .text-xs {
        font-size: 0.75rem;
    }
    
    .loading-placeholder {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 200px;
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    
</style>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('fechaActual').textContent = new Date().toLocaleDateString('es-GT');
    
    function actualizarTimestamp() {
        document.getElementById('ultimaActualizacion').textContent = new Date().toLocaleString('es-GT');
    }
    actualizarTimestamp();
</script>


<script src="<?= asset('build/js/reportes/index.js') ?>"></script>

</body>
</html>