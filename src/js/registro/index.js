console.log("Sistema de Usuarios");
import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones";
import { lenguaje } from "../lenguaje";

const FormUsuarios = document.getElementById("FormUsuarios");
const BtnGuardar = document.getElementById("BtnGuardar");
const BtnModificar = document.getElementById("BtnModificar");
const BtnLimpiar = document.getElementById("BtnLimpiar");
const ValidarTelefono = document.getElementById("usuario_tel");
const ValidarDPI = document.getElementById("usuario_dpi");

const ValidacionTelefono = () => {
  const cantidadDigitos = ValidarTelefono.value;

  if (cantidadDigitos.length < 1) {
    ValidarTelefono.classList.remove("is-valid", "is-invalid");
  } else {
    if (cantidadDigitos.length != 8) {
      Swal.fire({
        position: "center",
        icon: "warning",
        title: "Numero Invalido",
        text: "el numero telefono debe ser de 8 digitos",
        timer: 800,
      });

      ValidarTelefono.classList.remove("is-valid");
      ValidarTelefono.classList.add("is-invalid");
    } else {
      ValidarTelefono.classList.remove("is-invalid");
      ValidarTelefono.classList.add("is-valid");
    }
  }
};

const ValidacionDPI = () => {
  const cantidadDigitos = ValidarDPI.value;

  if (cantidadDigitos.length < 1) {
    ValidarDPI.classList.remove("is-valid", "is-invalid");
  } else {
    if (cantidadDigitos.length != 13) {
      Swal.fire({
        position: "center",
        icon: "warning",
        title: "DPI Invalido",
        text: "el DPI debe ser de 13 digitos",
        timer: 800,
      });

      ValidarDPI.classList.remove("is-valid");
      ValidarDPI.classList.add("is-invalid");
    } else {
      ValidarDPI.classList.remove("is-invalid");
      ValidarDPI.classList.add("is-valid");
    }
  }
};

const TablaUsuarios = new DataTable("#TableUsuarios", {
  dom: `<"row mt-3 justify-content-between" 
                <"col" l> 
                <"col" B> 
                <"col-3" f>
            >
            t
            <"row mt-3 justify-content-between" 
                <"col-md-3 d-flex align-items-center" i> 
                <"col-md-8 d-flex justify-content-end" p>
            >`,
  language: lenguaje,
  data: [],
  columns: [
    {
      title: "No.",
      data: "usuario_id",
      width: "8%",
      render: (data, type, row, meta) => meta.row + 1,
    },
    {
      title: "Nombre Completo",
      data: "usuario_nom1",
      render: (data, type, row) => `${row.usuario_nom1} ${row.usuario_nom2} ${row.usuario_ape1} ${row.usuario_ape2}`,
    },
    {
      title: "Telefono",
      data: "usuario_tel",
    },
    {
      title: "DPI",
      data: "usuario_dpi",
    },
    {
      title: "Correo",
      data: "usuario_correo",
    },
    {
      title: "Acciones",
      data: "usuario_id",
      searchable: false,
      orderable: false,
      render: (data, type, row, meta) => {
        return `
                <div class='d-flex justify-content-center'>
                         <button class='btn btn-warning modificar mx-1' 
                             data-usuario_id="${data}" 
                             data-usuario_nom1="${row.usuario_nom1}"  
                             data-usuario_nom2="${row.usuario_nom2}"
                             data-usuario_ape1="${row.usuario_ape1}"  
                             data-usuario_ape2="${row.usuario_ape2}"
                             data-usuario_tel="${row.usuario_tel}"
                             data-usuario_dpi="${row.usuario_dpi}"
                             data-usuario_direc="${row.usuario_direc}"
                             data-usuario_correo="${row.usuario_correo}">
                             <i class='bi bi-pencil-square me-1'></i> Modificar
                         </button>
                         <button class='btn btn-danger eliminar mx-1' 
                             data-usuario_id="${data}">
                            <i class="bi bi-trash3 me-1"></i>Eliminar
                         </button>
                     </div>`;
      },
    },
  ],
});

const guardarAPI = async (e) => {
  e.preventDefault();
  BtnGuardar.disabled = true;

  if (!validarFormulario(FormUsuarios, ["usuario_id"])) {
    Swal.fire({
      position: "center",
      icon: "warning",
      title: "Campo obligatorio",
      text: "todos los campos son obligatorios",
      showConfirmButton: true,
    });
    BtnGuardar.disabled = false;
    return;
  }

  const body = new FormData(FormUsuarios);
  const url = "/proyecto01/registro/guardarAPI";
  const config = {
    method: "POST",
    body,
  };

  try {
    const respuesta = await fetch(url, config);
    const datos = await respuesta.json();
    console.log(datos);
    const { codigo, mensaje } = datos;

    if (codigo === 1) {
      await Swal.fire({
        position: "center",
        icon: "success",
        title: "Exito",
        text: mensaje,
        showConfirmButton: true,
      });
      limpiarTodo();
      buscarAPI();
    }
  } catch (error) {
    console.log(error);
  }
  BtnGuardar.disabled = false;
};

