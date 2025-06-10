console.log("Hola Mundo 3");
import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones";
import { lenguaje } from "../lenguaje";

// Elementos DOM principales
const FormClientes = document.getElementById("FormClientes");
const BtnGuardar = document.getElementById("BtnGuardar");
const BtnModificar = document.getElementById("BtnModificar");
const BtnLimpiar = document.getElementById("BtnLimpiar");
const ValidarTelefono = document.getElementById("telefono");
const ValidarNIT = document.getElementById("nit");

// Elementos para búsqueda manual
const BtnBuscarClientes = document.getElementById("BtnBuscarClientes");
const seccionClientes = document.getElementById("seccion-clientes");
const mensajeSinClientes = document.getElementById("mensaje-sin-clientes");
let clientesCargados = false;

const ValidacionTelefono = () => {
  const cantidadDigitos = ValidarTelefono.value;

  // Si el campo está vacío, remover todas las clases de validación
  if (cantidadDigitos.length < 1) {
    ValidarTelefono.classList.remove("is-valid", "is-invalid");
  } else {
    // Si el número no tiene exactamente 8 dígitos
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
      // Si el número tiene exactamente 8 dígitos
      ValidarTelefono.classList.remove("is-invalid");
      ValidarTelefono.classList.add("is-valid");
    }
  }
};

function validarNIT() {
  const nit = ValidarNIT.value.trim();

  let nd,
    add = 0;

  if ((nd = /^(\d+)-?([\dkK])$/.exec(nit))) {
    nd[2] = nd[2].toLowerCase() === "k" ? 10 : parseInt(nd[2], 10);

    for (let i = 0; i < nd[1].length; i++) {
      add += ((i - nd[1].length) * -1 + 1) * parseInt(nd[1][i], 10);
    }
    return (11 - (add % 11)) % 11 === nd[2];
  } else {
    return false;
  }
}

const ValidacionNIT = () => {
  if (validarNIT()) {
    ValidarNIT.classList.add("is-valid");
    ValidarNIT.classList.remove("is-invalid");
  } else {
    ValidarNIT.classList.remove("is-valid");
    ValidarNIT.classList.add("is-invalid");
    Swal.fire({
      position: "center",
      icon: "error",
      title: "NIT INVALIDO",
      text: "El numero de nit ingresado es invalido",
      showConfirmButton: true,
    });
  }
  return validarNIT();
};

