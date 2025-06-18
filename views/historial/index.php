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
                    <h4 class="text-center mb-2 text-primary">Historial de Actividades del Sistema</h4>
                </div>

                <!-- Filtros de Búsqueda -->
                <div class="row justify-content-center p-4 shadow-lg mb-4">
                    <form id="FormFiltrosHistorial">
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-3">
                                <label for="filtro_fecha_inicio" class="form-label">Desde Fecha</label>
                                <input type="date" class="form-control" id="filtro_fecha_inicio" name="filtro_fecha_inicio">
                            </div>
                            <div class="col-lg-3">
                                <label for="filtro_usuario" class="form-label">Usuario</label>
                                <select class="form-control" id="filtro_usuario" name="filtro_usuario">
                                    <option value="">Todos los usuarios</option>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label for="filtro_modulo" class="form-label">Módulo</label>
                                <select class="form-control" id="filtro_modulo" name="filtro_modulo">
                                    <option value="">Todos los módulos</option>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label for="filtro_limite" class="form-label">Mostrar</label>
                                <select class="form-control" id="filtro_limite" name="filtro_limite">
                                    <option value="100">100 registros</option>
                                    <option value="250">250 registros</option>
                                    <option value="500">500 registros</option>
                                    <option value="1000">1000 registros</option>
                                </select>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="row justify-content-center mt-4">
                            <div class="col-auto">
                                <button class="btn btn-primary btn-lg" type="button" id="BtnBuscarHistorial">
                                    <i class="bi bi-search me-2"></i>Buscar Actividades
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-secondary btn-lg" type="reset" id="BtnLimpiarFiltros">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Limpiar Filtros
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sección de Historial de Actividades -->
<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <h3 class="text-center text-primary">Registro de Actividades</h3>
                    </div>
                </div>

                <!-- Sección de tabla (inicialmente oculta) -->
                <div id="seccion-historial" class="d-none">
                    <div class="table-responsive p-2">
                        <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableHistorial">
                            <!-- La tabla se generará dinámicamente con JavaScript -->
                        </table>
                    </div>
                </div>

                <!-- Mensaje cuando no hay historial -->
                <div id="mensaje-sin-historial" class="text-center p-4">
                    <i class="bi bi-clock-history fs-1 text-muted"></i>
                    <h5 class="text-muted mt-2">Configure los filtros y presiona "Buscar Actividades" para cargar el historial</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Ver Detalle de Actividad -->
<div class="modal fade" id="ModalDetalleActividad" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-info-circle me-2"></i>Detalle de Actividad
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="ContenidoDetalleActividad">
                <!-- Contenido se carga dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Script del historial -->
<script src="<?= asset('build/js/historial/index.js') ?>"></script>