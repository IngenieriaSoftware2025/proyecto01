<section class="vh-100 gradient-custom">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-12 col-md-8 col-lg-6 col-xl-5">
        <div class="card bg-dark text-white" style="border-radius: 1rem;">
          <div class="card-body p-5 text-center">

            <div class="mb-md-5 mt-md-4 pb-5">

              <h2 class="fw-bold mb-2 text-uppercase">Login</h2>
              <p class="text-white-50 mb-5">Por favor ingresa tu códio y contraseña</p>

              <form id="FormLogin" name="FormLogin">
                
                <div data-mdb-input-init class="form-outline form-white mb-4">
                  <input type="number" id="usu_codigo" name="usu_codigo" class="form-control form-control-lg" required />
                  <label class="form-label" for="usu_codigo">Código de Usuario</label>
                </div>

                <div data-mdb-input-init class="form-outline form-white mb-4">
                  <input type="password" id="usu_password" name="usu_password" class="form-control form-control-lg" required />
                  <label class="form-label" for="usu_password">Contraseña</label>
                </div>

                <button id="BtnIniciar" class="btn btn-outline-light btn-lg px-5" type="submit">
                  <span id="btnText">Iniciar Sesión</span>
                  <span id="btnSpinner" class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                </button>

              </form>

              <div class="d-flex justify-content-center text-center mt-4 pt-1">
                <p class="text-white-50 mb-0">
                  ¿No tienes cuenta? 
                  <a href="/proyecto01/registro" class="text-white">Regístrate aquí</a>
                </p>
              </div>

            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
.gradient-custom {
  background: linear-gradient(to right, rgba(106, 17, 203, 1), rgba(37, 117, 252, 1));
}

.form-white .form-control {
  border: 1px solid #fff;
}

.form-white .form-control:focus {
  border-color: #fff;
  box-shadow: inset 0 0 0 1px #fff;
}

.form-white .form-control::placeholder {
  color: #fff;
}

.form-white label {
  color: #fff;
}

.btn-outline-light:hover {
  background-color: #fff;
  color: #000;
  transform: translateY(-2px);
  transition: all 0.3s ease;
}
.loading {
  pointer-events: none;
  opacity: 0.6;
}
</style>

<script src="<?= asset('./build/js/login/index.js') ?>"></script>