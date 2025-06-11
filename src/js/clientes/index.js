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
const InputTelefono = document.getElementById("telefono");
const InputDocumento = document.getElementById("nit");

// Elementos para búsqueda
const BtnBuscarClientes = document.getElementById("BtnBuscarClientes");
const seccionClientes = document.getElementById("seccion-clientes");
const mensajeSinClientes = document.getElementById("mensaje-sin-clientes");
let clientesCargados = false;

// FUNCIÓN PARA VALIDAR TELÉFONO GUATEMALTECO
const validarTelefono = () => {
  const telefono = InputTelefono.value.trim();

  if (telefono.length < 1) {
    InputTelefono.classList.remove("is-valid", "is-invalid");
    return true;
  }

  // Verificar que tenga 8 dígitos y comience con 2,3,4,5,6,7,8
  const esValido = /^[2-8]\d{7}$/.test(telefono);

  if (!esValido) {
    InputTelefono.classList.remove("is-valid");
    InputTelefono.classList.add("is-invalid");

    Swal.fire({
      position: "center",
      icon: "error",
      title: "Teléfono inválido",
      text: "Debe tener 8 dígitos y comenzar con 2,3,4,5,6,7 u 8",
      showConfirmButton: true,
    });
    return false;
  } else {
    InputTelefono.classList.remove("is-invalid");
    InputTelefono.classList.add("is-valid");
    return true;
  }
};

// FUNCIÓN PARA VALIDAR NIT GUATEMALTECO (CORREGIDA)
const validarNit = (nit) => {
  const nitLimpio = nit.trim();
  let nd, add = 0;

  if (nd = /^(\d+)-?([\dkK])$/.exec(nitLimpio)) {
    nd[2] = (nd[2].toLowerCase() === 'k') ? 10 : parseInt(nd[2], 10);

    for (let i = 0; i < nd[1].length; i++) {
      add += ((((i - nd[1].length) * -1) + 1) * parseInt(nd[1][i], 10));
    }
    return ((11 - (add % 11)) % 11) === nd[2];
  } else {
    return false;
  }
};

// FUNCIÓN PARA DETECTAR Y VALIDAR DOCUMENTO (SOLO NIT)
const validarDocumento = () => {
  const documento = InputDocumento.value.trim();

  if (documento.length < 1) {
    InputDocumento.classList.remove("is-valid", "is-invalid");
    return { valido: true, tipo: null };
  }

  let esValido = false;
  let tipo = null;
  let mensaje = "";

  // VALIDAR ÚNICAMENTE NIT
  if (/^[\d]+-?[\dkK]$/.test(documento)) {
    esValido = validarNit(documento);
    tipo = "NIT";
    mensaje = esValido ? "NIT válido" : "NIT inválido - Verifique el formato";
  } else {
    esValido = false;
    mensaje = "Formato no válido. Use formato NIT (123456-7)";
  }

  // Aplicar estilos de validación
  if (esValido) {
    InputDocumento.classList.remove("is-invalid");
    InputDocumento.classList.add("is-valid");
  } else {
    InputDocumento.classList.remove("is-valid");
    InputDocumento.classList.add("is-invalid");

    Swal.fire({
      position: "center",
      icon: "error",
      title: "NIT inválido",
      text: mensaje,
      showConfirmButton: true,
    });
  }

  return { valido: esValido, tipo: tipo };
};

