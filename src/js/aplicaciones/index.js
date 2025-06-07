import DataTable from "datatables.net-bs5";
import { validarFormulario, Toast } from "../funciones";
import { lenguaje } from "../lenguaje";
import Swal from "sweetalert2";

// Referencias a elementos del DOM
const formAplicacion = document.getElementById('formAplicacion');
const btnGuardarApp = document.getElementById('BtnGuardarApp');
const btnModificarApp = document.getElementById('BtnModificarApp');
const btnLimpiarApp = document.getElementById('BtnLimpiarApp');
const btnBuscarApps = document.getElementById('BtnBuscarApps');
const seccion_aplicaciones = document.getElementById('seccion_aplicaciones');
const mensaje_sin_apps = document.getElementById('mensaje_sin_apps');

// Variables globales
let tabla_aplicaciones = null;
let aplicaciones_cargadas = false;

// Validación personalizada de campos
const validarCamposApp = () => {
    const errores = [];
    
    if (!formAplicacion.app_nombre_largo.value.trim()) {
        errores.push('El nombre largo es obligatorio');
    }
    
    if (!formAplicacion.app_nombre_medium.value.trim()) {
        errores.push('El nombre medium es obligatorio');
    }
    
    if (!formAplicacion.app_nombre_corto.value.trim()) {
        errores.push('El nombre corto es obligatorio');
    }
    
    return errores;
};

// Función para mostrar errores
const mostrarErroresApp = (errores) => {
    const listaErrores = errores.map(error => `• ${error}`).join('<br>');
    
    Swal.fire({
        icon: 'error',
        title: 'Errores en el formulario',
        html: listaErrores,
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#dc3545'
    });
};

// Función para limpiar estilos de validación
const limpiarValidacionApp = () => {
    const inputs = formAplicacion.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.classList.remove('is-invalid', 'is-valid');
    });
};

// Función principal de guardar aplicación
const guardarAplicacion = async (e) => {
    e.preventDefault();
    
    limpiarValidacionApp();
    
    // Validar campos
    const errores = validarCamposApp();
    if (errores.length > 0) {
        mostrarErroresApp(errores);
        return;
    }
    
    btnGuardarApp.disabled = true;
    btnGuardarApp.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
    
    try {
        const body = new FormData(formAplicacion);
        const respuesta = await fetch('/proyecto01/aplicaciones/guardar', {
            method: 'POST',
            body
        });
        
        const data = await respuesta.json();
        
        if (data.codigo === 1) {
            Toast.fire({
                icon: 'success',
                title: data.mensaje
            });
            
            limpiarFormularioApp();
            
            if (aplicaciones_cargadas) {
                buscarAplicaciones();
            }
            
        } else {
            Toast.fire({
                icon: 'error',
                title: data.mensaje
            });
        }
        
    } catch (error) {
        console.error('Error:', error);
        Toast.fire({
            icon: 'error',
            title: 'Error de conexión con el servidor'
        });
    } finally {
        btnGuardarApp.disabled = false;
        btnGuardarApp.innerHTML = '<i class="bi bi-floppy me-2"></i>Guardar Aplicación';
    }
};

// Función para buscar aplicaciones
const buscarAplicaciones = async () => {
    try {
        btnBuscarApps.disabled = true;
        btnBuscarApps.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Cargando...';
        
        const respuesta = await fetch('/proyecto01/aplicaciones/buscarAPI');
        const data = await respuesta.json();
        
        if (data.codigo === 1) {
            mostrarTablaAplicaciones(data.data);
            aplicaciones_cargadas = true;
        } else {
            Toast.fire({
                icon: 'error',
                title: 'Error al cargar aplicaciones'
            });
        }
        
    } catch (error) {
        console.error('Error:', error);
        Toast.fire({
            icon: 'error',
            title: 'Error de conexión al buscar aplicaciones'
        });
    } finally {
        btnBuscarApps.disabled = false;
        btnBuscarApps.innerHTML = '<i class="bi bi-search me-2"></i>Buscar Aplicaciones';
    }
};

