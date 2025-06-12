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
                    <h4 class="text-center mb-2 text-primary">Gestión de Ventas</h4>
                </div>

                <?php if($esAdmin): ?>
                <!-- Formulario para Nueva Venta - Solo ADMIN -->
                <div class="row justify-content-center p-4 shadow-lg mb-4">
                    <form id="FormVentas">
                        <input type="hidden" id="venta_id" name="venta_id">

                        <!-- Información del Cliente -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="cliente_id" class="form-label">Cliente <span class="text-danger">*</span></label>
                                <select class="form-control" id="cliente_id" name="cliente_id">
                                    <option value="">Seleccione un cliente...</option>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones" name="observaciones" rows="2" placeholder="Observaciones de la venta (opcional)"></textarea>
                            </div>
                        </div>

                        <!-- Sección para Agregar Dispositivos -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="text-secondary mb-3">
                                    <i class="bi bi-phone me-2"></i>Dispositivos a Vender
                                </h6>
                            </div>
                            
                            <div class="col-lg-8">
                                <label for="dispositivo_select" class="form-label">Seleccionar Dispositivo</label>
                                <select class="form-control" id="dispositivo_select">
                                    <option value="">Seleccione un dispositivo del inventario...</option>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <label for="cantidad_input" class="form-label">Cantidad</label>
                                <input type="number" class="form-control" id="cantidad_input" value="1" min="1">
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-success w-100" id="BtnAgregarDispositivo">
                                    <i class="bi bi-plus-circle me-1"></i>Agregar
                                </button>
                            </div>
                        </div>

                        <!-- Tabla de Dispositivos Agregados -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered" id="TablaDispositivos">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Dispositivo</th>
                                                <th>N° Serie</th>
                                                <th>Estado</th>
                                                <th>Precio Unit.</th>
                                                <th>Cantidad</th>
                                                <th>Subtotal</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="TbodyDispositivos">
                                            <tr id="FilaSinDispositivos">
                                                <td colspan="7" class="text-center text-muted">
                                                    <i class="bi bi-cart-x me-2"></i>No hay dispositivos agregados
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-warning">
                                                <td colspan="5" class="text-end fw-bold">TOTAL:</td>
                                                <td class="fw-bold" id="TotalVenta">Q0.00</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="row justify-content-center mt-4">
                            <div class="col-auto">
                                <button class="btn btn-success btn-lg" type="submit" id="BtnGuardarVenta">
                                    <i class="bi bi-cart-check me-2"></i>Procesar Venta
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-secondary btn-lg" type="button" id="BtnLimpiarVenta">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Limpiar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <?php else: ?>
                <!-- Mensaje para usuarios no ADMIN -->
                <div class="alert alert-info text-center mb-4">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Acceso limitado:</strong> Solo los administradores pueden crear nuevas ventas.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Sección de Historial de Ventas -->
<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <h3 class="text-center text-primary">Historial de Ventas</h3>
                    </div>
                </div>

                <!-- Botón de búsqueda -->
                <div class="row justify-content-center mb-3">
                    <div class="col-auto">
                        <button class="btn btn-primary" type="button" id="BtnBuscarVentas">
                            <i class="bi bi-search me-2"></i>Buscar Ventas
                        </button>
                    </div>
                </div>

                <!-- Sección de tabla (inicialmente oculta) -->
                <div id="seccion-ventas" class="d-none">
                    <div class="table-responsive p-2">
                        <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableVentas">
                            <!-- La tabla se generará dinámicamente con JavaScript -->
                        </table>
                    </div>
                </div>

                <!-- Mensaje cuando no hay ventas -->
                <div id="mensaje-sin-ventas" class="text-center p-4">
                    <i class="bi bi-cart3 fs-1 text-muted"></i>
                    <h5 class="text-muted mt-2">Presiona "Buscar Ventas" para cargar el historial</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Ver Detalle de Venta -->
<div class="modal fade" id="ModalDetalleVenta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-receipt me-2"></i>Detalle de Venta
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="ContenidoDetalleVenta">
                <!-- Contenido se carga dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<!-- Script de ventas -->
<script src="<?= asset('build/js/ventas/index.js') ?>"></script>