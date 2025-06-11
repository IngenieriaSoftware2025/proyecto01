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
const InputNumeroSerie = document.getElementById("numero_serie");
const InputPrecioCompra = document.getElementById("precio_compra");
const InputPrecioVenta = document.getElementById("precio_venta");

// Elementos para búsqueda
const BtnBuscarInventario = document.getElementById("BtnBuscarInventario");
const seccionInventario = document.getElementById("seccion-inventario");
const mensajeSinInventario = document.getElementById("mensaje-sin-inventario");
let inventarioCargado = false;

// FUNCIÓN PARA VALIDAR NÚMERO DE SERIE
const validarNumeroSerie = () => {
  const numeroSerie = InputNumeroSerie.value.trim().toUpperCase();

  if (numeroSerie.length < 1) {
    InputNumeroSerie.classList.remove("is-valid", "is-invalid");
    return true;
  }

  // Formato alfanumérico de 6-20 caracteres
  const esValido = /^[A-Z0-9]{6,20}$/.test(numeroSerie);

  if (!esValido) {
    InputNumeroSerie.classList.remove("is-valid");
    InputNumeroSerie.classList.add("is-invalid");

    Swal.fire({
      position: "center",
      icon: "error",
      title: "Número de serie inválido",
      text: "Use formato alfanumérico de 6-20 caracteres",
      showConfirmButton: true,
    });
    return false;
  } else {
    InputNumeroSerie.classList.remove("is-invalid");
    InputNumeroSerie.classList.add("is-valid");
    return true;
  }
};

// FUNCIÓN PARA VALIDAR PRECIOS
const validarPrecios = () => {
  const precioCompra = parseFloat(InputPrecioCompra.value);
  const precioVenta = parseFloat(InputPrecioVenta.value);

  // Limpiar validaciones previas
  InputPrecioCompra.classList.remove("is-valid", "is-invalid");
  InputPrecioVenta.classList.remove("is-valid", "is-invalid");

  let esValido = true;

  if (isNaN(precioCompra) || precioCompra <= 0) {
    InputPrecioCompra.classList.add("is-invalid");
    esValido = false;
  } else {
    InputPrecioCompra.classList.add("is-valid");
  }

  if (isNaN(precioVenta) || precioVenta <= 0) {
    InputPrecioVenta.classList.add("is-invalid");
    esValido = false;
  } else {
    InputPrecioVenta.classList.add("is-valid");
  }

  if (!esValido) {
    Swal.fire({
      position: "center",
      icon: "error",
      title: "Precios inválidos",
      text: "Los precios deben ser números mayores a 0",
      showConfirmButton: true,
    });
  }

  return esValido;
};

