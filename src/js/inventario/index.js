console.log("Modulo de Inventario Cargado");
import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones";
import { lenguaje } from "../lenguaje";

// Elementos DOM principales
const FormInventario = document.getElementById("FormInventario");
const BtnGuardar = document.getElementById("BtnGuardar");
const BtnModificar = document.getElementById("BtnModificar");
const BtnLimpiar = document.getElementById("BtnLimpiar");
const selectMarca = document.getElementById("marca_id");

// Elementos para búsqueda manual
const BtnBuscarInventario = document.getElementById("BtnBuscarInventario");
const seccionInventario = document.getElementById("seccion-inventario");
const mensajeSinInventario = document.getElementById("mensaje-sin-inventario");
let inventarioCargado = false;

// Inicializar DataTable
const TablaInventario = new DataTable("#TableInventario", {
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
      width: "5%",
      render: (data, type, row, meta) => meta.row + 1,
    },
    {
      title: "Marca/Modelo",
      data: "marca_nombre",
      width: "18%",
      render: (data, type, row) => `${row.marca_nombre}<br><small class="text-muted">${row.marca_modelo}</small>`,
    },
    {
      title: "N° Serie",
      data: "numero_serie",
      width: "12%"
    },
    {
      title: "Estado Disp.",
      data: "estado_dispositivo",
      width: "10%",
      render: (data) => {
        const badges = {
          'NUEVO': '<span class="badge bg-success">Nuevo</span>',
          'USADO': '<span class="badge bg-warning">Usado</span>',
          'REPARADO': '<span class="badge bg-info">Reparado</span>'
        };
        return badges[data] || data;
      }
    },
    {
      title: "Estado Inv.",
      data: "estado_inventario",
      width: "10%",
      render: (data) => {
        const badges = {
          'DISPONIBLE': '<span class="badge bg-primary">Disponible</span>',
          'VENDIDO': '<span class="badge bg-secondary">Vendido</span>',
          'EN_REPARACION': '<span class="badge bg-warning">En Reparación</span>'
        };
        return badges[data] || data;
      }
    },
    {
      title: "P. Compra",
      data: "precio_compra",
      width: "10%",
      render: (data) => `Q${parseFloat(data).toFixed(2)}`
    },
    {
      title: "P. Venta",
      data: "precio_venta",
      width: "10%",
      render: (data) => `Q${parseFloat(data).toFixed(2)}`
    },
    {
      title: "Stock",
      data: "stock_disponible",
      width: "8%",
      className: "text-center"
    },
    {
      title: "Fecha",
      data: "fecha_ingreso",
      width: "10%"
    },
    {
      title: "Acciones",
      data: "id",
      width: "15%",
      searchable: false,
      orderable: false,
      render: (data, type, row) => {
        return `
                <div class='d-flex justify-content-center'>
                         <button class='btn btn-warning btn-sm modificar mx-1' 
                             data-id="${data}" 
                             data-marca_id="${row.marca_id || ''}"
                             data-numero_serie="${row.numero_serie}"  
                             data-precio_compra="${row.precio_compra}"
                             data-precio_venta="${row.precio_venta}"
                             data-stock_disponible="${row.stock_disponible}"
                             data-estado_dispositivo="${row.estado_dispositivo}"
                             data-observaciones="${row.observaciones || ''}"
                             title="Modificar dispositivo">
                             <i class='bi bi-pencil-square'></i>
                         </button>
                         <button class='btn btn-danger btn-sm eliminar mx-1' 
                             data-id="${data}"
                             title="Eliminar dispositivo">
                            <i class="bi bi-trash3"></i>
                         </button>
                     </div>`;
      },
    },
  ],
});


const cargarMarcas = async () => {
  const url = "/proyecto01/inventario/buscarMarcasAPI";
  const config = {
    method: "GET",
  };

  try {
    const respuesta = await fetch(url, config);
    const datos = await respuesta.json();
    const { codigo, data } = datos;

    if (codigo === 1) {
      selectMarca.innerHTML = '<option value="">Seleccione una marca</option>';
      data.forEach(marca => {
        const option = document.createElement('option');
        option.value = marca.id;
        option.textContent = `${marca.nombre} - ${marca.modelo}`;
        selectMarca.appendChild(option);
      });
    }
  } catch (error) {
    console.error('Error al cargar marcas:', error);
  }
};