// Función para mostrar tabla de aplicaciones
const mostrarTablaAplicaciones = (aplicaciones) => {
    seccion_aplicaciones.classList.remove('d-none');
    seccion_aplicaciones.classList.add('fade-in');
    mensaje_sin_apps.classList.add('d-none');
    
    if (tabla_aplicaciones) {
        tabla_aplicaciones.destroy();
        document.getElementById('TableAplicaciones').innerHTML = '';
    }
    
    tabla_aplicaciones = new DataTable('#TableAplicaciones', {
        data: aplicaciones,
        language: lenguaje,
        responsive: true,
        pageLength: 10,
        order: [[0, 'desc']],
        columns: [
            {
                title: 'ID',
                data: 'app_id',
                width: '10%'
            },
            {
                title: 'Nombre Largo',
                data: 'app_nombre_largo',
                width: '30%'
            },
            {
                title: 'Nombre Medium',
                data: 'app_nombre_medium',
                width: '25%'
            },
            {
                title: 'Nombre Corto',
                data: 'app_nombre_corto',
                width: '15%'
            },
            {
                title: 'Estado',
                data: 'app_situacion',
                width: '10%',
                render: (data) => data == 1 
                    ? '<span class="badge bg-success">Activo</span>'
                    : '<span class="badge bg-danger">Inactivo</span>'
            },
            {
                title: 'Acciones',
                data: 'app_id',
                width: '10%',
                orderable: false,
                render: (data, type, row) => {
                    return `<div class='d-flex justify-content-center'>
                                <button class='btn btn-warning btn-sm modificar-app mx-1' 
                                    data-id="${data}" 
                                    data-largo="${row.app_nombre_largo}"
                                    data-medium="${row.app_nombre_medium}"
                                    data-corto="${row.app_nombre_corto}">
                                    <i class='bi bi-pencil me-1'></i>Editar
                                </button>
                                <button class='btn btn-danger btn-sm eliminar-app mx-1' data-id="${data}">
                                    <i class="bi bi-trash me-1"></i>Eliminar
                                </button>
                            </div>`;
                }
            }
        ]
    });
    
    // Event listeners para botones
    setTimeout(() => {
        document.querySelectorAll('.modificar-app').forEach(btn => {
            btn.addEventListener('click', llenarFormularioApp);
        });
        
        document.querySelectorAll('.eliminar-app').forEach(btn => {
            btn.addEventListener('click', eliminarAplicacion);
        });
    }, 100);
};

// Función para llenar formulario para modificar
const llenarFormularioApp = (e) => {
    const datos = e.currentTarget.dataset;
    
    document.getElementById('app_id').value = datos.id;
    document.getElementById('app_nombre_largo').value = datos.largo;
    document.getElementById('app_nombre_medium').value = datos.medium;
    document.getElementById('app_nombre_corto').value = datos.corto;
    
    btnGuardarApp.classList.add('d-none');
    btnModificarApp.classList.remove('d-none');
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

// Función para modificar aplicación
const modificarAplicacion = async (e) => {
    e.preventDefault();
    
    const errores = validarCamposApp();
    if (errores.length > 0) {
        mostrarErroresApp(errores);
        return;
    }
    
    btnModificarApp.disabled = true;
    btnModificarApp.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Modificando...';
    
    try {
        const body = new FormData(formAplicacion);
        const respuesta = await fetch('/proyecto01/aplicaciones/modificarAPI', {
            method: 'POST',
            body
        });
        
        const data = await respuesta.json();
        
        if (data.codigo === 1) {
            Toast.fire({ icon: 'success', title: data.mensaje });
            limpiarFormularioApp();
            buscarAplicaciones();
        } else {
            Toast.fire({ icon: 'error', title: data.mensaje });
        }
    } catch (error) {
        Toast.fire({ icon: 'error', title: 'Error de conexión' });
    } finally {
        btnModificarApp.disabled = false;
        btnModificarApp.innerHTML = '<i class="bi bi-pencil me-2"></i>Modificar';
    }
};

// Función para eliminar aplicación
const eliminarAplicacion = async (e) => {
    const id = e.currentTarget.dataset.id;
    
    const confirmacion = await Swal.fire({
        title: '¿Eliminar aplicación?',
        text: '¿Estás seguro de eliminar esta aplicación?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });
    
    if (confirmacion.isConfirmed) {
        try {
            const respuesta = await fetch(`/proyecto01/aplicaciones/eliminarAPI?id=${id}`);
            const data = await respuesta.json();
            
            if (data.codigo === 1) {
                Toast.fire({ icon: 'success', title: data.mensaje });
                buscarAplicaciones();
            } else {
                Toast.fire({ icon: 'error', title: data.mensaje });
            }
        } catch (error) {
            Toast.fire({ icon: 'error', title: 'Error de conexión' });
        }
    }
};

// Función para limpiar formulario
const limpiarFormularioApp = () => {
    formAplicacion.reset();
    limpiarValidacionApp();
    btnGuardarApp.classList.remove('d-none');
    btnModificarApp.classList.add('d-none');
};

// Event listeners principales
formAplicacion.addEventListener('submit', (e) => {
    if (btnModificarApp.classList.contains('d-none')) {
        guardarAplicacion(e);
    } else {
        modificarAplicacion(e);
    }
});

btnModificarApp.addEventListener('click', modificarAplicacion);
btnBuscarApps.addEventListener('click', buscarAplicaciones);
btnLimpiarApp.addEventListener('click', limpiarFormularioApp);

console.log('Sistema de aplicaciones inicializado correctamente');