const buscarAPI = async (e) => {
  const url = "/proyecto01/registro/buscarAPI";
  const config = {
    method: "GET",
  };
  try {
    const respuesta = await fetch(url, config);
    const datos = await respuesta.json();
    const { codigo, mensaje, data } = datos;

    if (codigo === 1) {
      Swal.fire({
        position: "center",
        icon: "success",
        title: "Exito",
        text: mensaje,
        timer: 800,
      });

      TablaUsuarios.clear().draw();
      TablaUsuarios.rows.add(data).draw();
    } else {
      Swal.fire({
        position: "center",
        icon: "warning",
        title: "Error",
        text: mensaje,
        timer: 800,
      });
    }
  } catch (error) {
    console.log(error);
  }
};

const llenarFormulario = (e) => {
  const datos = e.currentTarget.dataset;

  document.getElementById("usuario_id").value = datos.usuario_id;
  document.getElementById("usuario_nom1").value = datos.usuario_nom1;
  document.getElementById("usuario_nom2").value = datos.usuario_nom2;
  document.getElementById("usuario_ape1").value = datos.usuario_ape1;
  document.getElementById("usuario_ape2").value = datos.usuario_ape2;
  document.getElementById("usuario_tel").value = datos.usuario_tel;
  document.getElementById("usuario_dpi").value = datos.usuario_dpi;
  document.getElementById("usuario_direc").value = datos.usuario_direc;
  document.getElementById("usuario_correo").value = datos.usuario_correo;

  BtnGuardar.classList.add("d-none");
  BtnModificar.classList.remove("d-none");

  window.scrollTo({
    top: 0,
  });
};

const limpiarTodo = () => {
  FormUsuarios.reset();
  BtnGuardar.classList.remove("d-none");
  BtnModificar.classList.add("d-none");
};

const modificarAPI = async (e) => {
  e.preventDefault();
  BtnModificar.disabled = true;

  if (!validarFormulario(FormUsuarios, ["usuario_id"])) {
    Swal.fire({
      position: "center",
      icon: "warning",
      title: "Campos obligatorios",
      text: "Los campos se deben de llenar todos",
      showConfirmButton: false,
      timer: 800,
    });
    BtnModificar.disabled = false;
    return;
  }

  const body = new FormData(FormUsuarios);
  const url = "/proyecto01/registro/modificarAPI";
  const config = {
    method: "POST",
    body,
  };
  try {
    const respuesta = await fetch(url, config);
    const datos = await respuesta.json();
    const { codigo, mensaje } = datos;

    if (codigo === 1) {
      await Swal.fire({
        position: "center",
        icon: "success",
        title: "Exito",
        text: mensaje,
        showConfirmButton: false,
        timer: 800,
      });

      limpiarTodo();
      buscarAPI();
    } else {
      await Swal.fire({
        position: "center",
        icon: "info",
        title: "Error",
        text: mensaje,
        showConfirmButton: false,
        timer: 800,
      });
    }
  } catch (error) {
    console.log(error);
  }
  BtnModificar.disabled = false;
};

const eliminarAPI = async (e) => {
  const id = e.currentTarget.dataset.usuario_id;

  const AlertaConfirmarEliminar = await Swal.fire({
    position: "center",
    icon: "info",
    title: "¿Desea eliminar un usuario?",
    text: "¿Estas completamente seguro de que lo quieres eliminar???",
    showConfirmButton: true,
    confirmButtonText: "Si, Eliminar",
    confirmButtonColor: "red",
    cancelButtonText: "No, Cancelar",
    showCancelButton: true,
  });

  if (AlertaConfirmarEliminar.isConfirmed) {
    const url = `/proyecto01/registro/eliminarAPI?usuario_id=${id}`;
    const config = {
      method: "GET",
    };
    try {
      const respuesta = await fetch(url, config);
      const datos = await respuesta.json();
      const { codigo, mensaje } = datos;

      if (codigo === 1) {
        await Swal.fire({
          position: "center",
          icon: "success",
          title: "Exito",
          text: mensaje,
          showConfirmButton: false,
          timer: 800,
        });

        buscarAPI();
      } else {
        await Swal.fire({
          position: "center",
          icon: "info",
          title: "Error",
          text: mensaje,
          showConfirmButton: false,
          timer: 800,
        });
      }
    } catch (error) {
      console.log(error);
    }
  }
};

buscarAPI();
TablaUsuarios.on("click", ".eliminar", eliminarAPI);
TablaUsuarios.on("click", ".modificar", llenarFormulario);
ValidarTelefono.addEventListener("change", ValidacionTelefono);
ValidarDPI.addEventListener("change", ValidacionDPI);
FormUsuarios.addEventListener("submit", guardarAPI);
BtnLimpiar.addEventListener("click", limpiarTodo);
BtnModificar.addEventListener("click", modificarAPI);