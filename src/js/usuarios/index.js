import { Toast, validarFormulario } from "../funciones";
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";
import Swal from "sweetalert2";

const FormUsuarios = document.getElementById("FormUsuarios");
const BtnGuardar = document.getElementById("BtnGuardar");
const BtnModificar = document.getElementById("BtnModificar");
const BtnLimpiar = document.getElementById("BtnLimpiar");
const BtnBuscarUsuarios = document.getElementById("BtnBuscarUsuarios");
const TableUsuarios = document.getElementById("TableUsuarios");

let TablaUsuarios;

// FUNCIÓN PARA GUARDAR USUARIO
const guardarUsuario = async (e) => {
  e.preventDefault();

  // Validar campos requeridos
  if (
    !FormUsuarios.usu_codigo.value ||
    !FormUsuarios.usu_nombre.value ||
    !FormUsuarios.usu_password.value ||
    !FormUsuarios.usu_rol.value
  ) {
    Toast.fire({
      icon: "error",
      title: "Complete todos los campos requeridos",
    });
    return;
  }

  try {
    const body = new FormData(FormUsuarios);
    const url = "/proyecto01/usuarios/guardarAPI";

    const config = {
      method: "POST",
      body,
    };

    const respuesta = await fetch(url, config);
    const datos = await respuesta.json();

    if (datos.codigo === 1) {
      Toast.fire({
        icon: "success",
        title: datos.mensaje,
      });

      limpiarFormulario();
      buscarUsuarios();
    } else {
      Toast.fire({
        icon: "error",
        title: datos.mensaje,
      });
    }
  } catch (error) {
    console.error("Error:", error);
    Toast.fire({
      icon: "error",
      title: "Error de conexión",
    });
  }
};

// FUNCIÓN PARA BUSCAR USUARIOS
const buscarUsuarios = async () => {
  try {
    const url = "/proyecto01/usuarios/buscarAPI";
    const config = {
      method: "GET",
    };

    const respuesta = await fetch(url, config);
    const datos = await respuesta.json();

    if (datos.codigo === 1) {
      // Mostrar sección de tabla
      document.getElementById("seccion-usuarios").classList.remove("d-none");
      document.getElementById("mensaje-sin-usuarios").classList.add("d-none");

      llenarTablaUsuarios(datos.data);
    } else {
      Toast.fire({
        icon: "error",
        title: datos.mensaje,
      });
    }
  } catch (error) {
    console.error("Error:", error);
    Toast.fire({
      icon: "error",
      title: "Error al buscar usuarios",
    });
  }
};

// FUNCIÓN PARA LLENAR TABLA DE USUARIOS
const llenarTablaUsuarios = (usuarios) => {
  if (TablaUsuarios) {
    TablaUsuarios.destroy();
  }

  TablaUsuarios = new DataTable("#TableUsuarios", {
    data: usuarios,
    language: lenguaje,
    pageLength: 10,
    order: [[1, "asc"]],
    columns: [
      {
        title: "ID",
        data: "usu_id",
        width: "5%",
      },
      {
        title: "Codigo",
        data: "usu_codigo",
        width: "15%",
        render: (data) => `<code class="bg-light p-1 rounded">${data}</code>`,
      },
      {
        title: "Nombre",
        data: "usu_nombre",
        width: "35%",
      },
      {
        title: "Estado",
        data: "usu_situacion",
        width: "15%",
        render: (data) => {
          if (data == 1) {
            return '<span class="badge bg-success">Activo</span>';
          } else {
            return '<span class="badge bg-secondary">Inactivo</span>';
          }
        },
      },
      {
        title: "Acciones",
        data: "usu_id",
        width: "40%",
        orderable: false,
        render: (data, type, row) => {
          return `
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-warning btn-sm modificar-usuario" 
                                data-id="${data}"
                                data-codigo="${row.usu_codigo}"
                                data-nombre="${row.usu_nombre}"
                                data-situacion="${row.usu_situacion}">
                                <i class="bi bi-pencil me-1"></i>Editar
                            </button>
                            <button class="btn btn-danger btn-sm eliminar-usuario" 
                                data-id="${data}"
                                data-nombre="${row.usu_nombre}">
                                <i class="bi bi-trash me-1"></i>Eliminar
                            </button>
                        </div>
                    `;
        },
      },
    ],
  });

  // Event listeners para botones de la tabla
  setTimeout(() => {
    document.querySelectorAll(".modificar-usuario").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        llenarFormularioModificar(e);
      });
    });

    document.querySelectorAll(".eliminar-usuario").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        eliminarUsuario(e);
      });
    });
  }, 500); // Aumentar tiempo de espera
};

