import { validarFormulario } from "../funciones";
import Swal from "sweetalert2";

const formLogin = document.getElementById("formLogin");
const btnLogin = document.getElementById("btnLogin");
const btnText = document.getElementById("btnText");
const btnSpinner = document.getElementById("btnSpinner");
const usuario_correo = document.getElementById("usuario_correo");
const usuario_contra = document.getElementById("usuario_contra");

// document.addEventListener("DOMContentLoaded", () => {
//   verificarSesionExistente();
// });

const verificarSesionExistente = async () => {
  try {
    const respuesta = await fetch("/proyecto01/login/verificarSesion", {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    });

    const datos = await respuesta.json();

    if (datos.codigo === 1) {
      console.log("Sesión activa detectada, redirigiendo...");
      setTimeout(() => {
        window.location.href = "/proyecto01/";
      }, 500);
    }
  } catch (error) {
    console.log("No hay sesión activa");
  }
};

// Función para mostrar estado de carga
const toggleLoadingState = (isLoading) => {
  if (isLoading) {
    btnLogin.disabled = true;
    btnLogin.classList.add("loading");
    btnText.textContent = "Verificando...";
    btnSpinner.classList.remove("d-none");
    formLogin.style.pointerEvents = "none";
  } else {
    btnLogin.disabled = false;
    btnLogin.classList.remove("loading");
    btnText.textContent = "Iniciar Sesión";
    btnSpinner.classList.add("d-none");
    formLogin.style.pointerEvents = "auto";
  }
};

// Función para validar formato de email
const validarEmail = (email) => {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
};

// Función para limpiar estilos de validación
const limpiarValidacion = () => {
  usuario_correo.classList.remove("is-invalid");
  usuario_contra.classList.remove("is-invalid");
};

// Función principal de login
const loginAPI = async (e) => {
  e.preventDefault();

  limpiarValidacion();

  if (!validarFormulario(formLogin)) {
    Swal.fire({
      icon: "warning",
      title: "Campos requeridos",
      text: "Por favor complete todos los campos obligatorios",
      confirmButtonText: "Entendido",
    });
    return;
  }

  if (!validarEmail(usuario_correo.value)) {
    usuario_correo.classList.add("is-invalid");
    Swal.fire({
      icon: "error",
      title: "Email inválido",
      text: "Por favor ingrese un correo electrónico válido",
      confirmButtonText: "Entendido",
    });
    return;
  }

  if (usuario_contra.value.length < 8) {
    usuario_contra.classList.add("is-invalid");
    Swal.fire({
      icon: "error",
      title: "Contraseña muy corta",
      text: "La contraseña debe tener al menos 8 caracteres",
      confirmButtonText: "Entendido",
    });
    return;
  }

  toggleLoadingState(true);

  try {
    const body = new FormData(formLogin);
    const url = "/proyecto01/login/loginAPI";
    const config = {
      method: "POST",
      body,
    };

    const respuesta = await fetch(url, config);
    const datos = await respuesta.json();

    console.log("Respuesta del servidor:", datos);

    const { codigo, mensaje, datos: datosUsuario } = datos;

    if (codigo === 1) {
      await Swal.fire({
        icon: "success",
        title: "¡Bienvenido!",
        text: mensaje,
        timer: 2000,
        showConfirmButton: false,
      });

      // window.location.href = datosUsuario.redirect_url;
      window.location.href = "/proyecto01/";
    } else {
      toggleLoadingState(false);

      if (mensaje.includes("Credenciales") || mensaje.includes("contraseña")) {
        usuario_correo.classList.add("is-invalid");
        usuario_contra.classList.add("is-invalid");
      }

      await Swal.fire({
        icon: "error",
        title: "Error de autenticación",
        text: mensaje,
        confirmButtonText: "Intentar nuevamente",
      });

      usuario_contra.value = "";
    }
  } catch (error) {
    toggleLoadingState(false);
    console.error("Error en login:", error);

    await Swal.fire({
      icon: "error",
      title: "Error de conexión",
      text: "No se pudo conectar con el servidor. Intente nuevamente.",
      confirmButtonText: "Entendido",
    });
  }
};

formLogin.addEventListener("submit", loginAPI);
usuario_correo.addEventListener("input", () => {
  usuario_correo.classList.remove("is-invalid");
});

usuario_contra.addEventListener("input", () => {
  usuario_contra.classList.remove("is-invalid");
});

usuario_correo.addEventListener("keypress", (e) => {
  if (e.key === "Enter") {
    e.preventDefault();
    usuario_contra.focus();
  }
});

usuario_contra.addEventListener("keypress", (e) => {
  if (e.key === "Enter") {
    e.preventDefault();
    loginAPI(e);
  }
});

export const cerrarSesion = async () => {
  const confirmacion = await Swal.fire({
    title: "¿Cerrar sesión?",
    text: "Su sesión será cerrada",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, cerrar sesión",
    cancelButtonText: "Cancelar",
  });

  if (confirmacion.isConfirmed) {
    window.location.href = "/proyecto01/login/logout";
  }
};

setInterval(async () => {
  try {
    const respuesta = await fetch("/proyecto01/login/verificarSesion");
    const datos = await respuesta.json();

    if (datos.codigo === 0) {
      await Swal.fire({
        icon: "warning",
        title: "Sesión expirada",
        text: "Su sesión ha expirado. Será redirigido al login.",
        confirmButtonText: "Entendido",
      });
      window.location.href = "/proyecto01/login";
    }
  } catch (error) {
    console.error("Error verificando sesión:", error);
  }
}, 5 * 60 * 1000);