const TablaClientes = new DataTable("#TableClientes", {
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
      data: "id",
      width: "8%",
      render: (data, type, row, meta) => meta.row + 1,
    },
    {
      title: "Nombre Completo",
      data: "nombre",
      render: (data, type, row) => `${row.nombre} ${row.apellido}`,
    },
    {
      title: "Telefono",
      data: "telefono",
    },
    {
      title: "NIT",
      data: "nit",
    },
    {
      title: "Correo",
      data: "correo",
    },
    {
      title: "Acciones",
      data: "id",
      searchable: false,
      orderable: false,
      render: (data, type, row, meta) => {
        return `
                <div class='d-flex justify-content-center'>
                         <button class='btn btn-warning modificar mx-1' 
                             data-id="${data}" 
                             data-nombre="${row.nombre}"  
                             data-apellido="${row.apellido}"
                             data-nit="${row.nit}"  
                             data-telefono="${row.telefono}"
                             data-correo="${row.correo}">
                             <i class='bi bi-pencil-square me-1'></i> Modificar
                         </button>
                         <button class='btn btn-danger eliminar mx-1' 
                             data-id="${data}">
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

  if (!validarFormulario(FormClientes, ["id"])) {
    Swal.fire({
      position: "center",
      icon: "warning",
      title: "Campo obligatorio",
      text: "todos los campos son obligatotios",
      showConfirmButton: true,
    });
    BtnGuardar.disabled = false;
    return;
  }

  const body = new FormData(FormClientes);
  const url = "/proyecto01/clientes/guardarAPI";
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
      
      // Solo actualizar si los clientes ya estaban cargados
      if (clientesCargados) {
        buscarAPI();
      }
    }
  } catch (error) {
    console.log(error);
  }
  BtnGuardar.disabled = false;
};

const buscarAPI = async (e) => {
  // Deshabilitar botón durante la búsqueda
  BtnBuscarClientes.disabled = true;
  BtnBuscarClientes.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Buscando...';

  const url = "/proyecto01/clientes/buscarAPI";
  const config = {
    method: "GET",
  };

  try {
    const respuesta = await fetch(url, config);
    const datos = await respuesta.json();
    const { codigo, mensaje, data } = datos;

    if (codigo === 1) {
      // Mostrar sección de tabla y ocultar mensaje
      seccionClientes.classList.remove("d-none");
      seccionClientes.classList.add("fade-in");
      mensajeSinClientes.classList.add("d-none");

      // Limpiar y cargar datos en la tabla
      TablaClientes.clear().draw();
      TablaClientes.rows.add(data).draw();
      clientesCargados = true;

      Swal.fire({
        position: "center",
        icon: "success",
        title: "Exito",
        text: `${data.length} cliente(s) encontrado(s)`,
        timer: 800,
      });
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
    console.error('Error:', error);
    Swal.fire({
      position: "center",
      icon: "error",
      title: "Error de conexión",
      text: "No se pudo conectar con el servidor",
      showConfirmButton: true,
    });
  } finally {
    // Rehabilitar botón
    BtnBuscarClientes.disabled = false;
    BtnBuscarClientes.innerHTML = '<i class="bi bi-search me-2"></i>Buscar Clientes';
  }
};

const llenarFormulario = (e) => {
  const datos = e.currentTarget.dataset;

  document.getElementById("id").value = datos.id;
  document.getElementById("nombre").value = datos.nombre;
  document.getElementById("apellido").value = datos.apellido;
  document.getElementById("telefono").value = datos.telefono;
  document.getElementById("nit").value = datos.nit;
  document.getElementById("correo").value = datos.correo;

  BtnGuardar.classList.add("d-none");
  BtnModificar.classList.remove("d-none");

  window.scrollTo({
    top: 0,
  });
};

const limpiarTodo = () => {
  FormClientes.reset();
  BtnGuardar.classList.remove("d-none");
  BtnModificar.classList.add("d-none");
};

const modificarAPI = async (e) => {
  e.preventDefault();
  BtnModificar.disabled = true;

  if (!validarFormulario(FormClientes, ["id"])) {
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

  const body = new FormData(FormClientes);
  const url = "/proyecto01/clientes/modificarAPI";
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
      
      // Solo actualizar si los clientes ya estaban cargados
      if (clientesCargados) {
        buscarAPI();
      }
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
  const id = e.currentTarget.dataset.id;

  const AlertaConfirmarEliminar = await Swal.fire({
    position: "center",
    icon: "info",
    title: "¿Desea eliminar un cliente",
    text: "¿Estas completamente seguro de que lo quieres eliminar???",
    showConfirmButton: true,
    confirmButtonText: "Si, Eliminar",
    confirmButtonColor: "red",
    cancelButtonText: "No, Cancelar",
    showCancelButton: true,
  });

  if (AlertaConfirmarEliminar.isConfirmed) {
    const url = `/proyecto01/clientes/eliminarAPI?id=${id}`;
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

        // Solo actualizar si los clientes ya estaban cargados
        if (clientesCargados) {
          buscarAPI();
        }
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

// Event Listeners
BtnBuscarClientes.addEventListener("click", buscarAPI);
TablaClientes.on("click", ".eliminar", eliminarAPI);
TablaClientes.on("click", ".modificar", llenarFormulario);
ValidarTelefono.addEventListener("change", ValidacionTelefono);
ValidarNIT.addEventListener("change", ValidacionNIT);
FormClientes.addEventListener("submit", guardarAPI);
BtnLimpiar.addEventListener("click", limpiarTodo);
BtnModificar.addEventListener("click", modificarAPI);