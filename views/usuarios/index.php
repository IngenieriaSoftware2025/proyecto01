<?php
// Verificar autenticación y permisos de administrador
session_start();
if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'ADMIN') {
    header('Location: /proyecto01/');
    exit;
}
?>

<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h5 class="text-center mb-2">Sistema de Gestión de Celulares</h5>
                    <h4 class="text-center mb-2 text-primary">Gestión de Usuarios</h4>
                </div>

                <!-- Formulario para Usuarios -->
                <div class="row justify-content-center p-4 shadow-lg mb-4">
                    <form id="FormUsuarios">
                        <input type="hidden" id="usu_id" name="usu_id">

                        <div class="row mb-3 justify-content-center">
                        <div class="col-lg-6">
                                <label for="usu_codigo" class="form-label">Código de Usuario <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="usu_codigo" name="usu_codigo"
                                    placeholder="Ingrese código de 6 dígitos" min="100000" max="999999" required>
                                <small class="form-text text-muted">Código único de 6 dígitos para login</small>
                            </div>
                            <div class="col-lg-6">
                                <label for="usu_nombre" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="usu_nombre" name="usu_nombre"
                                    placeholder="Ingrese nombre completo del usuario" required>
                            </div>
                            <div class="col-lg-6">
                                <label for="usu_password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="usu_password" name="usu_password" placeholder="Ingrese contraseña" required>
                                <small class="form-text text-muted">Se generará código automáticamente</small>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="usu_rol" class="form-label">Rol <span class="text-danger">*</span></label>
                                <select class="form-control" id="usu_rol" name="usu_rol" required>
                                    <option value="">Seleccione un rol...</option>
                                    <option value="ADMIN">Administrador</option>
                                    <option value="USER">Usuario</option>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label for="usu_situacion" class="form-label">Estado</label>
                                <select class="form-control" id="usu_situacion" name="usu_situacion">
                                    <option value="1">Activo</option>
                                    <option value="2">Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="row justify-content-center mt-4">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    <i class="bi bi-floppy me-2"></i>Guardar Usuario
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

<!-- Sección de Tabla de Usuarios -->
<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h3 class="text-center">Usuarios Registrados</h3>
                </div>

                <!-- Botón de búsqueda -->
                <div class="row justify-content-center mb-3">
                    <div class="col-auto">
                        <button class="btn btn-primary" type="button" id="BtnBuscarUsuarios">
                            <i class="bi bi-search me-2"></i>Buscar Usuarios
                        </button>
                    </div>
                </div>

                <!-- Sección de tabla -->
                <div id="seccion-usuarios" class="d-none">
                    <div class="table-responsive p-2">
                        <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableUsuarios">
                        </table>
                    </div>
                </div>

                <!-- Mensaje cuando no hay usuarios -->
                <div id="mensaje-sin-usuarios" class="text-center p-4">
                    <i class="bi bi-people fs-1 text-muted"></i>
                    <h5 class="text-muted mt-2">Presiona "Buscar Usuarios" para cargar los datos</h5>
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

    .table td {
        vertical-align: middle;
    }

    .badge {
        font-size: 0.75rem;
    }
</style>

<script src="<?= asset('build/js/usuarios/index.js') ?>"></script>