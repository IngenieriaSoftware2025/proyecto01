import { Toast } from '../funciones';

const BtnCargarConfiguracion = document.getElementById('BtnCargarConfiguracion');
const BtnRespaldo = document.getElementById('BtnRespaldo');
const BtnLimpiarLogs = document.getElementById('BtnLimpiarLogs');
const BtnActualizarStats = document.getElementById('BtnActualizarStats');

// FUNCIÓN PARA CARGAR CONFIGURACIÓN
const cargarConfiguracion = async () => {
    try {
        mostrarSpinners(true);

        const url = '/proyecto01/configuracion/obtenerConfiguracionAPI';
        const config = {
            method: 'GET'
        };

        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();

        if (datos.codigo === 1) {
            mostrarInformacionSistema(datos.data.sistema);
            mostrarInformacionBaseDatos(datos.data.base_datos);
            mostrarInformacionUsuarios(datos.data.usuarios);
            mostrarEstadisticas(datos.data.estadisticas);

            Toast.fire({
                icon: 'success',
                title: 'Configuración cargada exitosamente'
            });
        } else {
            Toast.fire({
                icon: 'error',
                title: datos.mensaje
            });
        }

        mostrarSpinners(false);

    } catch (error) {
        console.error('Error:', error);
        mostrarSpinners(false);
        Toast.fire({
            icon: 'error',
            title: 'Error al cargar la configuración'
        });
    }
};

// FUNCIÓN PARA MOSTRAR/OCULTAR SPINNERS
const mostrarSpinners = (mostrar) => {
    const spinners = document.querySelectorAll('.spinner-border');
    const contenedores = ['info-sistema', 'info-database', 'info-usuarios', 'info-estadisticas'];

    if (mostrar) {
        spinners.forEach(spinner => spinner.style.display = 'block');
        contenedores.forEach(id => {
            const elemento = document.getElementById(id);
            if (elemento) {
                elemento.innerHTML = `
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                `;
            }
        });
    }
};

// FUNCIÓN PARA MOSTRAR INFORMACIÓN DEL SISTEMA
const mostrarInformacionSistema = (sistema) => {
    const container = document.getElementById('info-sistema');
    container.innerHTML = `
        <div class="info-item">
            <span class="info-label">Nombre:</span>
            <span class="info-value">${sistema.nombre}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Versión:</span>
            <span class="info-value">${sistema.version}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Desarrollador:</span>
            <span class="info-value">${sistema.desarrollador}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Última Actualización:</span>
            <span class="info-value">${new Date(sistema.ultima_actualizacion).toLocaleString()}</span>
        </div>
    `;
};

// FUNCIÓN PARA MOSTRAR INFORMACIÓN DE BASE DE DATOS
const mostrarInformacionBaseDatos = (baseDatos) => {
    const container = document.getElementById('info-database');
    container.innerHTML = `
        <div class="info-item">
            <span class="info-label">Host:</span>
            <span class="info-value">${baseDatos.host}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Servidor:</span>
            <span class="info-value">${baseDatos.servidor}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Base de Datos:</span>
            <span class="info-value">${baseDatos.nombre_bd}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Estado:</span>
            <span class="info-value">
                <span class="badge bg-success">Conectado</span>
            </span>
        </div>
    `;
};

// FUNCIÓN PARA MOSTRAR INFORMACIÓN DE USUARIOS
const mostrarInformacionUsuarios = (usuarios) => {
    const container = document.getElementById('info-usuarios');
    container.innerHTML = `
        <div class="info-item">
            <span class="info-label">Total Usuarios:</span>
            <span class="info-value">${usuarios.total_usuarios}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Usuarios Activos:</span>
            <span class="info-value">${usuarios.usuarios_activos}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Administradores:</span>
            <span class="info-value">${usuarios.administradores}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Estado del Sistema:</span>
            <span class="info-value">
                <span class="badge bg-success">Operativo</span>
            </span>
        </div>
    `;
};

// FUNCIÓN PARA MOSTRAR ESTADÍSTICAS
const mostrarEstadisticas = (estadisticas) => {
    const container = document.getElementById('info-estadisticas');
    container.innerHTML = `
        <div class="info-item">
            <span class="info-label">Total Clientes:</span>
            <span class="info-value">${estadisticas.total_clientes}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Dispositivos en Inventario:</span>
            <span class="info-value">${estadisticas.total_inventario}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Ventas este Mes:</span>
            <span class="info-value">${estadisticas.ventas_mes}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Reparaciones Pendientes:</span>
            <span class="info-value">${estadisticas.reparaciones_pendientes}</span>
        </div>
    `;
};

// FUNCIÓN PARA CREAR RESPALDO
const crearRespaldo = async () => {
    const confirmacion = await Swal.fire({
        title: '¿Crear respaldo?',
        text: 'Se creará un respaldo completo de la base de datos',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, crear respaldo',
        cancelButtonText: 'Cancelar'
    });

    if (!confirmacion.isConfirmed) return;

    try {
        BtnRespaldo.disabled = true;
        BtnRespaldo.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Creando...';

        const url = '/proyecto01/configuracion/crearRespaldoAPI';
        const config = {
            method: 'POST'
        };

        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();

        if (datos.codigo === 1) {
            Toast.fire({
                icon: 'success',
                title: 'Respaldo creado exitosamente'
            });

            // Mostrar información del respaldo
            const infoRespaldo = document.getElementById('info-respaldo');
            const ultimoRespaldo = document.getElementById('ultimo-respaldo');
            
            ultimoRespaldo.textContent = `${datos.archivo} (${datos.tamaño}) - ${datos.fecha}`;
            infoRespaldo.classList.remove('d-none');

        } else {
            Toast.fire({
                icon: 'error',
                title: datos.mensaje
            });
        }

    } catch (error) {
        console.error('Error:', error);
        Toast.fire({
            icon: 'error',
            title: 'Error al crear el respaldo'
        });
    } finally {
        BtnRespaldo.disabled = false;
        BtnRespaldo.innerHTML = '<i class="bi bi-download me-2"></i>Crear Respaldo de BD';
    }
};

// FUNCIÓN PARA LIMPIAR LOGS (simulada)
const limpiarLogs = async () => {
    const confirmacion = await Swal.fire({
        title: '¿Limpiar logs?',
        text: 'Se eliminarán todos los logs antiguos del sistema',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, limpiar',
        cancelButtonText: 'Cancelar'
    });

    if (!confirmacion.isConfirmed) return;

    // Simulación de limpieza de logs
    Toast.fire({
        icon: 'success',
        title: 'Logs del sistema limpiados exitosamente'
    });
};

// FUNCIÓN PARA ACTUALIZAR ESTADÍSTICAS
const actualizarEstadisticas = async () => {
    Toast.fire({
        icon: 'info',
        title: 'Actualizando estadísticas...'
    });

    // Recargar la configuración para obtener estadísticas actualizadas
    await cargarConfiguracion();
};

// EVENT LISTENERS
BtnCargarConfiguracion.addEventListener('click', cargarConfiguracion);
BtnRespaldo.addEventListener('click', crearRespaldo);
BtnLimpiarLogs.addEventListener('click', limpiarLogs);
BtnActualizarStats.addEventListener('click', actualizarEstadisticas);

// CARGAR CONFIGURACIÓN AL INICIALIZAR
document.addEventListener('DOMContentLoaded', () => {
    cargarConfiguracion();
});

console.log('Módulo de configuración inicializado correctamente');