<?php
// Verificar autenticación y obtener rol
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: /proyecto01/');
    exit;
}
$esAdmin = $_SESSION['rol'] === 'ADMIN';
?>

<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h5 class="text-center mb-2">Sistema de Gestión de Celulares</h5>
                    <h4 class="text-center mb-2 text-primary">Gestión de Reparaciones</h4>
                </div>

                <!-- Formulario para Nueva Reparación -->
                <div class="row justify-content-center p-4 shadow-lg mb-4">
                    <form id="FormReparaciones">
                        <input type="hidden" id="reparacion_id" name="reparacion_id">

                        <!-- Información del Cliente y Dispositivo -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="cliente_id" class="form-label">Cliente <span class="text-danger">*</span></label>
                                <select class="form-control" id="cliente_id" name="cliente_id">
                                    <option value="">Seleccione un cliente...</option>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label for="dispositivo_marca" class="form-label">Marca <span class="text-danger">*</span></label>
                                <select class="form-control" id="dispositivo_marca" name="dispositivo_marca">
                                    <option value="">Seleccione una marca...</option>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label for="dispositivo_modelo" class="form-label">Modelo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="dispositivo_modelo" name="dispositivo_modelo" placeholder="Ej: Galaxy S23, iPhone 15">
                            </div>
                        </div>

                        <!-- Información del Dispositivo -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="dispositivo_serie" class="form-label">Número de Serie</label>
                                <input type="text" class="form-control" id="dispositivo_serie" name="dispositivo_serie" placeholder="Número de serie del dispositivo">
                            </div>
                            <div class="col-lg-6">
                                <label for="dispositivo_imei" class="form-label">IMEI</label>
                                <input type="text" class="form-control" id="dispositivo_imei" name="dispositivo_imei" placeholder="IMEI del dispositivo" maxlength="15">
                            </div>
                        </div>

                        <!-- Problema y Tipo de Servicio -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-8">
                                <label for="problema_reportado" class="form-label">Problema Reportado <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="problema_reportado" name="problema_reportado" rows="3" placeholder="Describa detalladamente el problema reportado por el cliente"></textarea>
                            </div>
                            <div class="col-lg-4">
                                <label for="tipo_servicio_id" class="form-label">Tipo de Servicio</label>
                                <select class="form-control" id="tipo_servicio_id" name="tipo_servicio_id">
                                    <option value="">Seleccione tipo de servicio...</option>
                                </select>
                                <small class="form-text text-muted">El precio se mostrará como referencia</small>
                            </div>
                        </div>

                        <!-- Costos -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-4">
                                <label for="presupuesto_inicial" class="form-label">Presupuesto Inicial</label>
                                <input type="number" step="0.01" class="form-control" id="presupuesto_inicial" name="presupuesto_inicial" placeholder="0.00">
                                <small class="form-text text-muted">Presupuesto estimado inicial</small>
                            </div>
                            <div class="col-lg-4">
                                <label for="anticipo" class="form-label">Anticipo</label>
                                <input type="number" step="0.01" class="form-control" id="anticipo" name="anticipo" placeholder="0.00">
                                <small class="form-text text-muted">Anticipo recibido del cliente</small>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label">Precio Base Servicio</label>
                                <div class="form-control bg-light" id="precio_base_display">Q0.00</div>
                                <small class="form-text text-muted">Precio base del tipo de servicio</small>
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-12">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones" name="observaciones" rows="2" placeholder="Observaciones adicionales sobre la recepción del dispositivo"></textarea>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="row justify-content-center mt-4">
                            <div class="col-auto">
                                <button class="btn btn-success btn-lg" type="submit" id="BtnGuardarReparacion">
                                    <i class="bi bi-tools me-2"></i>Recibir Reparación
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-secondary btn-lg" type="button" id="BtnLimpiarReparacion">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Limpiar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sección de Historial de Reparaciones -->
<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <h3 class="text-center text-primary">Historial de Reparaciones</h3>
                    </div>
                </div>

                <!-- Botón de búsqueda -->
                <div class="row justify-content-center mb-3">
                    <div class="col-auto">
                        <button class="btn btn-primary" type="button" id="BtnBuscarReparaciones">
                            <i class="bi bi-search me-2"></i>Buscar Reparaciones
                        </button>
                    </div>
                </div>

                <!-- Sección de tabla (inicialmente oculta) -->
                <div id="seccion-reparaciones" class="d-none">
                    <div class="table-responsive p-2">
                        <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableReparaciones">
                            <!-- La tabla se generará dinámicamente con JavaScript -->
                        </table>
                    </div>
                </div>

                <!-- Mensaje cuando no hay reparaciones -->
                <div id="mensaje-sin-reparaciones" class="text-center p-4">
                    <i class="bi bi-tools fs-1 text-muted"></i>
                    <h5 class="text-muted mt-2">Presiona "Buscar Reparaciones" para cargar el historial</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Ver Detalle de Reparación -->
