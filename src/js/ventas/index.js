import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones";
import { lenguaje } from "../lenguaje";

// Elementos DOM principales
const FormVentas = document.getElementById("FormVentas");
const BtnGuardarVenta = document.getElementById("BtnGuardarVenta");
const BtnLimpiarVenta = document.getElementById("BtnLimpiarVenta");
const selectCliente = document.getElementById("cliente_id");
const selectDispositivo = document.getElementById("dispositivo_select");
const cantidadInput = document.getElementById("cantidad_input");
const BtnAgregarDispositivo = document.getElementById("BtnAgregarDispositivo");

// Elementos para búsqueda de ventas
const BtnBuscarVentas = document.getElementById("BtnBuscarVentas");
const seccionVentas = document.getElementById("seccion-ventas");
const mensajeSinVentas = document.getElementById("mensaje-sin-ventas");

// Variables globales
let ventasCargadas = false;
let dispositivosEnVenta = []; // Array para almacenar dispositivos seleccionados
let totalVenta = 0;

// FUNCIÓN PARA CARGAR CLIENTES
const cargarClientes = async () => {
    try {
        const url = "/proyecto01/ventas/buscarClientesAPI";
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

// FUNCIÓN PARA CARGAR INVENTARIO DISPONIBLE
const cargarInventarioDisponible = async () => {
    try {
        const url = "/proyecto01/ventas/buscarInventarioDisponibleAPI";
        const respuesta = await fetch(url);
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        if (codigo === 1) {
            selectDispositivo.innerHTML = '<option value="">Seleccione un dispositivo del inventario...</option>';
            data.forEach(dispositivo => {
                const option = document.createElement('option');
                option.value = dispositivo.id;
                option.textContent = `${dispositivo.marca_nombre} ${dispositivo.marca_modelo} - ${dispositivo.numero_serie} - Q${parseFloat(dispositivo.precio_venta).toFixed(2)} (Stock: ${dispositivo.stock_disponible})`;
                option.dataset.precio = dispositivo.precio_venta;
                option.dataset.stock = dispositivo.stock_disponible;
                option.dataset.marca = dispositivo.marca_nombre;
                option.dataset.modelo = dispositivo.marca_modelo;
                option.dataset.serie = dispositivo.numero_serie;
                option.dataset.estado = dispositivo.estado_dispositivo;
                selectDispositivo.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar inventario:', error);
    }
};

// FUNCIÓN PARA AGREGAR DISPOSITIVO A LA VENTA
const agregarDispositivo = () => {
    const dispositivoSeleccionado = selectDispositivo.value;
    const cantidad = parseInt(cantidadInput.value);

    if (!dispositivoSeleccionado) {
        Swal.fire({
            icon: 'warning',
            title: 'Seleccione un dispositivo',
            text: 'Debe seleccionar un dispositivo del inventario',
            confirmButtonText: 'Entendido'
        });
        return;
    }

    if (!cantidad || cantidad <= 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Cantidad inválida',
            text: 'La cantidad debe ser mayor a 0',
            confirmButtonText: 'Entendido'
        });
        return;
    }

    const option = selectDispositivo.options[selectDispositivo.selectedIndex];
    const stockDisponible = parseInt(option.dataset.stock);
    const precio = parseFloat(option.dataset.precio);

    // Verificar stock disponible
    if (cantidad > stockDisponible) {
        Swal.fire({
            icon: 'error',
            title: 'Stock insuficiente',
            text: `Solo hay ${stockDisponible} unidades disponibles`,
            confirmButtonText: 'Entendido'
        });
        return;
    }

    // Verificar si el dispositivo ya fue agregado
    const dispositivoExistente = dispositivosEnVenta.find(d => d.inventario_id === dispositivoSeleccionado);
    if (dispositivoExistente) {
        Swal.fire({
            icon: 'warning',
            title: 'Dispositivo ya agregado',
            text: 'Este dispositivo ya está en la venta. Puede modificar la cantidad desde la tabla.',
            confirmButtonText: 'Entendido'
        });
        return;
    }

    // Agregar dispositivo al array
    const nuevoDispositivo = {
        inventario_id: dispositivoSeleccionado,
        marca: option.dataset.marca,
        modelo: option.dataset.modelo,
        numero_serie: option.dataset.serie,
        estado_dispositivo: option.dataset.estado,
        precio_unitario: precio,
        cantidad: cantidad,
        subtotal: precio * cantidad
    };

    dispositivosEnVenta.push(nuevoDispositivo);
    actualizarTablaDispositivos();
    
    // Limpiar selección
    selectDispositivo.value = '';
    cantidadInput.value = 1;

    Swal.fire({
        icon: 'success',
        title: 'Dispositivo agregado',
        text: 'El dispositivo se agregó correctamente a la venta',
        timer: 1000,
        showConfirmButton: false
    });
};

// FUNCIÓN PARA ACTUALIZAR LA TABLA DE DISPOSITIVOS
const actualizarTablaDispositivos = () => {
    const tbody = document.getElementById('TbodyDispositivos');
    const filaSinDispositivos = document.getElementById('FilaSinDispositivos');
    const totalElement = document.getElementById('TotalVenta');

    if (dispositivosEnVenta.length === 0) {
        filaSinDispositivos.classList.remove('d-none');
        totalVenta = 0;
    } else {
        filaSinDispositivos.classList.add('d-none');
        
        // Limpiar tabla y reconstruir
        tbody.innerHTML = '<tr id="FilaSinDispositivos" class="d-none"><td colspan="7" class="text-center text-muted"><i class="bi bi-cart-x me-2"></i>No hay dispositivos agregados</td></tr>';

        totalVenta = 0;
        dispositivosEnVenta.forEach((dispositivo, index) => {
            totalVenta += dispositivo.subtotal;

            const fila = document.createElement('tr');
            fila.className = 'dispositivo-item';
            fila.innerHTML = `
                <td>
                    <strong>${dispositivo.marca} ${dispositivo.modelo}</strong>
                </td>
                <td>
                    <code class="bg-light p-1 rounded">${dispositivo.numero_serie}</code>
                </td>
                <td>
                    <span class="badge ${dispositivo.estado_dispositivo === 'NUEVO' ? 'bg-success' : dispositivo.estado_dispositivo === 'USADO' ? 'bg-warning' : 'bg-info'}">
                        ${dispositivo.estado_dispositivo}
                    </span>
                </td>
                <td>Q${dispositivo.precio_unitario.toFixed(2)}</td>
                <td>
                    <input type="number" class="form-control form-control-sm cantidad-dispositivo" 
                           value="${dispositivo.cantidad}" min="1" 
                           data-index="${index}" style="width: 80px;">
                </td>
                <td class="fw-bold">Q${dispositivo.subtotal.toFixed(2)}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm btn-remove-dispositivo" 
                            data-index="${index}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(fila);
        });
    }

    totalElement.textContent = `Q${totalVenta.toFixed(2)}`;

    // Agregar event listeners para cambios de cantidad y eliminación
    setTimeout(() => {
        document.querySelectorAll('.cantidad-dispositivo').forEach(input => {
            input.addEventListener('change', cambiarCantidadDispositivo);
        });

        document.querySelectorAll('.btn-remove-dispositivo').forEach(btn => {
            btn.addEventListener('click', eliminarDispositivo);
        });
    }, 100);
};

// FUNCIÓN PARA CAMBIAR CANTIDAD DE DISPOSITIVO
const cambiarCantidadDispositivo = (e) => {
    const index = parseInt(e.target.dataset.index);
    const nuevaCantidad = parseInt(e.target.value);

    if (nuevaCantidad <= 0) {
        e.target.value = dispositivosEnVenta[index].cantidad;
        return;
    }

    dispositivosEnVenta[index].cantidad = nuevaCantidad;
    dispositivosEnVenta[index].subtotal = dispositivosEnVenta[index].precio_unitario * nuevaCantidad;
    
    actualizarTablaDispositivos();
};

// FUNCIÓN PARA ELIMINAR DISPOSITIVO
const eliminarDispositivo = (e) => {
    const index = parseInt(e.currentTarget.dataset.index);
    dispositivosEnVenta.splice(index, 1);
    actualizarTablaDispositivos();
};

// FUNCIÓN PARA PROCESAR VENTA
const procesarVenta = async (e) => {
    e.preventDefault();

    if (!selectCliente.value) {
        Swal.fire({
            icon: 'warning',
            title: 'Seleccione un cliente',
            text: 'Debe seleccionar un cliente para procesar la venta',
            confirmButtonText: 'Entendido'
        });
        return;
    }

    if (dispositivosEnVenta.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Agregue dispositivos',
            text: 'Debe agregar al menos un dispositivo a la venta',
            confirmButtonText: 'Entendido'
        });
        return;
    }

    const confirmacion = await Swal.fire({
        title: '¿Procesar venta?',
        text: `Total: Q${totalVenta.toFixed(2)}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, procesar',
        cancelButtonText: 'Cancelar'
    });

    if (!confirmacion.isConfirmed) return;

    BtnGuardarVenta.disabled = true;
    BtnGuardarVenta.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';

    try {
        const formData = new FormData();
        formData.append('cliente_id', selectCliente.value);
        formData.append('total', totalVenta);
        formData.append('observaciones', document.getElementById('observaciones').value);
        
        // Agregar dispositivos como array
        dispositivosEnVenta.forEach((dispositivo, index) => {
            formData.append(`dispositivos[${index}][inventario_id]`, dispositivo.inventario_id);
            formData.append(`dispositivos[${index}][cantidad]`, dispositivo.cantidad);
            formData.append(`dispositivos[${index}][precio_unitario]`, dispositivo.precio_unitario);
        });

        const respuesta = await fetch('/proyecto01/ventas/guardarAPI', {
            method: 'POST',
            body: formData
        });

        const datos = await respuesta.json();

        if (datos.codigo === 1) {
            await Swal.fire({
                icon: 'success',
                title: '¡Venta procesada!',
                text: `${datos.mensaje}. Total: Q${datos.datos.total.toFixed(2)}`,
                confirmButtonText: 'Entendido'
            });

            limpiarFormulario();
            
            // Recargar inventario disponible
            await cargarInventarioDisponible();
            
            if (ventasCargadas) {
                buscarVentas();
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error al procesar venta',
                text: datos.mensaje,
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
        BtnGuardarVenta.disabled = false;
        BtnGuardarVenta.innerHTML = '<i class="bi bi-cart-check me-2"></i>Procesar Venta';
    }
};

// INICIALIZAR DATATABLE PARA HISTORIAL DE VENTAS
const TablaVentas = new DataTable("#TableVentas", {
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
            data: "venta_id",
            width: "8%"
        },
        {
            title: "Cliente",
            data: "cliente_nombre",
            width: "20%",
            render: (data, type, row) => `${row.cliente_nombre} ${row.cliente_apellido}`
        },
        {
            title: "Vendedor",
            data: "vendedor_nombre",
            width: "15%"
        },
        {
            title: "Total",
            data: "total",
            width: "12%",
            render: (data) => `Q${parseFloat(data).toFixed(2)}`,
            className: "text-end"
        },
        {
            title: "Fecha",
            data: "fecha_venta",
            width: "12%"
        },
        {
            title: "Estado",
            data: "estado",
            width: "10%",
            render: (data) => {
                const badges = {
                    'COMPLETADA': '<span class="badge bg-success">Completada</span>',
                    'ANULADA': '<span class="badge bg-danger">Anulada</span>',
                    'PENDIENTE': '<span class="badge bg-warning">Pendiente</span>'
                };
                return badges[data] || data;
            }
        },
        {
            title: "Acciones",
            data: "venta_id",
            width: "23%",
            orderable: false,
            render: (data, type, row) => {
                let botones = `
                    <button class='btn btn-info btn-sm ver-detalle mx-1' 
                        data-id="${data}" title="Ver detalle">
                        <i class='bi bi-eye'></i> Ver
                    </button>
                `;

                // Solo ADMIN puede anular ventas
                if (typeof esAdmin !== 'undefined' && esAdmin && row.estado === 'COMPLETADA') {
                    botones += `
                        <button class='btn btn-danger btn-sm anular-venta mx-1' 
                            data-id="${data}" title="Anular venta">
                            <i class='bi bi-x-circle'></i> Anular
                        </button>
                    `;
                }

                return `<div class='d-flex justify-content-center'>${botones}</div>`;
            }
        }
    ]
});

// FUNCIÓN PARA BUSCAR HISTORIAL DE VENTAS
const buscarVentas = async () => {
    BtnBuscarVentas.disabled = true;
    BtnBuscarVentas.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Buscando...';

    try {
        const respuesta = await fetch('/proyecto01/ventas/buscarAPI');
        const datos = await respuesta.json();

        if (datos.codigo === 1) {
            seccionVentas.classList.remove('d-none');
            seccionVentas.classList.add('fade-in');
            mensajeSinVentas.classList.add('d-none');

            TablaVentas.clear().draw();
            TablaVentas.rows.add(datos.data).draw();
            ventasCargadas = true;

            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: `${datos.data.length} venta(s) encontrada(s)`,
                timer: 800,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Sin datos',
                text: datos.mensaje,
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
        BtnBuscarVentas.disabled = false;
        BtnBuscarVentas.innerHTML = '<i class="bi bi-search me-2"></i>Buscar Ventas';
    }
};

// FUNCIÓN PARA VER DETALLE DE VENTA
const verDetalleVenta = async (e) => {
    const ventaId = e.currentTarget.dataset.id;

    try {
        const respuesta = await fetch(`/proyecto01/ventas/verDetalleAPI?venta_id=${ventaId}`);
        const datos = await respuesta.json();

        if (datos.codigo === 1) {
            const { venta, detalle } = datos.data;
            
            let contenidoModal = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>ID Venta:</strong> ${venta.venta_id}<br>
                        <strong>Cliente:</strong> ${venta.cliente_nombre} ${venta.cliente_apellido}<br>
                        <strong>Teléfono:</strong> ${venta.telefono}
                    </div>
                    <div class="col-md-6">
                        <strong>Fecha:</strong> ${venta.fecha_venta}<br>
                        <strong>Vendedor:</strong> ${venta.vendedor_nombre}<br>
                        <strong>Estado:</strong> <span class="badge bg-${venta.estado === 'COMPLETADA' ? 'success' : 'danger'}">${venta.estado}</span>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-12">
                        <strong>Observaciones:</strong> ${venta.observaciones || 'Sin observaciones'}
                    </div>
                </div>

                <h6 class="border-bottom pb-2 mb-3">Dispositivos Vendidos</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Dispositivo</th>
                                <th>N° Serie</th>
                                <th>Estado</th>
                                <th>Precio Unit.</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>`;

            detalle.forEach(item => {
                contenidoModal += `
                    <tr>
                        <td><strong>${item.marca_nombre} ${item.marca_modelo}</strong></td>
                        <td><code>${item.numero_serie}</code></td>
                        <td>
                            <span class="badge bg-${item.estado_dispositivo === 'NUEVO' ? 'success' : item.estado_dispositivo === 'USADO' ? 'warning' : 'info'}">
                                ${item.estado_dispositivo}
                            </span>
                        </td>
                        <td>Q${parseFloat(item.precio_unitario).toFixed(2)}</td>
                        <td>${item.cantidad}</td>
                        <td class="fw-bold">Q${parseFloat(item.subtotal).toFixed(2)}</td>
                    </tr>`;
            });

            contenidoModal += `
                        </tbody>
                        <tfoot>
                            <tr class="table-warning">
                                <td colspan="5" class="text-end fw-bold">TOTAL:</td>
                                <td class="fw-bold">Q${parseFloat(venta.total).toFixed(2)}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>`;

            document.getElementById('ContenidoDetalleVenta').innerHTML = contenidoModal;
            
            // Mostrar modal usando Bootstrap
            const modal = new bootstrap.Modal(document.getElementById('ModalDetalleVenta'));
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
            text: 'No se pudo cargar el detalle de la venta',
            confirmButtonText: 'Entendido'
        });
    }
};

// FUNCIÓN PARA ANULAR VENTA
const anularVenta = async (e) => {
    const ventaId = e.currentTarget.dataset.id;

    const confirmacion = await Swal.fire({
        title: '¿Anular venta?',
        text: 'Esta acción restaurará el stock de los dispositivos vendidos. ¿Está seguro?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, anular',
        cancelButtonText: 'Cancelar'
    });

    if (!confirmacion.isConfirmed) return;

    try {
        const respuesta = await fetch(`/proyecto01/ventas/anularAPI?venta_id=${ventaId}`);
        const datos = await respuesta.json();

        if (datos.codigo === 1) {
            Swal.fire({
                icon: 'success',
                title: 'Venta anulada',
                text: datos.mensaje,
                timer: 2000,
                showConfirmButton: false
            });

            // Recargar tabla de ventas e inventario
            buscarVentas();
            await cargarInventarioDisponible();
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
            text: 'No se pudo anular la venta',
            confirmButtonText: 'Entendido'
        });
    }
};

// FUNCIÓN PARA LIMPIAR FORMULARIO
const limpiarFormulario = () => {
    if (FormVentas) {
        FormVentas.reset();
    }
    dispositivosEnVenta = [];
    totalVenta = 0;
    actualizarTablaDispositivos();
    
    // Recargar datos
    cargarClientes();
    cargarInventarioDisponible();
};

// INICIALIZACIÓN AL CARGAR LA PÁGINA
document.addEventListener('DOMContentLoaded', () => {
    cargarClientes();
    cargarInventarioDisponible();
});

// EVENT LISTENERS
if (FormVentas) {
    FormVentas.addEventListener('submit', procesarVenta);
}

if (BtnAgregarDispositivo) {
    BtnAgregarDispositivo.addEventListener('click', agregarDispositivo);
}

if (BtnLimpiarVenta) {
    BtnLimpiarVenta.addEventListener('click', limpiarFormulario);
}

BtnBuscarVentas.addEventListener('click', buscarVentas);

// Event listeners para tabla de ventas
TablaVentas.on('click', '.ver-detalle', verDetalleVenta);
TablaVentas.on('click', '.anular-venta', anularVenta);

// Verificar si es admin (para mostrar botones de anulación)
const esAdmin = typeof window !== 'undefined' && 
                document.querySelector('meta[name="user-role"]') && 
                document.querySelector('meta[name="user-role"]').content === 'ADMIN';

console.log('Módulo de ventas inicializado correctamente');