const guardarAPI = async (e) => {
  e.preventDefault();
  BtnGuardar.disabled = true;

  if (!validarFormulario(FormInventario, ["id"])) {
    Swal.fire({
      position: "center",
      icon: "warning",
      title: "Campos obligatorios",
      text: "Complete todos los campos requeridos",
      showConfirmButton: true,
    });
    BtnGuardar.disabled = false;
    return;
  }

  const body = new FormData(FormInventario);
  const url = "/proyecto01/inventario/guardarAPI";
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
        showConfirmButton: true,
      });
      limpiarTodo();
      
      if (inventarioCargado) {
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

const buscarAPI = async () => {
  BtnBuscarInventario.disabled = true;
  BtnBuscarInventario.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Buscando...';

  const url = "/proyecto01/inventario/buscarAPI";
  const config = {
    method: "GET",
  };

  try {
    const respuesta = await fetch(url, config);
    const datos = await respuesta.json();
    const { codigo, mensaje, data } = datos;

    if (codigo === 1) {
      seccionInventario.classList.remove("d-none");
      seccionInventario.classList.add("fade-in");
      mensajeSinInventario.classList.add("d-none");

      TablaInventario.clear().draw();
      TablaInventario.rows.add(data).draw();
      inventarioCargado = true;

      Swal.fire({
        position: "center",
        icon: "success",
        title: "Éxito",
        text: `${data.length} dispositivo(s) en inventario`,
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
    BtnBuscarInventario.disabled = false;
    BtnBuscarInventario.innerHTML = '<i class="bi bi-search me-2"></i>Buscar Inventario';
  }
};

const llenarFormulario = (e) => {
  const datos = e.currentTarget.dataset;

  document.getElementById("id").value = datos.id;
  document.getElementById("marca_id").value = datos.marca_id;
  document.getElementById("numero_serie").value = datos.numero_serie;
  document.getElementById("precio_compra").value = datos.precio_compra;
  document.getElementById("precio_venta").value = datos.precio_venta;
  document.getElementById("stock_disponible").value = datos.stock_disponible;
  document.getElementById("estado_dispositivo").value = datos.estado_dispositivo;
  document.getElementById("observaciones").value = datos.observaciones;

  BtnGuardar.classList.add("d-none");
  BtnModificar.classList.remove("d-none");

  window.scrollTo({
    top: 0,
  });
};


const limpiarTodo = () => {
  FormInventario.reset();
  BtnGuardar.classList.remove("d-none");
  BtnModificar.classList.add("d-none");
};

const modificarAPI = async (e) => {
  e.preventDefault();
  BtnModificar.disabled = true;

  if (!validarFormulario(FormInventario, ["id"])) {
    Swal.fire({
      position: "center",
      icon: "warning",
      title: "Campos obligatorios",
      text: "Complete todos los campos requeridos",
      showConfirmButton: false,
      timer: 800,
    });
    BtnModificar.disabled = false;
    return;
  }

  const body = new FormData(FormInventario);
  const url = "/proyecto01/inventario/modificarAPI";
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
      
      if (inventarioCargado) {
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

const eliminarAPI = async (e) => {
  const id = e.currentTarget.dataset.id;

  const AlertaConfirmarEliminar = await Swal.fire({
    position: "center",
    icon: "info",
    title: "¿Eliminar dispositivo del inventario?",
    text: "¿Está completamente seguro de eliminar este dispositivo?",
    showConfirmButton: true,
    confirmButtonText: "Sí, Eliminar",
    confirmButtonColor: "red",
    cancelButtonText: "No, Cancelar",
    showCancelButton: true,
  });

  if (AlertaConfirmarEliminar.isConfirmed) {
    const url = `/proyecto01/inventario/eliminarAPI?id=${id}`;
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

        if (inventarioCargado) {
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

document.addEventListener('DOMContentLoaded', () => {
  cargarMarcas();
});

// Event Listeners
BtnBuscarInventario.addEventListener("click", buscarAPI);
TablaInventario.on("click", ".eliminar", eliminarAPI);
TablaInventario.on("click", ".modificar", llenarFormulario);
FormInventario.addEventListener("submit", guardarAPI);
BtnLimpiar.addEventListener("click", limpiarTodo);
BtnModificar.addEventListener("click", modificarAPI);