// INICIALIZAR DATATABLE
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
      render: (data, type, row) => `<strong>${row.marca_nombre}</strong><br><small class="text-muted">${row.marca_modelo}</small>`,
    },
    {
      title: "N° Serie",
      data: "numero_serie",
      width: "12%",
      render: (data) => `<code class="bg-light p-1 rounded">${data}</code>`
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
      className: "text-center",
      render: (data) => {
        const color = data <= 0 ? 'text-danger' : data <= 5 ? 'text-warning' : 'text-success';
        return `<span class="${color} fw-bold">${data}</span>`;
      }
    },
    {
      title: "Fecha",
      data: "fecha_ingreso",
      width: "10%"
    },
    {
      title: "Acciones",
      data: "id",
      width: "12%",
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

// FUNCIÓN PARA CARGAR MARCAS EN SELECT
const cargarMarcas = async () => {
  try {
    const url = "/proyecto01/inventario/buscarMarcasAPI";
    const respuesta = await fetch(url);
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
    Swal.fire({
      position: "center",
      icon: "error",
      title: "Error al cargar marcas",
      text: "No se pudieron cargar las marcas disponibles",
      showConfirmButton: true,
    });
  }
};

// FUNCIÓN PARA GUARDAR INVENTARIO
const guardarAPI = async (e) => {
  e.preventDefault();
  BtnGuardar.disabled = true;
  BtnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';

  // Validaciones locales
  if (!validarFormulario(FormInventario, ["id", "observaciones"])) {
    Swal.fire({
      position: "center",
      icon: "warning",
      title: "Campos requeridos",
      text: "Complete todos los campos obligatorios",
      showConfirmButton: true,
    });
    BtnGuardar.disabled = false;
    BtnGuardar.innerHTML = '<i class="bi bi-floppy me-2"></i>Agregar al Inventario';
    return;
  }

  if (!validarNumeroSerie() || !validarPrecios()) {
    BtnGuardar.disabled = false;
    BtnGuardar.innerHTML = '<i class="bi bi-floppy me-2"></i>Agregar al Inventario';
    return;
  }

  try {
    const body = new FormData(FormInventario);
    const url = "/proyecto01/inventario/guardarAPI";
    const config = {
      method: "POST",
      body,
    };

    const respuesta = await fetch(url, config);
    const datos = await respuesta.json();
    const { codigo, mensaje } = datos;

    if (codigo === 1) {
      Swal.fire({
        position: "center",
        icon: "success",
        title: "Éxito",
        text: mensaje,
        timer: 800,
        showConfirmButton: false,
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
    console.error("Error:", error);
    Swal.fire({
      position: "center",
      icon: "error",
      title: "Error de conexión",
      text: "No se pudo conectar con el servidor",
      showConfirmButton: true,
    });
  }

  BtnGuardar.disabled = false;
  BtnGuardar.innerHTML = '<i class="bi bi-floppy me-2"></i>Agregar al Inventario';
};

// FUNCIÓN PARA BUSCAR INVENTARIO
const buscarAPI = async () => {
  BtnBuscarInventario.disabled = true;
  BtnBuscarInventario.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Buscando...';

  try {
    const url = "/proyecto01/inventario/buscarAPI";
    const respuesta = await fetch(url);
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
        showConfirmButton: false,
      });
    } else {
      Swal.fire({
        position: "center",
        icon: "warning",
        title: "Sin datos",
        text: mensaje,
        timer: 800,
        showConfirmButton: false,
      });
    }
  } catch (error) {
    console.error("Error:", error);
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

// FUNCIÓN PARA LLENAR FORMULARIO
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

  window.scrollTo({ top: 0, behavior: "smooth" });
};

// FUNCIÓN PARA LIMPIAR FORMULARIO
const limpiarTodo = () => {
  FormInventario.reset();

  // Limpiar clases de validación
  const inputs = FormInventario.querySelectorAll(".form-control");
  inputs.forEach((input) => {
    input.classList.remove("is-valid", "is-invalid");
  });

  BtnGuardar.classList.remove("d-none");
  BtnModificar.classList.add("d-none");
  
  // Recargar marcas
  cargarMarcas();
};

// FUNCIÓN PARA MODIFICAR INVENTARIO
const modificarAPI = async (e) => {
  e.preventDefault();
  BtnModificar.disabled = true;
  BtnModificar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Modificando...';

  // Mismas validaciones que guardar
  if (!validarFormulario(FormInventario, ["id", "observaciones"])) {
    Swal.fire({
      position: "center",
      icon: "warning",
      title: "Campos requeridos",
      text: "Complete todos los campos obligatorios",
      showConfirmButton: true,
    });
    BtnModificar.disabled = false;
    BtnModificar.innerHTML = '<i class="bi bi-pencil me-2"></i>Modificar';
    return;
  }

  if (!validarNumeroSerie() || !validarPrecios()) {
    BtnModificar.disabled = false;
    BtnModificar.innerHTML = '<i class="bi bi-pencil me-2"></i>Modificar';
    return;
  }

  try {
    const body = new FormData(FormInventario);
    const url = "/proyecto01/inventario/modificarAPI";
    const config = {
      method: "POST",
      body,
    };

    const respuesta = await fetch(url, config);
    const datos = await respuesta.json();
    const { codigo, mensaje } = datos;

    if (codigo === 1) {
      Swal.fire({
        position: "center",
        icon: "success",
        title: "Éxito",
        text: mensaje,
        timer: 800,
        showConfirmButton: false,
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
    console.error("Error:", error);
    Swal.fire({
      position: "center",
      icon: "error",
      title: "Error de conexión",
      text: "No se pudo conectar con el servidor",
      showConfirmButton: true,
    });
  }

  BtnModificar.disabled = false;
  BtnModificar.innerHTML = '<i class="bi bi-pencil me-2"></i>Modificar';
};

// FUNCIÓN PARA ELIMINAR INVENTARIO
const eliminarAPI = async (e) => {
  const id = e.currentTarget.dataset.id;

  const confirmacion = await Swal.fire({
    title: "¿Eliminar dispositivo?",
    text: "¿Está seguro de eliminar este dispositivo del inventario?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  });

  if (confirmacion.isConfirmed) {
    try {
      const url = `/proyecto01/inventario/eliminarAPI?id=${id}`;
      const respuesta = await fetch(url);
      const datos = await respuesta.json();
      const { codigo, mensaje } = datos;

      if (codigo === 1) {
        Swal.fire({
          position: "center",
          icon: "success",
          title: "Éxito",
          text: mensaje,
          timer: 800,
          showConfirmButton: false,
        });

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
      console.error("Error:", error);
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

// CARGAR MARCAS AL INICIALIZAR
document.addEventListener('DOMContentLoaded', () => {
  cargarMarcas();
});

// EVENT LISTENERS
FormInventario.addEventListener("submit", guardarAPI);
BtnLimpiar.addEventListener("click", limpiarTodo);
BtnModificar.addEventListener("click", modificarAPI);
BtnBuscarInventario.addEventListener("click", buscarAPI);

// Validación en tiempo real
InputNumeroSerie.addEventListener("input", () => {
  // Convertir a mayúsculas automáticamente
  InputNumeroSerie.value = InputNumeroSerie.value.toUpperCase();
});

InputNumeroSerie.addEventListener("blur", validarNumeroSerie);
InputPrecioCompra.addEventListener("blur", validarPrecios);
InputPrecioVenta.addEventListener("blur", validarPrecios);

// Event listeners para tabla
TablaInventario.on("click", ".eliminar", eliminarAPI);
TablaInventario.on("click", ".modificar", llenarFormulario);

console.log("Módulo de inventario con validaciones completas cargado exitosamente");