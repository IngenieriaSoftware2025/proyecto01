import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones";
import { lenguaje } from "../lenguaje";

// Elementos DOM principales
const FormReparaciones = document.getElementById("FormReparaciones");
const BtnGuardarReparacion = document.getElementById("BtnGuardarReparacion");
const BtnLimpiarReparacion = document.getElementById("BtnLimpiarReparacion");
const selectCliente = document.getElementById("cliente_id");
const selectTipoServicio = document.getElementById("tipo_servicio_id");
const precioBaseDisplay = document.getElementById("precio_base_display");

// Elementos para búsqueda de reparaciones
const BtnBuscarReparaciones = document.getElementById("BtnBuscarReparaciones");
const seccionReparaciones = document.getElementById("seccion-reparaciones");
const mensajeSinReparaciones = document.getElementById("mensaje-sin-reparaciones");

// Elementos para modales
const ModalCambiarEstado = document.getElementById("ModalCambiarEstado");
const ModalAsignarTecnico = document.getElementById("ModalAsignarTecnico");
const BtnConfirmarCambioEstado = document.getElementById("BtnConfirmarCambioEstado");
const BtnConfirmarAsignacion = document.getElementById("BtnConfirmarAsignacion");

// Variables globales
let reparacionesCargadas = false;

// FUNCIÓN PARA CARGAR CLIENTES
const cargarClientes = async () => {
    try {
        const url = "/proyecto01/reparaciones/buscarClientesAPI";
        const respuesta = await fetch(url);
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        if (codigo === 1) {
            selectCliente.innerHTML = '<option value="">Seleccione un cliente...</option>';
            data.forEach(cliente => {
                const option = document.createElement('option');
                option.value = cliente.id;
                option.textContent = `${cliente.nombre} ${cliente.apellido} - Tel: ${cliente.telefono}`;
                selectCliente.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar clientes:', error);
    }
};

// FUNCIÓN PARA CARGAR TIPOS DE SERVICIO
const cargarTiposServicio = async () => {
    try {
        const url = "/proyecto01/reparaciones/buscarTiposServicioAPI";
        const respuesta = await fetch(url);
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        if (codigo === 1) {
            selectTipoServicio.innerHTML = '<option value="">Seleccione tipo de servicio...</option>';
            data.forEach(tipo => {
                const option = document.createElement('option');
                option.value = tipo.tipo_id;
                option.textContent = `${tipo.tipo_nombre} - Q${parseFloat(tipo.precio_base).toFixed(2)}`;
                option.dataset.precio = tipo.precio_base;
                option.dataset.descripcion = tipo.tipo_descripcion;
                selectTipoServicio.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar tipos de servicio:', error);
    }
};

// FUNCIÓN PARA CARGAR TÉCNICOS
const cargarTecnicos = async () => {
    try {
        const url = "/proyecto01/reparaciones/buscarTecnicosAPI";
        const respuesta = await fetch(url);
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        if (codigo === 1) {
            const selectTecnico = document.getElementById("tecnico_id");
            selectTecnico.innerHTML = '<option value="">Seleccione un técnico...</option>';
            data.forEach(tecnico => {
                const option = document.createElement('option');
                option.value = tecnico.usu_id;
                option.textContent = tecnico.usu_nombre;
                selectTecnico.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar técnicos:', error);
    }
};

// FUNCIÓN PARA CARGAR MARCAS
const cargarMarcas = async () => {
    try {
        const url = "/proyecto01/reparaciones/buscarMarcasAPI";
        const respuesta = await fetch(url);
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        if (codigo === 1) {
            const selectMarca = document.getElementById('dispositivo_marca');
            selectMarca.innerHTML = '<option value="">Seleccione una marca...</option>';
            data.forEach(marca => {
                const option = document.createElement('option');
                option.value = marca.nombre;
                option.textContent = marca.nombre;
                selectMarca.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar marcas:', error);
    }
};

// FUNCIÓN PARA ACTUALIZAR PRECIO BASE
const actualizarPrecioBase = () => {
    const selectedOption = selectTipoServicio.options[selectTipoServicio.selectedIndex];
    if (selectedOption && selectedOption.dataset.precio) {
        const precio = parseFloat(selectedOption.dataset.precio);
        precioBaseDisplay.textContent = `Q${precio.toFixed(2)}`;
        
        // Actualizar presupuesto inicial si está vacío
        const presupuestoInput = document.getElementById("presupuesto_inicial");
        if (!presupuestoInput.value) {
            presupuestoInput.value = precio.toFixed(2);
        }
    } else {
        precioBaseDisplay.textContent = "Q0.00";
    }
};

// FUNCIÓN PARA GUARDAR REPARACIÓN
const guardarReparacion = async (e) => {
    e.preventDefault();

    if (!validarFormulario(FormReparaciones, ["reparacion_id", "dispositivo_serie", "dispositivo_imei", "tipo_servicio_id", "presupuesto_inicial", "anticipo", "observaciones"])) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos obligatorios',
            text: 'Complete todos los campos obligatorios marcados con *',
            confirmButtonText: 'Entendido'
        });
        return;
    }

    BtnGuardarReparacion.disabled = true;
    BtnGuardarReparacion.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';

    try {
        const formData = new FormData(FormReparaciones);
        const url = "/proyecto01/reparaciones/guardarAPI";
        const config = {
            method: "POST",
            body: formData
        };

        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        if (codigo === 1) {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: mensaje,
                timer: 2000,
                showConfirmButton: false
            });

            limpiarFormulario();

            if (reparacionesCargadas) {
                buscarReparaciones();
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: mensaje,
                confirmButtonText: 'Entendido'
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor',
            confirmButtonText: 'Entendido'
        });
    } finally {
        BtnGuardarReparacion.disabled = false;
        BtnGuardarReparacion.innerHTML = '<i class="bi bi-tools me-2"></i>Recibir Reparación';
    }
};

// INICIALIZAR DATATABLE PARA REPARACIONES
const TablaReparaciones = new DataTable("#TableReparaciones", {
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
            title: "ID",
            data: "reparacion_id",
            width: "5%"
        },
        {
            title: "Cliente",
            data: "cliente_nombre",
            width: "15%",
            render: (data, type, row) => `${row.cliente_nombre} ${row.cliente_apellido}`
        },
        {
            title: "Dispositivo",
            data: "dispositivo_marca",
            width: "15%",
            render: (data, type, row) => `<strong>${row.dispositivo_marca}</strong><br><small>${row.dispositivo_modelo}</small>`
        },
        {
            title: "Problema",
            data: "problema_reportado",
            width: "20%",
            render: (data) => {
                return data && data.length > 50 ? data.substring(0, 50) + '...' : data;
            }
        },
        {
            title: "Estado",
            data: "estado",
            width: "12%",
            render: (data) => {
                const badges = {
                    'RECIBIDO': '<span class="badge bg-secondary">Recibido</span>',
                    'EN_DIAGNOSTICO': '<span class="badge bg-info">En Diagnóstico</span>',
                    'DIAGNOSTICADO': '<span class="badge bg-warning">Diagnosticado</span>',
                    'EN_REPARACION': '<span class="badge bg-primary">En Reparación</span>',
                    'REPARADO': '<span class="badge bg-success">Reparado</span>',
                    'ENTREGADO': '<span class="badge bg-dark">Entregado</span>',
                    'CANCELADO': '<span class="badge bg-danger">Cancelado</span>'
                };
                return badges[data] || data;
            }
        },
        {
            title: "Técnico",
            data: "tecnico_nombre",
            width: "10%",
            render: (data) => data || '<span class="text-muted">Sin asignar</span>'
        },
        {
            title: "Fecha",
            data: "fecha_ingreso",
            width: "8%"
        },
        {
            title: "Presupuesto",
            data: "presupuesto_inicial",
            width: "8%",
            render: (data) => data ? `Q${parseFloat(data).toFixed(2)}` : 'Sin presupuesto'
        },
        {
            title: "Acciones",
            data: "reparacion_id",
            width: "15%",
            orderable: false,
            render: (data, type, row) => {
                return `
                    <div class='d-flex justify-content-center flex-wrap'>
                        <button class='btn btn-info btn-sm ver-detalle m-1' 
                            data-id="${data}" title="Ver detalle">
                            <i class='bi bi-eye'></i>
                        </button>
                        <button class='btn btn-warning btn-sm cambiar-estado m-1' 
                            data-id="${data}" title="Cambiar estado">
                            <i class='bi bi-arrow-repeat'></i>
                        </button>
                        <button class='btn btn-success btn-sm asignar-tecnico m-1' 
                            data-id="${data}" title="Asignar técnico">
                            <i class='bi bi-person-gear'></i>
                        </button>
                    </div>`;
            }
        }
    ]
});

// FUNCIÓN PARA BUSCAR REPARACIONES
const buscarReparaciones = async () => {
    BtnBuscarReparaciones.disabled = true;
    BtnBuscarReparaciones.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Buscando...';

    try {
        const url = "/proyecto01/reparaciones/buscarAPI";
        const respuesta = await fetch(url);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos;

        if (codigo === 1) {
            seccionReparaciones.classList.remove('d-none');
            seccionReparaciones.classList.add('fade-in');
            mensajeSinReparaciones.classList.add('d-none');

            TablaReparaciones.clear().draw();
            TablaReparaciones.rows.add(data).draw();
            reparacionesCargadas = true;

            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: `${data.length} reparación(es) encontrada(s)`,
                timer: 800,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Sin datos',
                text: mensaje,
                timer: 800,
                showConfirmButton: false
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor',
            confirmButtonText: 'Entendido'
        });
    } finally {
        BtnBuscarReparaciones.disabled = false;
        BtnBuscarReparaciones.innerHTML = '<i class="bi bi-search me-2"></i>Buscar Reparaciones';
    }
};

// FUNCIÓN PARA VER DETALLE DE REPARACIÓN
const verDetalleReparacion = async (e) => {
    const reparacionId = e.currentTarget.dataset.id;

    try {
        const respuesta = await fetch(`/proyecto01/reparaciones/verDetalleAPI?reparacion_id=${reparacionId}`);
        const datos = await respuesta.json();

        if (datos.codigo === 1) {
            const { reparacion, historial } = datos.data;
            
            let contenidoModal = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2">Información del Cliente</h6>
                        <strong>Cliente:</strong> ${reparacion.cliente_nombre} ${reparacion.cliente_apellido}<br>
                        <strong>Teléfono:</strong> ${reparacion.telefono}<br>
                        <strong>NIT:</strong> ${reparacion.nit || 'Sin NIT'}
                    </div>
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2">Información del Dispositivo</h6>
                        <strong>Dispositivo:</strong> ${reparacion.dispositivo_marca} ${reparacion.dispositivo_modelo}<br>
                        <strong>Serie:</strong> ${reparacion.dispositivo_serie || 'Sin serie'}<br>
                        <strong>IMEI:</strong> ${reparacion.dispositivo_imei || 'Sin IMEI'}
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-12">
                        <h6 class="border-bottom pb-2">Problema Reportado</h6>
                        <p>${reparacion.problema_reportado}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Estado:</strong> <span class="badge bg-info">${reparacion.estado}</span>
                    </div>
                    <div class="col-md-4">
                        <strong>Técnico:</strong> ${reparacion.tecnico_nombre || 'Sin asignar'}
                    </div>
                    <div class="col-md-4">
                        <strong>Tipo de Servicio:</strong> ${reparacion.tipo_nombre || 'Sin especificar'}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Presupuesto:</strong> Q${parseFloat(reparacion.presupuesto_inicial || 0).toFixed(2)}
                    </div>
                    <div class="col-md-3">
                        <strong>Costo Final:</strong> Q${parseFloat(reparacion.costo_final || 0).toFixed(2)}
                    </div>
                    <div class="col-md-3">
                        <strong>Anticipo:</strong> Q${parseFloat(reparacion.anticipo || 0).toFixed(2)}
                    </div>
                    <div class="col-md-3">
                        <strong>Precio Base:</strong> Q${parseFloat(reparacion.precio_base || 0).toFixed(2)}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <strong>Ingreso:</strong> ${reparacion.fecha_ingreso_formato || 'Sin fecha'}
                    </div>
                    <div class="col-md-3">
                        <strong>Diagnóstico:</strong> ${reparacion.fecha_diagnostico_formato || 'Pendiente'}
                    </div>
                    <div class="col-md-3">
                        <strong>Finalización:</strong> ${reparacion.fecha_finalizacion_formato || 'Pendiente'}
                    </div>
                    <div class="col-md-3">
                        <strong>Entrega:</strong> ${reparacion.fecha_entrega_formato || 'Pendiente'}
                    </div>
                </div>

                <h6 class="border-bottom pb-2 mb-3">Historial de Cambios</h6>
                <div class="timeline">`;

            historial.forEach(cambio => {
                contenidoModal += `
                    <div class="timeline-item">
                        <strong>${cambio.fecha_cambio_formato}</strong> - ${cambio.usuario_nombre}<br>
                        <span class="text-muted">
                            ${cambio.estado_anterior ? `${cambio.estado_anterior} → ` : ''}${cambio.estado_nuevo}
                        </span><br>
                        ${cambio.observaciones ? `<em>"${cambio.observaciones}"</em>` : ''}
                    </div>`;
            });

            contenidoModal += '</div>';

            document.getElementById('ContenidoDetalleReparacion').innerHTML = contenidoModal;
            
            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('ModalDetalleReparacion'));
            modal.show();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: datos.mensaje,
                confirmButtonText: 'Entendido'
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo cargar el detalle de la reparación',
            confirmButtonText: 'Entendido'
        });
    }
};

// FUNCIÓN PARA CAMBIAR ESTADO
const cambiarEstado = (e) => {
    const reparacionId = e.currentTarget.dataset.id;
    document.getElementById('estado_reparacion_id').value = reparacionId;
    
    const modal = new bootstrap.Modal(ModalCambiarEstado);
    modal.show();
};

// FUNCIÓN PARA CONFIRMAR CAMBIO DE ESTADO
const confirmarCambioEstado = async () => {
    const form = document.getElementById('FormCambiarEstado');
    const formData = new FormData(form);

    if (!formData.get('nuevo_estado')) {
        Swal.fire({
            icon: 'warning',
            title: 'Seleccione un estado',
            text: 'Debe seleccionar el nuevo estado',
            confirmButtonText: 'Entendido'
        });
        return;
    }

    try {
        const respuesta = await fetch('/proyecto01/reparaciones/actualizarEstadoAPI', {
            method: 'POST',
            body: formData
        });

        const datos = await respuesta.json();

        if (datos.codigo === 1) {
            Swal.fire({
                icon: 'success',
                title: 'Estado actualizado',
                text: datos.mensaje,
                timer: 1500,
                showConfirmButton: false
            });

            const modal = bootstrap.Modal.getInstance(ModalCambiarEstado);
            modal.hide();
            
            buscarReparaciones();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: datos.mensaje,
                confirmButtonText: 'Entendido'
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo actualizar el estado',
            confirmButtonText: 'Entendido'
        });
    }
};

// FUNCIÓN PARA ASIGNAR TÉCNICO
const asignarTecnico = (e) => {
    const reparacionId = e.currentTarget.dataset.id;
    document.getElementById('tecnico_reparacion_id').value = reparacionId;
    
    const modal = new bootstrap.Modal(ModalAsignarTecnico);
    modal.show();
};

// FUNCIÓN PARA CONFIRMAR ASIGNACIÓN DE TÉCNICO
const confirmarAsignacion = async () => {
    const form = document.getElementById('FormAsignarTecnico');
    const formData = new FormData(form);

    if (!formData.get('tecnico_id')) {
        Swal.fire({
            icon: 'warning',
            title: 'Seleccione un técnico',
            text: 'Debe seleccionar un técnico',
            confirmButtonText: 'Entendido'
        });
        return;
    }

    try {
        const respuesta = await fetch('/proyecto01/reparaciones/asignarTecnicoAPI', {
            method: 'POST',
            body: formData
        });

        const datos = await respuesta.json();

        if (datos.codigo === 1) {
            Swal.fire({
                icon: 'success',
                title: 'Técnico asignado',
                text: datos.mensaje,
                timer: 1500,
                showConfirmButton: false
            });

            const modal = bootstrap.Modal.getInstance(ModalAsignarTecnico);
            modal.hide();
            
            buscarReparaciones();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: datos.mensaje,
                confirmButtonText: 'Entendido'
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo asignar el técnico',
            confirmButtonText: 'Entendido'
        });
    }
};

// FUNCIÓN PARA LIMPIAR FORMULARIO
const limpiarFormulario = () => {
    FormReparaciones.reset();
    precioBaseDisplay.textContent = "Q0.00";
    
    // Recargar datos
    cargarClientes();
    cargarTiposServicio();
};

// CARGAR DATOS AL INICIALIZAR
document.addEventListener('DOMContentLoaded', () => {
    cargarClientes();
    cargarTiposServicio();
    cargarTecnicos();
    cargarMarcas();
});

// EVENT LISTENERS
FormReparaciones.addEventListener('submit', guardarReparacion);
BtnLimpiarReparacion.addEventListener('click', limpiarFormulario);
BtnBuscarReparaciones.addEventListener('click', buscarReparaciones);
selectTipoServicio.addEventListener('change', actualizarPrecioBase);
BtnConfirmarCambioEstado.addEventListener('click', confirmarCambioEstado);
BtnConfirmarAsignacion.addEventListener('click', confirmarAsignacion);

// Event listeners para tabla
TablaReparaciones.on('click', '.ver-detalle', verDetalleReparacion);
TablaReparaciones.on('click', '.cambiar-estado', cambiarEstado);
TablaReparaciones.on('click', '.asignar-tecnico', asignarTecnico);

console.log('Módulo de reparaciones inicializado correctamente');