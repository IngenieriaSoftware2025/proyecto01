<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h5 class="text-center mb-2">Bienvenido a mi aplicación de usuarios</h5>
                    <h4 class="text-center mb-2 text-primary">Registro de Nuevos Usuarios</h4>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">
                    <form id="formUsuario" name="formUsuario" enctype="multipart/form-data">
                        <input type="hidden" id="usuario_id" name="usuario_id">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-3">
                                <label for="usuario_nom1" class="form-label">Primer Nombre</label>
                                <input type="text" class="form-control" id="usuario_nom1" name="usuario_nom1" placeholder="Ingrese su primer nombre">
                            </div>
                            <div class="col-lg-3">
                                <label for="usuario_nom2" class="form-label">Segundo Nombre</label>
                                <input type="text" class="form-control" id="usuario_nom2" name="usuario_nom2" placeholder="Ingrese su segundo nombre">
                            </div>
                            <div class="col-lg-3">
                                <label for="usuario_ape1" class="form-label">Primer Apellido</label>
                                <input type="text" class="form-control" id="usuario_ape1" name="usuario_ape1" placeholder="Ingrese su primer apellido">
                            </div>
                            <div class="col-lg-3">
                                <label for="usuario_ape2" class="form-label">Segundo Apellido</label>
                                <input type="text" class="form-control" id="usuario_ape2" name="usuario_ape2" placeholder="Ingrese su segundo apellido">
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-4">
                                <label for="usuario_tel" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="usuario_tel" name="usuario_tel" placeholder="Ej: 12345678 (8 dígitos)" maxlength="8">
                                <div class="form-text">Ingrese 8 dígitos sin espacios</div>
                            </div>
                            <div class="col-lg-4">
                                <label for="usuario_dpi" class="form-label">DPI</label>
                                <input type="text" class="form-control" id="usuario_dpi" name="usuario_dpi" placeholder="Ej: 1234567890123 (13 dígitos)" maxlength="13">
                                <div class="form-text">Ingrese 13 dígitos sin espacios</div>
                            </div>
                            <div class="col-lg-4">
                                <label for="usuario_correo" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="usuario_correo" name="usuario_correo" placeholder="ejemplo@correo.com" maxlength="100">
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="usuario_direc" class="form-label">Dirección</label>
                                <textarea class="form-control" id="usuario_direc" name="usuario_direc" placeholder="Ingrese su dirección completa" rows="3" maxlength="150"></textarea>
                                <div class="form-text">Máximo 150 caracteres</div>
                            </div>
                            <div class="col-lg-6">
                                <label for="usuario_fotografia" class="form-label">Fotografía</label>
                                <input type="file" class="form-control" id="usuario_fotografia" name="usuario_fotografia" accept=".jpg,.jpeg,.png">
                                <div class="form-text">Formatos permitidos: JPG, PNG. Máximo 2MB</div>
                                
                                <div class="mt-3 text-center">
                                    <img id="preview_foto" src="#" alt="Vista previa" class="img-thumbnail d-none" style="max-width: 150px; max-height: 150px;">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="usuario_contra" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="usuario_contra" name="usuario_contra" placeholder="Mínimo 10 caracteres">
                                <div class="form-text">
                                    Debe contener: mayúscula, minúscula, número y carácter especial
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label for="confirmar_contra" class="form-label">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="confirmar_contra" name="confirmar_contra" placeholder="Repita la contraseña">
                            </div>
                        </div>

                        <div class="row justify-content-center mt-4">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    <i class="bi bi-floppy me-2"></i>Registrar Usuario
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
                            <div class="col-auto">
                                <button class="btn btn-primary" type="button" id="BtnBuscarUsuarios">
                                    <i class="bi bi-search me-2"></i>Buscar Usuarios
                                </button>
                            </div>
                        </div>

                        <div class="row justify-content-center mt-4">
                            <div class="col-auto">
                                <p class="text-center text-muted">
                                    ¿Ya tienes una cuenta? 
                                    <a href="/proyecto01/login" class="text-primary fw-bold">Inicia sesión aquí</a>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row justify-content-center p-3 d-none" id="seccion_usuarios">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <h3 class="text-center text-primary">Usuarios Registrados</h3>
                    </div>
                </div>

                <div id="contenedor_tabla">
                    <div class="table-responsive p-2">
                        <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableUsuarios">
                            <!-- La tabla se generará dinámicamente con JavaScript -->
                        </table>
                    </div>
                </div>

                <div id="mensaje_sin_datos" class="text-center p-4 d-none">
                    <i class="bi bi-people" style="font-size: 3rem; color: #6c757d;"></i>
                    <p class="text-muted mt-2">No se encontraron usuarios registrados</p>
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

#preview_foto {
    max-width: 150px;
    max-height: 150px;
    border-radius: 8px;
}
</style>

<!-- Script de registro -->
<script src="<?= asset('build/js/registro/index.js') ?>"></script>