// INICIALIZAR DATATABLE
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
      width: "5%",
      render: (data, type, row, meta) => meta.row + 1,
    },
    {
      title: "Nombre Completo",
      data: "nombre",
      width: "20%",
      render: (data, type, row) => `${row.nombre} ${row.apellido}`,
    },
    {
      title: "Teléfono",
      data: "telefono",
      width: "12%",
    },
    {
      title: "NIT",
      data: "nit",
      width: "15%",
      render: (data, type, row) => {
        if (data) {
          return `<span class="badge bg-info">NIT</span><br>${data}`;
        } else {
          return '<span class="text-muted">Sin NIT</span>';
        }
      },
    },
    {
      title: "Correo",
      data: "correo",
      width: "18%",
      render: (data) => data || '<span class="text-muted">Sin correo</span>',
    },
    {
      title: "Estado",
      data: "situacion",
      width: "10%",
      render: (data, type, row) => {
        const badges = {
          1: '<span class="badge bg-success estado-badge">Activo</span>',
          2: '<span class="badge bg-secondary estado-badge">Inactivo</span>',
          3: '<span class="badge bg-danger estado-badge">Moroso</span>',
        };
        return (
          badges[data] ||
          '<span class="badge bg-warning estado-badge">Desconocido</span>'
        );
      },
    },
    {
      title: "Acciones",
      data: "id",
      width: "20%",
      searchable: false,
      orderable: false,
      render: (data, type, row) => {
        return `
                    <div class='d-flex justify-content-center'>
                        <button class='btn btn-warning btn-sm modificar mx-1' 
                            data-id="${data}" 
                            data-nombre="${row.nombre}"  
                            data-apellido="${row.apellido}"
                            data-telefono="${row.telefono}"
                            data-nit="${row.nit || ""}"
                            data-correo="${row.correo || ""}"
                            data-situacion="${row.situacion}"
                            title="Modificar cliente">
                            <i class='bi bi-pencil-square'></i>
                        </button>
                        <button class='btn btn-danger btn-sm eliminar mx-1' 
                            data-id="${data}"
                            title="Eliminar cliente">
                           <i class="bi bi-trash3"></i>
                        </button>
                    </div>`;
      },
    },
  ],
});

