<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h5 class="text-center mb-2">Gestión de Aplicaciones</h5>
                    <h4 class="text-center mb-2 text-primary">Registro de Nuevas Aplicaciones</h4>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">
                    <form id="formAplicacion" name="formAplicacion">
                        <input type="hidden" id="app_id" name="app_id">

                        <!-- Fila de Nombres de Aplicación -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-4">
                                <label for="app_nombre_largo" class="form-label">Nombre Largo</label>
                                <input type="text" class="form-control" id="app_nombre_largo" name="app_nombre_largo" placeholder="Nombre completo de la aplicación" maxlength="250">
                            </div>
                            <div class="col-lg-4">
                                <label for="app_nombre_medium" class="form-label">Nombre Medium</label>
                                <input type="text" class="form-control" id="app_nombre_medium" name="app_nombre_medium" placeholder="Nombre mediano" maxlength="150">
                            </div>
                            <div class="col-lg-4">
                                <label for="app_nombre_corto" class="form-label">Nombre Corto</label>
                                <input type="text" class="form-control" id="app_nombre_corto" name="app_nombre_corto" placeholder="Nombre corto/código" maxlength="50">
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="row justify-content-center mt-4">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardarApp">
                                    <i class="bi bi-floppy me-2"></i>Guardar Aplicación
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-warning d-none" type="button" id="BtnModificarApp">
                                    <i class="bi bi-pencil me-2"></i>Modificar
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-secondary" type="reset" id="BtnLimpiarApp">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Limpiar
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-primary" type="button" id="BtnBuscarApps">
                                    <i class="bi bi-search me-2"></i>Buscar Aplicaciones
                                </button>
                            </div>
                        </div>

                        <!-- Enlace a otras secciones -->
                        <div class="row justify-content-center mt-4">
                            <div class="col-auto">
                                <p class="text-center text-muted">
                                    <a href="/proyecto01/registro" class="text-primary fw-bold">Gestionar Usuarios</a> | 
                                    <a href="/proyecto01/permisos" class="text-primary fw-bold">Gestionar Permisos</a> |
                                    <a href="/proyecto01/login" class="text-primary fw-bold">Cerrar Sesión</a>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sección de Aplicaciones Registradas (oculta por defecto) -->
<div class="row justify-content-center p-3 d-none" id="seccion_aplicaciones">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <h3 class="text-center text-primary">Aplicaciones Registradas</h3>
                    </div>
                </div>

                <!-- Contenedor de tabla -->
                <div id="contenedor_tabla_apps">
                    <div class="table-responsive p-2">
                        <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableAplicaciones">
                            <!-- La tabla se generará dinámicamente con JavaScript -->
                        </table>
                    </div>
                </div>

                <!-- Mensaje cuando no hay datos -->
                <div id="mensaje_sin_apps" class="text-center p-4 d-none">
                    <i class="bi bi-app" style="font-size: 3rem; color: #6c757d;"></i>
                    <p class="text-muted mt-2">No se encontraron aplicaciones registradas</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos mínimos -->
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

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<!-- Script de aplicaciones -->
<script src="<?= asset('build/js/aplicaciones/index.js') ?>"></script>