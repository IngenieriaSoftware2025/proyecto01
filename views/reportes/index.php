<?php
// Verificar autenticación y obtener rol
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: /proyecto01/');
    exit;
}
$esAdmin = $_SESSION['rol'] === 'ADMIN';
?>

<meta name="user-role" content="<?= $_SESSION['rol'] ?>">

<!-- Dashboard Principal -->
<div class="row p-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary mb-0">
                <i class="bi bi-graph-up me-2"></i>Dashboard de Reportes
            </h2>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary btn-sm" id="BtnActualizarDashboard">
                    <i class="bi bi-arrow-clockwise me-1"></i>Actualizar
                </button>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-calendar me-1"></i>Período
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item periodo-filter" href="#" data-periodo="7">Últimos 7 días</a></li>
                        <li><a class="dropdown-item periodo-filter" href="#" data-periodo="30">Últimos 30 días</a></li>
                        <li><a class="dropdown-item periodo-filter" href="#" data-periodo="90">Últimos 90 días</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Métricas Principales -->
<div class="row p-3" id="metricas-principales">
    <div class="col-12">
        <h5 class="text-secondary mb-3">Métricas Principales</h5>
    </div>
    
    <!-- Tarjetas de Métricas -->
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="text-muted small">Ventas Hoy</div>
                    <i class="bi bi-cart3 text-primary fs-4"></i>
                </div>
                <h4 class="mb-1" id="total-ventas-hoy">-</h4>
                <small class="text-success" id="ingresos-hoy">Q0.00</small>
            </div>
        </div>
    </div>

    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="text-muted small">Clientes</div>
                    <i class="bi bi-people text-success fs-4"></i>
                </div>
                <h4 class="mb-1" id="total-clientes">-</h4>
                <small class="text-muted">Activos</small>
            </div>
        </div>
    </div>

    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="text-muted small">Inventario</div>
                    <i class="bi bi-boxes text-warning fs-4"></i>
                </div>
                <h4 class="mb-1" id="total-dispositivos">-</h4>
                <small class="text-muted" id="stock-total">Stock: -</small>
            </div>
        </div>
    </div>

    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="text-muted small">Reparaciones</div>
                    <i class="bi bi-tools text-info fs-4"></i>
                </div>
                <h4 class="mb-1" id="reparaciones-pendientes">-</h4>
                <small class="text-muted">Pendientes</small>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-8 col-sm-12 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="text-muted small">Valor Inventario</div>
                    <i class="bi bi-currency-dollar text-dark fs-4"></i>
                </div>
                <h4 class="mb-1 text-success" id="valor-inventario">Q0.00</h4>
                <small class="text-muted">Total en stock</small>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos Principales -->
<div class="row p-3">
    <!-- Gráfico de Ventas -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Tendencia de Ventas</h6>
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-outline-primary active grafico-periodo" data-tipo="ventas_diarias">Diario</button>
                        <button class="btn btn-outline-primary grafico-periodo" data-tipo="ventas_mensuales">Mensual</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="graficoVentas" height="400"></canvas>
            </div>
        </div>
    </div>

    <!-- Gráfico de Reparaciones por Estado -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <h6 class="mb-0">Reparaciones por Estado</h6>
            </div>
            <div class="card-body">
                <canvas id="graficoReparaciones" height="400"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Sección de Reportes Detallados -->
<div class="row p-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-ventas">
                            <i class="bi bi-cart3 me-1"></i>Ventas
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-inventario">
                            <i class="bi bi-boxes me-1"></i>Inventario
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-reparaciones-detalle">
                            <i class="bi bi-tools me-1"></i>Reparaciones
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-clientes">
                            <i class="bi bi-people me-1"></i>Clientes
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Tab Ventas -->
                    <div class="tab-pane fade show active" id="tab-ventas">
                        <div class="row">
                            <div class="col-lg-6">
                                <h6 class="text-muted mb-3">Ranking de Vendedores</h6>
                                <div id="ranking-vendedores" class="loading-placeholder">
                                    <div class="text-center p-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <h6 class="text-muted mb-3">Marcas Más Vendidas</h6>
                                <canvas id="graficoMarcasVendidas" height="300"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Inventario -->
                    <div class="tab-pane fade" id="tab-inventario">
                        <div class="row">
                            <div class="col-lg-6">
                                <h6 class="text-muted mb-3">Stock Bajo (≤5 unidades)</h6>
                                <div id="stock-bajo" class="loading-placeholder">
                                    <div class="text-center p-4">
                                        <div class="spinner-border text-warning" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <h6 class="text-muted mb-3">Valor por Marca</h6>
                                <div id="valor-por-marca" class="loading-placeholder">
                                    <div class="text-center p-4">
                                        <div class="spinner-border text-success" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Reparaciones Detalle -->
                    <div class="tab-pane fade" id="tab-reparaciones-detalle">
                        <div class="row">
                            <div class="col-lg-6">
                                <h6 class="text-muted mb-3">Rendimiento por Técnico</h6>
                                <div id="rendimiento-tecnicos" class="loading-placeholder">
                                    <div class="text-center p-4">
                                        <div class="spinner-border text-info" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <h6 class="text-muted mb-3">Tipos de Servicio Solicitados</h6>
                                <div id="tipos-servicio" class="loading-placeholder">
                                    <div class="text-center p-4">
                                        <div class="spinner-border text-secondary" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Clientes -->
                    <div class="tab-pane fade" id="tab-clientes">
                        <div class="row">
                            <div class="col-lg-6">
                                <h6 class="text-muted mb-3">Top Compradores</h6>
                                <div id="top-compradores" class="loading-placeholder">
                                    <div class="text-center p-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <h6 class="text-muted mb-3">Clientes Frecuentes en Reparaciones</h6>
                                <div id="clientes-reparaciones" class="loading-placeholder">
                                    <div class="text-center p-4">
                                        <div class="spinner-border text-warning" role="status">
                                            <span class="visually-hidden">Cargando...</span>
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

<!-- Estilos personalizados para el dashboard -->
<style>
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.1) !important;
    }

    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        padding: 0.75rem 1rem;
    }

    .nav-tabs .nav-link.active {
        background-color: transparent;
        border-bottom: 2px solid #0d6efd;
        color: #0d6efd;
    }

    .loading-placeholder {
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .metric-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
    }

    .table-sm td {
        padding: 0.5rem;
        vertical-align: middle;
    }

    .badge-metric {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .progress-sm {
        height: 0.5rem;
    }

    .chart-container {
        position: relative;
        height: 400px;
        width: 100%;
    }

    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    .dropdown-menu {
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        border: 1px solid #e9ecef;
    }

    .text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<!-- Script específico para reportes -->
<script src="<?= asset('build/js/reportes/index.js') ?>"></script>