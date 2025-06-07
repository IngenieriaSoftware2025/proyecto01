import DataTable from "datatables.net-bs5";
import { validarFormulario, Toast } from "../funciones";
import { lenguaje } from "../lenguaje";
import Swal from "sweetalert2";

// Referencias a elementos del DOM
const formPermiso = document.getElementById('formPermiso');
const btnGuardarPermiso = document.getElementById('BtnGuardarPermiso');
const btnModificarPermiso = document.getElementById('BtnModificarPermiso');
const btnLimpiarPermiso = document.getElementById('BtnLimpiarPermiso');
const btnBuscarPermisos = document.getElementById('BtnBuscarPermisos');
const seccion_permisos = document.getElementById('seccion_permisos');
const mensaje_sin_permisos = document.getElementById('mensaje_sin_permisos');
const selectAplicacion = document.getElementById('permiso_app_id');

// Variables globales
let tabla_permisos = null;
let permisos_cargados = false;

// Cargar aplicaciones al inicializar
document.addEventListener('DOMContentLoaded', () => {
    cargarAplicaciones();
});

// Función para cargar aplicaciones en el select
const cargarAplicaciones = async () => {
    try {
        const respuesta = await fetch('/proyecto01/permisos/buscarAplicacionesAPI');
        const data = await respuesta.json();
        
        if (data.codigo === 1) {
            selectAplicacion.innerHTML = '<option value="">Seleccione una aplicación...</option>';
            
            data.data.forEach(app => {
                const option = document.createElement('option');
                option.value = app.app_id;
                option.textContent = app.app_nombre_corto;
                selectAplicacion.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error cargando aplicaciones:', error);
    }
};

// Validación personalizada de campos
const validarCamposPermiso = () => {
    const errores = [];
    
    if (!formPermiso.permiso_app_id.value) {
        errores.push('Debe seleccionar una aplicación');
    }
    
    if (!formPermiso.permiso_nombre.value.trim()) {
        errores.push('El nombre del permiso es obligatorio');
    }
    
    if (!formPermiso.permiso_clave.value.trim()) {
        errores.push('La clave del permiso es obligatoria');
    }
    
    if (!formPermiso.permiso_desc.value.trim()) {
        errores.push('La descripción del permiso es obligatoria');
    }
    
    return errores;
};

// Función para mostrar errores
const mostrarErroresPermiso = (errores) => {
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
const limpiarValidacionPermiso = () => {
    const inputs = formPermiso.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.classList.remove('is-invalid', 'is-valid');
    });
};

// Función principal de guardar permiso
const guardarPermiso = async (e) => {
    e.preventDefault();
    
    limpiarValidacionPermiso();
    
    // Validar campos
    const errores = validarCamposPermiso();
    if (errores.length > 0) {
        mostrarErroresPermiso(errores);
        return;
    }
    
    btnGuardarPermiso.disabled = true;
    btnGuardarPermiso.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
    
    try {
        const body = new FormData(formPermiso);
        const respuesta = await fetch('/proyecto01/permisos/guardar', {
            method: 'POST',
            body
        });
        
        const data = await respuesta.json();
        
        if (data.codigo === 1) {
            Toast.fire({
                icon: 'success',
                title: data.mensaje
            });
            
            limpiarFormularioPermiso();
            
            if (permisos_cargados) {
                buscarPermisos();
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
        btnGuardarPermiso.disabled = false;
        btnGuardarPermiso.innerHTML = '<i class="bi bi-floppy me-2"></i>Guardar Permiso';
    }
};

// Función para buscar permisos
const buscarPermisos = async () => {
    try {
        btnBuscarPermisos.disabled = true;
        btnBuscarPermisos.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Cargando...';
        
        const respuesta = await fetch('/proyecto01/permisos/buscarAPI');
        const data = await respuesta.json();
        
        if (data.codigo === 1) {
            mostrarTablaPermisos(data.data);
            permisos_cargados = true;
        } else {
            Toast.fire({
                icon: 'error',
                title: 'Error al cargar permisos'
            });
        }
        
    } catch (error) {
        console.error('Error:', error);
        Toast.fire({
            icon: 'error',
            title: 'Error de conexión al buscar permisos'
        });
    } finally {
        btnBuscarPermisos.disabled = false;
        btnBuscarPermisos.innerHTML = '<i class="bi bi-search me-2"></i>Buscar Permisos';
    }
};

// Función para mostrar tabla de permisos
const mostrarTablaPermisos = (permisos) => {
    seccion_permisos.classList.remove('d-none');
    seccion_permisos.classList.add('fade-in');
    mensaje_sin_permisos.classList.add('d-none');
    
    if (tabla_permisos) {
        tabla_permisos.destroy();
        document.getElementById('TablePermisos').innerHTML = '';
    }
    
    tabla_permisos = new DataTable('#TablePermisos', {
        data: permisos,
        language: lenguaje,
        responsive: true,
        pageLength: 10,
        order: [[0, 'desc']],
        columns: [
            {
                title: 'ID',
                data: 'permiso_id',
                width: '5%'
            },
            {
                title: 'Aplicación',
                data: 'app_nombre_corto',
                width: '15%',
                render: (data) => data || 'Sin aplicación'
            },
            {
                title: 'Nombre del Permiso',
                data: 'permiso_nombre',
                width: '20%'
            },
            {
                title: 'Clave',
                data: 'permiso_clave',
                width: '20%',
                render: (data) => `<code class="bg-light p-1 rounded">${data}</code>`
            },
            {
                title: 'Descripción',
                data: 'permiso_desc',
                width: '25%',
                render: (data) => {
                    return data && data.length > 50 ? data.substring(0, 50) + '...' : data;
                }
            },
            {
                title: 'Estado',
                data: 'permiso_situacion',
                width: '10%',
                render: (data) => data == 1 
                    ? '<span class="badge bg-success">Activo</span>'
                    : '<span class="badge bg-danger">Inactivo</span>'
            },
            {
                title: 'Acciones',
                data: 'permiso_id',
                width: '5%',
                orderable: false,
                render: (data, type, row) => {
                    return `<div class='d-flex justify-content-center'>
                                <button class='btn btn-warning btn-sm modificar-permiso mx-1' 
                                    data-id="${data}" 
                                    data-app="${row.permiso_app_id}"
                                    data-nombre="${row.permiso_nombre}"
                                    data-clave="${row.permiso_clave}"
                                    data-desc="${row.permiso_desc}">
                                    <i class='bi bi-pencil me-1'></i>
                                </button>
                                <button class='btn btn-danger btn-sm eliminar-permiso mx-1' data-id="${data}">
                                    <i class="bi bi-trash me-1"></i>
                                </button>
                            </div>`;
                }
            }
        ]
    });
    
    // Event listeners para botones
    setTimeout(() => {
        document.querySelectorAll('.modificar-permiso').forEach(btn => {
            btn.addEventListener('click', llenarFormularioPermiso);
        });
        
        document.querySelectorAll('.eliminar-permiso').forEach(btn => {
            btn.addEventListener('click', eliminarPermiso);
        });
    }, 100);
};

// Función para llenar formulario para modificar
const llenarFormularioPermiso = (e) => {
    const datos = e.currentTarget.dataset;
    
    document.getElementById('permiso_id').value = datos.id;
    document.getElementById('permiso_app_id').value = datos.app;
    document.getElementById('permiso_nombre').value = datos.nombre;
    document.getElementById('permiso_clave').value = datos.clave;
    document.getElementById('permiso_desc').value = datos.desc;
    
    btnGuardarPermiso.classList.add('d-none');
    btnModificarPermiso.classList.remove('d-none');
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

// Función para modificar permiso
const modificarPermiso = async (e) => {
    e.preventDefault();
    
    const errores = validarCamposPermiso();
    if (errores.length > 0) {
        mostrarErroresPermiso(errores);
        return;
    }
    
    btnModificarPermiso.disabled = true;
    btnModificarPermiso.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Modificando...';
    
    try {
        const body = new FormData(formPermiso);
        const respuesta = await fetch('/proyecto01/permisos/modificarAPI', {
            method: 'POST',
            body
        });
        
        const data = await respuesta.json();
        
        if (data.codigo === 1) {
            Toast.fire({ icon: 'success', title: data.mensaje });
            limpiarFormularioPermiso();
            buscarPermisos();
        } else {
            Toast.fire({ icon: 'error', title: data.mensaje });
        }
    } catch (error) {
        Toast.fire({ icon: 'error', title: 'Error de conexión' });
    } finally {
        btnModificarPermiso.disabled = false;
        btnModificarPermiso.innerHTML = '<i class="bi bi-pencil me-2"></i>Modificar';
    }
};

// Función para eliminar permiso
const eliminarPermiso = async (e) => {
    const id = e.currentTarget.dataset.id;
    
    const confirmacion = await Swal.fire({
        title: '¿Eliminar permiso?',
        text: '¿Estás seguro de eliminar este permiso?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });
    
    if (confirmacion.isConfirmed) {
        try {
            const respuesta = await fetch(`/proyecto01/permisos/eliminarAPI?id=${id}`);
            const data = await respuesta.json();
            
            if (data.codigo === 1) {
                Toast.fire({ icon: 'success', title: data.mensaje });
                buscarPermisos();
            } else {
                Toast.fire({ icon: 'error', title: data.mensaje });
            }
        } catch (error) {
            Toast.fire({ icon: 'error', title: 'Error de conexión' });
        }
    }
};

// Función para limpiar formulario
const limpiarFormularioPermiso = () => {
    formPermiso.reset();
    limpiarValidacionPermiso();
    btnGuardarPermiso.classList.remove('d-none');
    btnModificarPermiso.classList.add('d-none');
    cargarAplicaciones(); // Recargar select de aplicaciones
};

// Auto-generar clave en mayúsculas
document.getElementById('permiso_clave').addEventListener('input', (e) => {
    e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9_]/g, '');
});

// Event listeners principales
formPermiso.addEventListener('submit', (e) => {
    if (btnModificarPermiso.classList.contains('d-none')) {
        guardarPermiso(e);
    } else {
        modificarPermiso(e);
    }
});

btnModificarPermiso.addEventListener('click', modificarPermiso);
btnBuscarPermisos.addEventListener('click', buscarPermisos);
btnLimpiarPermiso.addEventListener('click', limpiarFormularioPermiso);

console.log('Sistema de permisos inicializado correctamente');