// FUNCIÓN PARA LLENAR FORMULARIO PARA MODIFICAR
const llenarFormularioModificar = (e) => {
  const datos = e.currentTarget.dataset;

  document.getElementById("usu_id").value = datos.id;
  document.getElementById("usu_codigo").value = datos.codigo;
  document.getElementById("usu_nombre").value = datos.nombre; 
  document.getElementById("usu_situacion").value = datos.situacion;

  // Limpiar campo de contraseña para modificación
  document.getElementById("usu_password").value = "";
  document.getElementById("usu_password").required = false;

  // Cambiar botones
  BtnGuardar.classList.add("d-none");
  BtnModificar.classList.remove("d-none");

  // Scroll al formulario
  window.scrollTo({ top: 0, behavior: "smooth" });
};

// FUNCIÓN PARA MODIFICAR USUARIO
const modificarUsuario = async (e) => {
    e.preventDefault();

    // Validación manual
    if (!FormUsuarios.usu_nombre.value.trim()) {
        Toast.fire({
            icon: "error",
            title: "El nombre es obligatorio"
        });
        return;
    }

    try {
        const body = new FormData(FormUsuarios);
        const url = "/proyecto01/usuarios/modificarAPI";

        const config = {
            method: "POST",
            body
        };

        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();

        if (datos.codigo === 1) {
            Toast.fire({
                icon: "success",
                title: datos.mensaje
            });

            limpiarFormulario();
            buscarUsuarios();
        } else {
            Toast.fire({
                icon: "error",
                title: datos.mensaje
            });
        }
    } catch (error) {
        console.error("Error:", error);
        Toast.fire({
            icon: "error",
            title: "Error de conexión"
        });
    }
};

// FUNCIÓN PARA ELIMINAR USUARIO
const eliminarUsuario = async (e) => {
  const id = e.currentTarget.dataset.id;
  const nombre = e.currentTarget.dataset.nombre;

  const confirmacion = await Swal.fire({
    title: "¿Eliminar usuario?",
    text: `¿Está seguro de eliminar el usuario "${nombre}"?`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  });

  if (!confirmacion.isConfirmed) return;

  try {
    const url = `/proyecto01/usuarios/eliminarAPI?id=${id}`;
    const config = {
      method: "GET",
    };

    const respuesta = await fetch(url, config);
    const datos = await respuesta.json();

    if (datos.codigo === 1) {
      Toast.fire({
        icon: "success",
        title: datos.mensaje,
      });

      buscarUsuarios();
    } else {
      Toast.fire({
        icon: "error",
        title: datos.mensaje,
      });
    }
  } catch (error) {
    console.error("Error:", error);
    Toast.fire({
      icon: "error",
      title: "Error de conexión",
    });
  }
};

// FUNCIÓN PARA LIMPIAR FORMULARIO
const limpiarFormulario = () => {
  FormUsuarios.reset();
  document.getElementById("usu_id").value = "";
  document.getElementById("usu_password").required = true;

  BtnGuardar.classList.remove("d-none");
  BtnModificar.classList.add("d-none");
};

// EVENT LISTENERS
FormUsuarios.addEventListener("submit", guardarUsuario);
BtnModificar.addEventListener("click", modificarUsuario);
BtnLimpiar.addEventListener("click", limpiarFormulario);
BtnBuscarUsuarios.addEventListener("click", buscarUsuarios);

console.log("Módulo de usuarios inicializado correctamente");
