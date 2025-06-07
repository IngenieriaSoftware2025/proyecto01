<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h5 class="text-center mb-2">Gestión de Permisos</h5>
                    <h4 class="text-center mb-2 text-primary">Registro de Nuevos Permisos</h4>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">
                    <form id="formPermiso" name="formPermiso">
                        <input type="hidden" id="permiso_id" name="permiso_id">

                        <!-- Fila de Aplicación y Nombre -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="permiso_app_id" class="form-label">Aplicación</label>
                                <select class="form-control" id="permiso_app_id" name="permiso_app_id">
                                    <option value="">Seleccione una aplicación...</option>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label for="permiso_nombre" class="form-label">Nombre del Permiso</label>
                                <input type="text" class="form-control" id="permiso_nombre" name="permiso_nombre" placeholder="Ej: Crear Usuarios" maxlength="150">
                            </div>
                        </div>

                        <!-- Fila de Clave y Descripción -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="permiso_clave" class="form-label">Clave del Permiso</label>
                                <input type="text" class="form-control" id="permiso_clave" name="permiso_clave" placeholder="Ej: CREAR_USUARIOS" maxlength="250">
                                <div class="form-text">Use MAYÚSCULAS y guiones bajos</div>
                            </div>
                            <div class="col-lg-6">
                                <label for="permiso_desc" class="form-label">Descripción</label>
                                <textarea class="form-control" id="permiso_desc" name="permiso_desc" placeholder="Descripción detallada del permiso" rows="3" maxlength="250"></textarea>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="row justify-content-center mt-4">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardarPermiso">
                                    <i class="bi bi-floppy me-2"></i>Guardar Permiso
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-warning d-none" type="button" id="BtnModificarPermiso">
                                    <i class="bi bi-pencil me-2"></i>Modificar
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-secondary" type="reset" id="BtnLimpiarPermiso">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Limpiar
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-primary" type="button" id="BtnBuscarPermisos">
                                    <i class="bi bi-search me-2"></i>Buscar Permisos
                                </button>
                            </div>
                        </div>

                        <!-- Enlace a otras secciones -->
                        <div class="row justify-content-center mt-4">
                            <div class="col-auto">
                                <p class="text-center text-muted">
                                    <a href="/proyecto01/registro" class="text-primary fw-bold">Gestionar Usuarios</a> | 
                                    <a href="/proyecto01/aplicaciones" class="text-primary fw-bold">Gestionar Aplicaciones</a> |
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

<!-- Sección de Permisos Registrados (oculta por defecto) -->
<div class="row justify-content-center p-3 d-none" id="seccion_permisos">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <h3 class="text-center text-primary">Permisos Registrados</h3>
                    </div>
                </div>

                <!-- Contenedor de tabla -->
                <div id="contenedor_tabla_permisos">
                    <div class="table-responsive p-2">
                        <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TablePermisos">
                            <!-- La tabla se generará dinámicamente con JavaScript -->
                        </table>
                    </div>
                </div>

                <!-- Mensaje cuando no hay datos -->
                <div id="mensaje_sin_permisos" class="text-center p-4 d-none">
                    <i class="bi bi-shield-check" style="font-size: 3rem; color: #6c757d;"></i>
                    <p class="text-muted mt-2">No se encontraron permisos registrados</p>
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

<!-- Script de permisos -->
<script src="<?= asset('build/js/permisos/index.js') ?>"></script>