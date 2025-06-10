<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h5 class="text-center mb-2">Sistema de Control de Inventario - Celulares</h5>
                    <h4 class="text-center mb-2 text-primary">Gestión de Marcas y Modelos</h4>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">
                    <form id="FormMarcas">
                        <input type="hidden" id="id" name="id">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="nombre" class="form-label">Nombre de la Marca</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej: Samsung, Apple, Xiaomi">
                            </div>
                            <div class="col-lg-6">
                                <label for="modelo" class="form-label">Modelo</label>
                                <input type="text" class="form-control" id="modelo" name="modelo" placeholder="Ej: Galaxy S23, iPhone 15, Redmi Note 12">
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-12">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" placeholder="Descripción opcional del modelo"></textarea>
                            </div>
                        </div>

                        <div class="row justify-content-center mt-4">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    <i class="bi bi-floppy me-2"></i>Guardar
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
                    <h3 class="text-center">Marcas y Modelos Registrados</h3>
                </div>

                <!-- Botón de búsqueda -->
                <div class="row justify-content-center mb-3">
                    <div class="col-auto">
                        <button class="btn btn-primary" type="button" id="BtnBuscarMarcas">
                            <i class="bi bi-search me-2"></i>Buscar Marcas
                        </button>
                    </div>
                </div>

                <!-- Sección de tabla (inicialmente oculta) -->
                <div id="seccion-marcas" class="d-none">
                    <div class="table-responsive p-2">
                        <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableMarcas">
                        </table>
                    </div>
                </div>

                <!-- Mensaje cuando no hay marcas -->
                <div id="mensaje-sin-marcas" class="text-center p-4">
                    <i class="bi bi-phone fs-1 text-muted"></i>
                    <h5 class="text-muted mt-2">Presiona "Buscar Marcas" para cargar los datos</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/marcas/index.js') ?>"></script>