<div class="modal fade" id="ModalDetalleReparacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-gear me-2"></i>Detalle de Reparación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="ContenidoDetalleReparacion">
                <!-- Contenido se carga dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Cambiar Estado -->
<div class="modal fade" id="ModalCambiarEstado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="bi bi-arrow-repeat me-2"></i>Cambiar Estado
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="FormCambiarEstado">
                    <input type="hidden" id="estado_reparacion_id" name="reparacion_id">

                    <div class="mb-3">
                        <label for="nuevo_estado" class="form-label">Nuevo Estado</label>
                        <select class="form-control" id="nuevo_estado" name="nuevo_estado">
                            <option value="">Seleccione un estado...</option>
                            <option value="RECIBIDO">Recibido</option>
                            <option value="EN_DIAGNOSTICO">En Diagnóstico</option>
                            <option value="DIAGNOSTICADO">Diagnosticado</option>
                            <option value="EN_REPARACION">En Reparación</option>
                            <option value="REPARADO">Reparado</option>
                            <option value="ENTREGADO">Entregado</option>
                            <option value="CANCELADO">Cancelado</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="observaciones_estado" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones_estado" name="observaciones" rows="3" placeholder="Observaciones sobre el cambio de estado"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="BtnConfirmarCambioEstado">
                    <i class="bi bi-check-circle me-2"></i>Cambiar Estado
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Asignar Técnico -->
<div class="modal fade" id="ModalAsignarTecnico" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="bi bi-person-gear me-2"></i>Asignar Técnico
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="FormAsignarTecnico">
                    <input type="hidden" id="tecnico_reparacion_id" name="reparacion_id">

                    <div class="mb-3">
                        <label for="tecnico_id" class="form-label">Seleccionar Técnico</label>
                        <select class="form-control" id="tecnico_id" name="tecnico_id">
                            <option value="">Seleccione un técnico...</option>
                        </select>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Nota:</strong> Solo los administradores pueden asignar técnicos.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-info" id="BtnConfirmarAsignacion">
                    <i class="bi bi-check-circle me-2"></i>Asignar Técnico
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Estilos personalizados -->
<style>
    .custom-card {
        border-radius: 10px;
        border: 1px solid #007bff;
    }

    .table td {
        vertical-align: middle;
    }

    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }

    .estado-badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }

    .btn-estado {
        transition: all 0.2s ease;
    }

    .btn-estado:hover {
        transform: scale(1.05);
    }

    .timeline-item {
        border-left: 3px solid #007bff;
        padding-left: 15px;
        margin-bottom: 15px;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .precio-referencia {
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        border: 1px dashed #6c757d;
    }

    #dispositivo_imei {
        font-family: 'Courier New', monospace;
    }
</style>

<!-- Script de reparaciones -->
<script src="<?= asset('build/js/reparaciones/index.js') ?>"></script>