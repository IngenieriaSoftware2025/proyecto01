import { Toast } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";
import Swal from "sweetalert2";
import { Modal } from "bootstrap";

// Elementos DOM principales
const FormFiltrosHistorial = document.getElementById("FormFiltrosHistorial");
const BtnBuscarHistorial = document.getElementById("BtnBuscarHistorial");
const BtnLimpiarFiltros = document.getElementById("BtnLimpiarFiltros");
const seccionHistorial = document.getElementById("seccion-historial");
const mensajeSinHistorial = document.getElementById("mensaje-sin-historial");

// Variables globales
let TablaHistorial = null;
let historialCargado = false;

// FUNCIÓN PARA CARGAR USUARIOS EN FILTRO
const cargarUsuarios = async () => {
    try {
        const respuesta = await fetch('/proyecto01/historial/obtenerUsuariosAPI');
        const datos = await respuesta.json();
        
        if (datos.codigo === 1) {
            const selectUsuario = document.getElementById('filtro_usuario');
            selectUsuario.innerHTML = '<option value="">Todos los usuarios</option>';
            
            datos.data.forEach(usuario => {
                const option = document.createElement('option');
                option.value = usuario.usu_id;
                option.textContent = `${usuario.usu_nombre} (${usuario.usu_codigo})`;
                selectUsuario.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar usuarios:', error);
    }
};

// FUNCIÓN PARA CARGAR MÓDULOS EN FILTRO
const cargarModulos = async () => {
    try {
        const respuesta = await fetch('/proyecto01/historial/obtenerModulosAPI');
        const datos = await respuesta.json();
        
        if (datos.codigo === 1) {
            const selectModulo = document.getElementById('filtro_modulo');
            selectModulo.innerHTML = '<option value="">Todos los módulos</option>';
            
            datos.data.forEach(modulo => {
                const option = document.createElement('option');
                option.value = modulo.modulo;
                option.textContent = modulo.modulo;
                selectModulo.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar módulos:', error);
    }
};

// FUNCIÓN PARA BUSCAR HISTORIAL DE ACTIVIDADES
const buscarHistorial = async () => {
    BtnBuscarHistorial.disabled = true;
    BtnBuscarHistorial.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Buscando...';

    try {
        // Construir URL con parámetros de filtro
        const params = new URLSearchParams();
        
        const fechaInicio = document.getElementById('filtro_fecha_inicio').value;
        const usuario = document.getElementById('filtro_usuario').value;
        const modulo = document.getElementById('filtro_modulo').value;
        const limite = document.getElementById('filtro_limite').value;

        if (fechaInicio) params.append('fecha_inicio', fechaInicio);
        if (usuario) params.append('usuario_id', usuario);
        if (modulo) params.append('modulo', modulo);
        if (limite) params.append('limite', limite);

        const url = `/proyecto01/historial/buscarHistorialAPI?${params.toString()}`;
        const respuesta = await fetch(url);
        const datos = await respuesta.json();

        if (datos.codigo === 1) {
            mostrarTablaHistorial(datos.data);
            
            Swal.fire({
                icon: 'success',
                title: 'Historial cargado',
                text: `${datos.total} actividad(es) encontrada(s)`,
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Sin resultados',
                text: datos.mensaje,
                confirmButtonText: 'Entendido'
            });
        }

    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo cargar el historial',
            confirmButtonText: 'Entendido'
        });
    } finally {
        BtnBuscarHistorial.disabled = false;
        BtnBuscarHistorial.innerHTML = '<i class="bi bi-search me-2"></i>Buscar Actividades';
    }
};

// FUNCIÓN PARA MOSTRAR TABLA DE HISTORIAL
const mostrarTablaHistorial = (actividades) => {
    seccionHistorial.classList.remove('d-none');
    seccionHistorial.classList.add('fade-in');
    mensajeSinHistorial.classList.add('d-none');

    if (TablaHistorial) {
        TablaHistorial.destroy();
        document.getElementById('TableHistorial').innerHTML = '';
    }

    TablaHistorial = new DataTable('#TableHistorial', {
        data: actividades,
        language: lenguaje,
        responsive: true,
        pageLength: 25,
        order: [[0, 'desc']],
        columns: [
            {
                title: 'ID',
                data: 'historial_id',
                width: '5%'
            },
            {
                title: 'Fecha/Hora',
                data: 'fecha',
                width: '12%'
            },
            {
                title: 'Usuario',
                data: 'usuario',
                width: '18%',
                render: (data) => `<span class="badge bg-info">${data}</span>`
            },
            {
                title: 'Módulo',
                data: 'modulo',
                width: '10%',
                render: (data) => {
                    const colores = {
                        'CLIENTES': 'bg-primary',
                        'MARCAS': 'bg-success',
                        'INVENTARIO': 'bg-warning',
                        'VENTAS': 'bg-danger',
                        'REPARACIONES': 'bg-info',
                        'USUARIOS': 'bg-dark',
                        'LOGIN': 'bg-secondary'
                    };
                    const color = colores[data] || 'bg-secondary';
                    return `<span class="badge ${color}">${data}</span>`;
                }
            },
            {
                title: 'Acción',
                data: 'accion',
                width: '10%',
                render: (data) => {
                    const colores = {
                        'CREATE': 'bg-success',
                        'UPDATE': 'bg-warning text-dark',
                        'DELETE': 'bg-danger',
                        'LOGIN': 'bg-info',
                        'LOGOUT': 'bg-secondary'
                    };
                    const color = colores[data] || 'bg-secondary';
                    return `<span class="badge ${color}">${data}</span>`;
                }
            },
            {
                title: 'Descripción',
                data: 'descripcion',
                width: '25%',
                render: (data) => {
                    return data && data.length > 60 ? data.substring(0, 60) + '...' : data;
                }
            },
            {
                title: 'Tabla',
                data: 'tabla_afectada',
                width: '10%',
                render: (data) => data || '<span class="text-muted">N/A</span>'
            },
            {
                title: 'IP',
                data: 'ip',
                width: '8%',
                render: (data) => `<code class="text-muted">${data}</code>`
            },
            {
                title: 'Acciones',
                data: 'historial_id',
                width: '7%',
                orderable: false,
                render: (data, type, row) => {
                    return `
                        <button class='btn btn-info btn-sm ver-detalle' 
                            data-id="${data}" 
                            data-descripcion="${row.descripcion}"
                            data-datos-anteriores="${row.datos_anteriores || ''}"
                            data-datos-nuevos="${row.datos_nuevos || ''}"
                            title="Ver detalle completo">
                            <i class='bi bi-eye'></i>
                        </button>
                    `;
                }
            }
        ]
    });

    // Event listeners para botones de la tabla
    setTimeout(() => {
        document.querySelectorAll('.ver-detalle').forEach(btn => {
            btn.addEventListener('click', verDetalleActividad);
        });
    }, 100);
};

// FUNCIÓN PARA VER DETALLE DE ACTIVIDAD
const verDetalleActividad = (e) => {
    const datos = e.currentTarget.dataset;
    
    let contenidoModal = `
        <div class="row mb-3">
            <div class="col-12">
                <h6 class="text-primary">Descripción Completa</h6>
                <p class="mb-3">${datos.descripcion || 'Sin descripción'}</p>
            </div>
        </div>
    `;

    // Mostrar datos anteriores si existen
    if (datos.datosAnteriores && datos.datosAnteriores !== 'null') {
        try {
            const datosAnt = JSON.parse(datos.datosAnteriores);
            contenidoModal += `
                <div class="row mb-3">
                    <div class="col-12">
                        <h6 class="text-warning">Datos Anteriores</h6>
                        <div class="datos-json">${JSON.stringify(datosAnt, null, 2)}</div>
                    </div>
                </div>
            `;
        } catch (e) {
            contenidoModal += `
                <div class="row mb-3">
                    <div class="col-12">
                        <h6 class="text-warning">Datos Anteriores</h6>
                        <div class="datos-json">${datos.datosAnteriores}</div>
                    </div>
                </div>
            `;
        }
    }

    // Mostrar datos nuevos si existen
    if (datos.datosNuevos && datos.datosNuevos !== 'null') {
        try {
            const datosNuev = JSON.parse(datos.datosNuevos);
            contenidoModal += `
                <div class="row mb-3">
                    <div class="col-12">
                        <h6 class="text-success">Datos Nuevos</h6>
                        <div class="datos-json">${JSON.stringify(datosNuev, null, 2)}</div>
                    </div>
                </div>
            `;
        } catch (e) {
            contenidoModal += `
                <div class="row mb-3">
                    <div class="col-12">
                        <h6 class="text-success">Datos Nuevos</h6>
                        <div class="datos-json">${datos.datosNuevos}</div>
                    </div>
                </div>
            `;
        }
    }

    document.getElementById('ContenidoDetalleActividad').innerHTML = contenidoModal;
    
    const modal = new Modal(document.getElementById('ModalDetalleActividad'));
    modal.show();
};

// FUNCIÓN PARA LIMPIAR FILTROS
const limpiarFiltros = () => {
    FormFiltrosHistorial.reset();
    seccionHistorial.classList.add('d-none');
    seccionEstadisticas.classList.add('d-none');
    mensajeSinHistorial.classList.remove('d-none');
    
    if (TablaHistorial) {
        TablaHistorial.destroy();
        TablaHistorial = null;
    }
};

// FUNCIÓN PARA ESTABLECER FECHA INICIAL POR DEFECTO
const establecerFechaInicial = () => {
    const hoy = new Date();
    const hace7Dias = new Date(hoy.getTime() - (7 * 24 * 60 * 60 * 1000));
    const fechaFormateada = hace7Dias.toISOString().split('T')[0];
    document.getElementById('filtro_fecha_inicio').value = fechaFormateada;
};

// INICIALIZACIÓN
document.addEventListener('DOMContentLoaded', () => {
    cargarUsuarios();
    cargarModulos();
    establecerFechaInicial();
});

// EVENT LISTENERS
BtnBuscarHistorial.addEventListener('click', buscarHistorial);
BtnLimpiarFiltros.addEventListener('click', limpiarFiltros);

console.log('Módulo de historial de actividades inicializado correctamente');