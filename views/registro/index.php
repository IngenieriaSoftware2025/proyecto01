<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h5 class="text-center mb-2">Bienvenido a mi aplicacion de usuarios</h5>
                    <h4 class="text-center mb-2 text-primary">Manipulacion de usuarios</h4>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">

                    <form id="FormUsuarios">
                        <input type="hidden" id="usuario_id" name="usuario_id">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-3">
                                <label for="usuario_nom1" class="form-label">Primer Nombre</label>
                                <input type="text" class="form-control" id="usuario_nom1" name="usuario_nom1" placeholder="Ingrese aca su primer nombre">
                            </div>
                            <div class="col-lg-3">
                                <label for="usuario_nom2" class="form-label">Segundo Nombre</label>
                                <input type="text" class="form-control" id="usuario_nom2" name="usuario_nom2" placeholder="Ingrese aca su segundo nombre">
                            </div>
                            <div class="col-lg-3">
                                <label for="usuario_ape1" class="form-label">Primer Apellido</label>
                                <input type="text" class="form-control" id="usuario_ape1" name="usuario_ape1" placeholder="Ingrese aca su primer apellido">
                            </div>
                            <div class="col-lg-3">
                                <label for="usuario_ape2" class="form-label">Segundo Apellido</label>
                                <input type="text" class="form-control" id="usuario_ape2" name="usuario_ape2" placeholder="Ingrese aca su segundo apellido">
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-4">
                                <label for="usuario_tel" class="form-label">Telefono</label>
                                <input type="text" class="form-control" id="usuario_tel" name="usuario_tel" placeholder="Ingrese aca su numero de telefono con +502">
                            </div>

                            <div class="col-lg-4">
                                <label for="usuario_dpi" class="form-label">DPI</label>
                                <input type="text" class="form-control" id="usuario_dpi" name="usuario_dpi" placeholder="Ingrese aca su DPI de 13 digitos">
                            </div>

                            <div class="col-lg-4">
                                <label for="usuario_correo" class="form-label">Correo</label>
                                <input type="email" class="form-control" id="usuario_correo" name="usuario_correo" placeholder="Ingrese aca su correo ejemplo@ejemplo.com">
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="usuario_direc" class="form-label">Direccion</label>
                                <textarea class="form-control" id="usuario_direc" name="usuario_direc" placeholder="Ingrese aca su direccion completa" rows="2"></textarea>
                            </div>
                            <div class="col-lg-6">
                                <label for="usuario_contra" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="usuario_contra" name="usuario_contra" placeholder="Ingrese aca su contraseña">
                            </div>
                        </div>

                        <div class="row justify-content-center mt-5">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar"><i class="bi bi-floppy me-2"></i>
                                    Guardar
                                </button>
                            </div>

                            <div class="col-auto ">
                                <button class="btn btn-warning d-none" type="button" id="BtnModificar"><i class="bi bi-pencil me-2"></i>
                                    Modificar
                                </button>
                            </div>

                            <div class="col-auto">
                                <button class="btn btn-secondary" type="reset" id="BtnLimpiar"><i class="bi bi-arrow-clockwise me-2"></i>
                                    Limpiar
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
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <h3 class="text-center">usuarios existentes</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableUsuarios">
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
</div>
<script src="<?= asset('build/js/registro/index.js') ?>"></script>