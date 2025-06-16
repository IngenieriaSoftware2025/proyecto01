<?php
// Verificar autenticación y permisos de administrador
session_start();
if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'ADMIN') {
    header('Location: /proyecto01/');
    exit;
}
?>

<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h5 class="text-center mb-2">Sistema de Gestión de Celulares</h5>
                    <h4 class="text-center mb-2 text-primary">Configuración del Sistema</h4>
                </div>

                <!-- Información del Sistema -->
                <div class="row mb-4">
                    <div class="col-lg-6">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-gear me-2"></i>Información del Sistema
                            </div>
                            <div class="card-body">
                                <div id="info-sistema">
                                    <div class="d-flex justify-content-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <i class="bi bi-database me-2"></i>Base de Datos
                            </div>
                            <div class="card-body">
                                <div id="info-database">
                                    <div class="d-flex justify-content-center">
                                        <div class="spinner-border text-info" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas Generales -->
                <div class="row mb-4">
                    <div class="col-lg-6">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <i class="bi bi-people me-2"></i>Usuarios del Sistema
                            </div>
                            <div class="card-body">
                                <div id="info-usuarios">
                                    <div class="d-flex justify-content-center">
                                        <div class="spinner-border text-success" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <i class="bi bi-bar-chart me-2"></i>Estadísticas Generales
                            </div>
                            <div class="card-body">
                                <div id="info-estadisticas">
                                    <div class="d-flex justify-content-center">
                                        <div class="spinner-border text-warning" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Herramientas de Administración -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                <i class="bi bi-tools me-2"></i>Herramientas de Administración
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-center">
                                    <div class="col-auto mb-3">
                                        <button class="btn btn-outline-primary" id="BtnRespaldo">
                                            <i class="bi bi-download me-2"></i>Crear Respaldo de BD
                                        </button>
                                    </div>
                                    <div class="col-auto mb-3">
                                        <button class="btn btn-outline-success" id="BtnLimpiarLogs">
                                            <i class="bi bi-trash me-2"></i>Limpiar Logs del Sistema
                                        </button>
                                    </div>
                                    <div class="col-auto mb-3">
                                        <button class="btn btn-outline-info" id="BtnActualizarStats">
                                            <i class="bi bi-arrow-clockwise me-2"></i>Actualizar Estadísticas
                                        </button>
                                    </div>
                                </div>

                                <!-- Información de último respaldo -->
                                <div id="info-respaldo" class="mt-3 d-none">
                                    <div class="alert alert-success" role="alert">
                                        <i class="bi bi-check-circle me-2"></i>
                                        <strong>Último respaldo:</strong> <span id="ultimo-respaldo"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botón para recargar configuración -->
                <div class="row justify-content-center mt-4">
                    <div class="col-auto">
                        <button class="btn btn-primary" id="BtnCargarConfiguracion">
                            <i class="bi bi-arrow-clockwise me-2"></i>Recargar Configuración
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.custom-card {
    border-radius: 10px;
    border: 1px solid #007bff;
}

.info-label {
    font-weight: 600;
    color: #495057;
}

.info-value {
    color: #28a745;
    font-weight: 500;
}

.card-header {
    font-weight: 600;
}

.btn-outline-primary:hover,
.btn-outline-success:hover,
.btn-outline-info:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>

<script src="<?= asset('build/js/configuracion/index.js') ?>"></script>