// FUNCIÓN PARA GUARDAR CLIENTE
const guardarAPI = async (e) => {
  e.preventDefault();
  BtnGuardar.disabled = true;
  BtnGuardar.innerHTML =
    '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';

  // Validaciones locales
  if (!validarFormulario(FormClientes, ["id", "nit", "correo"])) {
    Swal.fire({
      position: "center",
      icon: "warning",
      title: "Campos requeridos",
      text: "Complete todos los campos obligatorios",
      showConfirmButton: true,
    });
    BtnGuardar.disabled = false;
    BtnGuardar.innerHTML = '<i class="bi bi-floppy me-2"></i>Guardar Cliente';
    return;
  }

  if (!validarTelefono()) {
    BtnGuardar.disabled = false;
    BtnGuardar.innerHTML = '<i class="bi bi-floppy me-2"></i>Guardar Cliente';
    return;
  }

  const validacionDoc = validarDocumento();
  if (InputDocumento.value.trim() && !validacionDoc.valido) {
    BtnGuardar.disabled = false;
    BtnGuardar.innerHTML = '<i class="bi bi-floppy me-2"></i>Guardar Cliente';
    return;
  }

  try {
    const body = new FormData(FormClientes);
    const url = "/proyecto01/clientes/guardarAPI";
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

      if (clientesCargados) {
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
  BtnGuardar.innerHTML = '<i class="bi bi-floppy me-2"></i>Guardar Cliente';
};

// FUNCIÓN PARA BUSCAR CLIENTES
const buscarAPI = async () => {
  BtnBuscarClientes.disabled = true;
  BtnBuscarClientes.innerHTML =
    '<span class="spinner-border spinner-border-sm me-2"></span>Buscando...';

  try {
    const url = "/proyecto01/clientes/buscarAPI";
    const respuesta = await fetch(url);
    const datos = await respuesta.json();
    const { codigo, mensaje, data } = datos;

    if (codigo === 1) {
      seccionClientes.classList.remove("d-none");
      seccionClientes.classList.add("fade-in");
      mensajeSinClientes.classList.add("d-none");

      TablaClientes.clear().draw();
      TablaClientes.rows.add(data).draw();
      clientesCargados = true;

      Swal.fire({
        position: "center",
        icon: "success",
        title: "Éxito",
        text: `${data.length} cliente(s) encontrado(s)`,
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
    BtnBuscarClientes.disabled = false;
    BtnBuscarClientes.innerHTML =
      '<i class="bi bi-search me-2"></i>Buscar Clientes';
  }
};

// FUNCIÓN PARA LLENAR FORMULARIO
const llenarFormulario = (e) => {
  const datos = e.currentTarget.dataset;

  document.getElementById("id").value = datos.id;
  document.getElementById("nombre").value = datos.nombre;
  document.getElementById("apellido").value = datos.apellido;
  document.getElementById("telefono").value = datos.telefono;
  document.getElementById("nit").value = datos.nit;
  document.getElementById("correo").value = datos.correo;
  document.getElementById("situacion").value = datos.situacion;

  BtnGuardar.classList.add("d-none");
  BtnModificar.classList.remove("d-none");

  window.scrollTo({ top: 0, behavior: "smooth" });
};

// FUNCIÓN PARA LIMPIAR FORMULARIO
const limpiarTodo = () => {
  FormClientes.reset();

  // Limpiar clases de validación
  const inputs = FormClientes.querySelectorAll(".form-control");
  inputs.forEach((input) => {
    input.classList.remove("is-valid", "is-invalid");
  });

  BtnGuardar.classList.remove("d-none");
  BtnModificar.classList.add("d-none");
};

// FUNCIÓN PARA MODIFICAR CLIENTE
const modificarAPI = async (e) => {
  e.preventDefault();
  BtnModificar.disabled = true;
  BtnModificar.innerHTML =
    '<span class="spinner-border spinner-border-sm me-2"></span>Modificando...';

  // Mismas validaciones que guardar
  if (!validarFormulario(FormClientes, ["id", "nit", "correo"])) {
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

  if (!validarTelefono()) {
    BtnModificar.disabled = false;
    BtnModificar.innerHTML = '<i class="bi bi-pencil me-2"></i>Modificar';
    return;
  }

  const validacionDoc = validarDocumento();
  if (InputDocumento.value.trim() && !validacionDoc.valido) {
    BtnModificar.disabled = false;
    BtnModificar.innerHTML = '<i class="bi bi-pencil me-2"></i>Modificar';
    return;
  }

  try {
    const body = new FormData(FormClientes);
    const url = "/proyecto01/clientes/modificarAPI";
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

      if (clientesCargados) {
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

// FUNCIÓN PARA ELIMINAR CLIENTE
const eliminarAPI = async (e) => {
  const id = e.currentTarget.dataset.id;

  const confirmacion = await Swal.fire({
    title: "¿Eliminar cliente?",
    text: "¿Está seguro de eliminar este cliente?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  });

  if (confirmacion.isConfirmed) {
    try {
      const url = `/proyecto01/clientes/eliminarAPI?id=${id}`;
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

        if (clientesCargados) {
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

// EVENT LISTENERS
FormClientes.addEventListener("submit", guardarAPI);
BtnLimpiar.addEventListener("click", limpiarTodo);
BtnModificar.addEventListener("click", modificarAPI);
BtnBuscarClientes.addEventListener("click", buscarAPI);

// Validación en tiempo real
InputTelefono.addEventListener("input", () => {
  // Permitir solo números
  InputTelefono.value = InputTelefono.value.replace(/\D/g, "");
  if (InputTelefono.value.length > 8) {
    InputTelefono.value = InputTelefono.value.slice(0, 8);
  }
});

InputTelefono.addEventListener("blur", validarTelefono);
InputDocumento.addEventListener("blur", validarDocumento);

// Event listeners para tabla
TablaClientes.on("click", ".eliminar", eliminarAPI);
TablaClientes.on("click", ".modificar", llenarFormulario);

console.log("Módulo de clientes con validación NIT corregida cargado exitosamente");