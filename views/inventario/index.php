<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h5 class="text-center mb-2">Sistema de Control de Inventario - Celulares</h5>
                    <h4 class="text-center mb-2 text-primary">Gestión de Inventario</h4>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">
                    <form id="FormInventario">
                        <input type="hidden" id="id" name="id">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="marca_id" class="form-label">Marca y Modelo</label>
                                <select class="form-control" id="marca_id" name="marca_id">
                                    <option value="">Seleccione una marca</option>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label for="numero_serie" class="form-label">Número de Serie</label>
                                <input type="text" class="form-control" id="numero_serie" name="numero_serie" placeholder="Ej: SM123456789">
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-4">
                                <label for="precio_compra" class="form-label">Precio de Compra</label>
                                <input type="number" step="0.01" class="form-control" id="precio_compra" name="precio_compra" placeholder="0.00">
                            </div>
                            <div class="col-lg-4">
                                <label for="precio_venta" class="form-label">Precio de Venta</label>
                                <input type="number" step="0.01" class="form-control" id="precio_venta" name="precio_venta" placeholder="0.00">
                            </div>
                            <div class="col-lg-4">
                                <label for="stock_disponible" class="form-label">Stock</label>
                                <input type="number" class="form-control" id="stock_disponible" name="stock_disponible" value="1" min="0">
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="estado_dispositivo" class="form-label">Estado del Dispositivo</label>
                                <select class="form-control" id="estado_dispositivo" name="estado_dispositivo">
                                    <option value="NUEVO">Nuevo</option>
                                    <option value="USADO">Usado</option>
                                    <option value="REPARADO">Reparado</option>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones" name="observaciones" rows="2" placeholder="Observaciones adicionales (opcional)"></textarea>
                            </div>
                        </div>

                        <div class="row justify-content-center mt-4">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    <i class="bi bi-floppy me-2"></i>Agregar al Inventario
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-warning d-none" type="button" id="BtnModificar">
                                    <i class="bi bi-pencil me-2"></i>Modificar
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-secondary" type="reset" id="BtnLimpiar">
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

<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h3 class="text-center">Dispositivos en Inventario</h3>
                </div>

                <!-- Botón de búsqueda -->
                <div class="row justify-content-center mb-3">
                    <div class="col-auto">
                        <button class="btn btn-primary" type="button" id="BtnBuscarInventario">
                            <i class="bi bi-search me-2"></i>Buscar Inventario
                        </button>
                    </div>
                </div>

                <!-- Sección de tabla (inicialmente oculta) -->
                <div id="seccion-inventario" class="d-none">
                    <div class="table-responsive p-2">
                        <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableInventario">
                        </table>
                    </div>
                </div>

                <!-- Mensaje cuando no hay inventario -->
                <div id="mensaje-sin-inventario" class="text-center p-4">
                    <i class="bi bi-boxes fs-1 text-muted"></i>
                    <h5 class="text-muted mt-2">Presiona "Buscar Inventario" para cargar los datos</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/inventario/index.js') ?>"></script>