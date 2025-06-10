console.log("Modulo de Marcas Cargado");
import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones";
import { lenguaje } from "../lenguaje";

// Elementos DOM principales
const FormMarcas = document.getElementById("FormMarcas");
const BtnGuardar = document.getElementById("BtnGuardar");
const BtnModificar = document.getElementById("BtnModificar");
const BtnLimpiar = document.getElementById("BtnLimpiar");

// Elementos para búsqueda manual
const BtnBuscarMarcas = document.getElementById("BtnBuscarMarcas");
const seccionMarcas = document.getElementById("seccion-marcas");
const mensajeSinMarcas = document.getElementById("mensaje-sin-marcas");
let marcasCargadas = false;

// Inicializar DataTable
const TablaMarcas = new DataTable("#TableMarcas", {
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
      title: "Marca",
      data: "nombre",
      width: "20%"
    },
    {
      title: "Modelo",
      data: "modelo",
      width: "25%"
    },
    {
      title: "Descripción",
      data: "descripcion",
      width: "30%",
      render: (data) => data || 'Sin descripción'
    },
    {
      title: "Fecha Creación",
      data: "fecha_creacion",
      width: "12%"
    },
    {
      title: "Acciones",
      data: "id",
      width: "15%",
      searchable: false,
      orderable: false,
      render: (data, type, row, meta) => {
        return `
                <div class='d-flex justify-content-center'>
                         <button class='btn btn-warning modificar mx-1' 
                             data-id="${data}" 
                             data-nombre="${row.nombre}"  
                             data-modelo="${row.modelo}"
                             data-descripcion="${row.descripcion || ''}"
                             title="Modificar marca">
                             <i class='bi bi-pencil-square me-1'></i> Modificar
                         </button>
                         <button class='btn btn-danger eliminar mx-1' 
                             data-id="${data}"
                             title="Eliminar marca">
                            <i class="bi bi-trash3 me-1"></i>Eliminar
                         </button>
                     </div>`;
      },
    },
  ],
});

// Función para guardar marca
const guardarAPI = async (e) => {
  e.preventDefault();
  BtnGuardar.disabled = true;

  if (!validarFormulario(FormMarcas, ["id"])) {
    Swal.fire({
      position: "center",
      icon: "warning",
      title: "Campos obligatorios",
      text: "El nombre de la marca y el modelo son obligatorios",
      showConfirmButton: true,
    });
    BtnGuardar.disabled = false;
    return;
  }

  const body = new FormData(FormMarcas);
  const url = "/proyecto01/marcas/guardarAPI";
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
        title: "Éxito",
        text: mensaje,
        showConfirmButton: true,
      });
      limpiarTodo();
      
      // Solo actualizar si las marcas ya estaban cargadas
      if (marcasCargadas) {
        buscarAPI();
      }
    } else {
      Swal.fire({
        position: "center",
        icon: "error",
        title: "Error",
        text: mensaje,
        showConfirmButton: true,
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
  }
  BtnGuardar.disabled = false;
};

// Función para buscar marcas
const buscarAPI = async (e) => {
  // Deshabilitar botón durante la búsqueda
  BtnBuscarMarcas.disabled = true;
  BtnBuscarMarcas.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Buscando...';

  const url = "/proyecto01/marcas/buscarAPI";
  const config = {
    method: "GET",
  };

  try {
    const respuesta = await fetch(url, config);
    const datos = await respuesta.json();
    const { codigo, mensaje, data } = datos;

    if (codigo === 1) {
      // Mostrar sección de tabla y ocultar mensaje
      seccionMarcas.classList.remove("d-none");
      seccionMarcas.classList.add("fade-in");
      mensajeSinMarcas.classList.add("d-none");

      // Limpiar y cargar datos en la tabla
      TablaMarcas.clear().draw();
      TablaMarcas.rows.add(data).draw();
      marcasCargadas = true;

      Swal.fire({
        position: "center",
        icon: "success",
        title: "Éxito",
        text: `${data.length} marca(s) encontrada(s)`,
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
    BtnBuscarMarcas.disabled = false;
    BtnBuscarMarcas.innerHTML = '<i class="bi bi-search me-2"></i>Buscar Marcas';
  }
};

// Función para llenar formulario con datos de modificación
const llenarFormulario = (e) => {
  const datos = e.currentTarget.dataset;

  document.getElementById("id").value = datos.id;
  document.getElementById("nombre").value = datos.nombre;
  document.getElementById("modelo").value = datos.modelo;
  document.getElementById("descripcion").value = datos.descripcion;

  BtnGuardar.classList.add("d-none");
  BtnModificar.classList.remove("d-none");

  window.scrollTo({
    top: 0,
  });
};

// Función para limpiar formulario
const limpiarTodo = () => {
  FormMarcas.reset();
  BtnGuardar.classList.remove("d-none");
  BtnModificar.classList.add("d-none");
};

// Función para modificar marca
const modificarAPI = async (e) => {
  e.preventDefault();
  BtnModificar.disabled = true;

  if (!validarFormulario(FormMarcas, ["id"])) {
    Swal.fire({
      position: "center",
      icon: "warning",
      title: "Campos obligatorios",
      text: "El nombre de la marca y el modelo son obligatorios",
      showConfirmButton: false,
      timer: 800,
    });
    BtnModificar.disabled = false;
    return;
  }

  const body = new FormData(FormMarcas);
  const url = "/proyecto01/marcas/modificarAPI";
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
        title: "Éxito",
        text: mensaje,
        showConfirmButton: false,
        timer: 800,
      });

      limpiarTodo();
      
      // Solo actualizar si las marcas ya estaban cargadas
      if (marcasCargadas) {
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
    console.error('Error:', error);
    Swal.fire({
      position: "center",
      icon: "error",
      title: "Error de conexión",
      text: "No se pudo conectar con el servidor",
      showConfirmButton: true,
    });
  }
  BtnModificar.disabled = false;
};

// Función para eliminar marca
const eliminarAPI = async (e) => {
  const id = e.currentTarget.dataset.id;

  const AlertaConfirmarEliminar = await Swal.fire({
    position: "center",
    icon: "info",
    title: "¿Desea eliminar esta marca?",
    text: "¿Está completamente seguro de que desea eliminarla?",
    showConfirmButton: true,
    confirmButtonText: "Sí, Eliminar",
    confirmButtonColor: "red",
    cancelButtonText: "No, Cancelar",
    showCancelButton: true,
  });

  if (AlertaConfirmarEliminar.isConfirmed) {
    const url = `/proyecto01/marcas/eliminarAPI?id=${id}`;
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
          title: "Éxito",
          text: mensaje,
          showConfirmButton: false,
          timer: 800,
        });

        // Solo actualizar si las marcas ya estaban cargadas
        if (marcasCargadas) {
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
      console.error('Error:', error);
      Swal.fire({
        position: "center",
        icon: "error",
        title: "Error de conexión",
        text: "No se pudo conectar con el servidor",
        showConfirmButton: true,
      });
    }
  }
};

// Event Listeners
BtnBuscarMarcas.addEventListener("click", buscarAPI);
TablaMarcas.on("click", ".eliminar", eliminarAPI);
TablaMarcas.on("click", ".modificar", llenarFormulario);
FormMarcas.addEventListener("submit", guardarAPI);
BtnLimpiar.addEventListener("click", limpiarTodo);
BtnModificar.addEventListener("click", modificarAPI);