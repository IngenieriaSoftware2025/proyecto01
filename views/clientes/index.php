<?php
session_start();
$esAdmin = isset($_SESSION['rol']) && $_SESSION['rol'] === 'ADMIN';
?>

<!-- Solo mostrar botones de modificar/eliminar si es ADMIN -->
<?php if($esAdmin): ?>
    <button class='btn btn-warning btn-sm modificar mx-1'>
        <i class='bi bi-pencil-square'></i> Editar
    </button>
    <button class='btn btn-danger btn-sm eliminar mx-1'>
        <i class="bi bi-trash3"></i> Eliminar
    </button>
<?php else: ?>
    <span class="text-muted">Solo lectura</span>
<?php endif; ?>


<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h5 class="text-center mb-2">Sistema de Gestión de Celulares</h5>
                    <h4 class="text-center mb-2 text-primary">Gestión de Clientes</h4>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">
                    <form id="FormClientes">
                        <input type="hidden" id="id" name="id">

                        <!-- Fila de Nombres -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="nombre" class="form-label">Nombres <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese los nombres" maxlength="100">
                            </div>
                            <div class="col-lg-6">
                                <label for="apellido" class="form-label">Apellidos <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Ingrese los apellidos" maxlength="100">
                            </div>
                        </div>

                        <!-- Fila de Contacto -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="telefono" class="form-label">Teléfono <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Ej: 23456789" maxlength="8">
                                <div class="form-text">8 dígitos, debe iniciar con 2,3,4,5,6,7 u 8</div>
                            </div>
                            <div class="col-lg-6">
                                <label for="correo" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="correo" name="correo" placeholder="ejemplo@correo.com" maxlength="150">
                            </div>
                        </div>

                        <!-- Fila de Documentos -->
                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="nit" class="form-label">NIT</label>
                                <input type="text" class="form-control" id="nit" name="nit" placeholder="NIT: 123456-7 o DPI: 1234567890123">
                                <div class="form-text">NIT: formato 123456-7</div>
                            </div>
                            <div class="col-lg-6">
                                <label for="situacion" class="form-label">Estado del Cliente</label>
                                <select class="form-control" id="situacion" name="situacion">
                                    <option value="1">Activo</option>
                                    <option value="2">Inactivo</option>
                                    <option value="3">Moroso</option>
                                </select>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="row justify-content-center mt-4">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    <i class="bi bi-floppy me-2"></i>Guardar Cliente
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

<!-- Sección de Tabla de Clientes -->
<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h3 class="text-center">Clientes Registrados</h3>
                </div>

                <!-- Botón de búsqueda -->
                <div class="row justify-content-center mb-3">
                    <div class="col-auto">
                        <button class="btn btn-primary" type="button" id="BtnBuscarClientes">
                            <i class="bi bi-search me-2"></i>Buscar Clientes
                        </button>
                    </div>
                </div>

                <!-- Sección de tabla (inicialmente oculta) -->
                <div id="seccion-clientes" class="d-none">
                    <div class="table-responsive p-2">
                        <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableClientes">
                        </table>
                    </div>
                </div>

                <!-- Mensaje cuando no hay clientes -->
                <div id="mensaje-sin-clientes" class="text-center p-4">
                    <i class="bi bi-people fs-1 text-muted"></i>
                    <h5 class="text-muted mt-2">Presiona "Buscar Clientes" para cargar los datos</h5>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="<?= asset('build/js/clientes/index.js') ?>"></script>