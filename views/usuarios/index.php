
<section class="vh-100 bg-image"
  style="background-image: url('https://mdbcdn.b-cdn.net/img/Photos/new-templates/search-box/img4.webp');">
  <div class="mask d-flex align-items-center h-100 gradient-custom-3">
    <div class="container h-100">
      <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-12 col-md-10 col-lg-8 col-xl-7">
          <div class="card" style="border-radius: 15px;">
            <div class="card-body p-5">
              <h2 class="text-uppercase text-center mb-5">Crear Cuenta de Usuario</h2>
              <form id="formUsuario" name="formUsuario">
                <div class="row">
                  <div class="col-md-6">
                    <div data-mdb-input-init class="form-outline mb-4">
                      <input type="text" id="usuario_nom1" name="usuario_nom1" class="form-control form-control-lg" required />
                      <label class="form-label" for="usuario_nom1">Primer Nombre</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div data-mdb-input-init class="form-outline mb-4">
                      <input type="text" id="usuario_nom2" name="usuario_nom2" class="form-control form-control-lg" required />
                      <label class="form-label" for="usuario_nom2">Segundo Nombre</label>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div data-mdb-input-init class="form-outline mb-4">
                      <input type="text" id="usuario_ape1" name="usuario_ape1" class="form-control form-control-lg" required />
                      <label class="form-label" for="usuario_ape1">Primer Apellido</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div data-mdb-input-init class="form-outline mb-4">
                      <input type="text" id="usuario_ape2" name="usuario_ape2" class="form-control form-control-lg" required />
                      <label class="form-label" for="usuario_ape2">Segundo Apellido</label>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div data-mdb-input-init class="form-outline mb-4">
                      <input type="tel" id="usuario_tel" name="usuario_tel" class="form-control form-control-lg" required />
                      <label class="form-label" for="usuario_tel">Teléfono</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div data-mdb-input-init class="form-outline mb-4">
                      <input type="text" id="usuario_dpi" name="usuario_dpi" class="form-control form-control-lg" maxlength="13" required />
                      <label class="form-label" for="usuario_dpi">DPI</label>
                    </div>
                  </div>
                </div>

                <div data-mdb-input-init class="form-outline mb-4">
                  <input type="text" id="usuario_direc" name="usuario_direc" class="form-control form-control-lg" maxlength="150" required />
                  <label class="form-label" for="usuario_direc">Dirección</label>
                </div>

                <div data-mdb-input-init class="form-outline mb-4">
                  <input type="email" id="usuario_correo" name="usuario_correo" class="form-control form-control-lg" maxlength="100" required />
                  <label class="form-label" for="usuario_correo">Correo Electrónico</label>
                </div>

                <div data-mdb-input-init class="form-outline mb-4">
                  <input type="password" id="usuario_contra" name="usuario_contra" class="form-control form-control-lg" required />
                  <label class="form-label" for="usuario_contra">Contraseña</label>
                </div>

                <div data-mdb-input-init class="form-outline mb-4">
                  <input type="password" id="confirmar_contra" name="confirmar_contra" class="form-control form-control-lg" required />
                  <label class="form-label" for="confirmar_contra">Confirmar Contraseña</label>
                </div>

                <div data-mdb-input-init class="form-outline mb-4">
                  <input type="file" id="usuario_fotografia" name="usuario_fotografia" class="form-control form-control-lg" accept="image/*" />
                  <label class="form-label" for="usuario_fotografia">Fotografía (Opcional)</label>
                </div>

                <div class="form-check d-flex justify-content-center mb-5">
                  <input class="form-check-input me-2" type="checkbox" value="" id="terminos_condiciones" required />
                  <label class="form-check-label" for="terminos_condiciones">
                    Acepto todos los términos y condiciones del <a href="#!" class="text-body"><u>Servicio</u></a>
                  </label>
                </div>

                <div class="d-flex justify-content-center">
                  <button type="submit" data-mdb-button-init data-mdb-ripple-init 
                    class="btn btn-success btn-block btn-lg gradient-custom-4 text-body">
                    Registrar Usuario
                  </button>
                </div>

                <p class="text-center text-muted mt-5 mb-0">
                  ¿Ya tienes una cuenta? 
                  <a href="#!" class="fw-bold text-body"><u>Inicia sesión aquí</u></a>
                </p>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script src="<?= asset('./build/js/registro/index.js') ?>"></script>
