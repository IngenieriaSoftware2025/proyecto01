import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from "../funciones";
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";
import Chart from "chart.js/auto";

// Variables globales para las gráficas
let graficoVentas = null;
let graficoReparaciones = null;
let graficoMarcasVendidas = null;

// Elementos del DOM
const BtnActualizarDashboard = document.getElementById('BtnActualizarDashboard');

// Función para mostrar mensajes de error
const mostrarError = (mensaje) => {
    console.error('Error:', mensaje);
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: mensaje,
        confirmButtonText: 'Entendido'
    });
};

// Función para validar datos antes de procesarlos
const validarDatos = (datos, nombreFuncion = '') => {
    if (!datos) {
        console.warn(`${nombreFuncion}: datos es null o undefined`);
        return false;
    }
    if (!Array.isArray(datos)) {
        console.warn(`${nombreFuncion}: datos no es un array`);
        return false;
    }
    return true;
};

// Función para actualizar métricas principales
const cargarMetricasPrincipales = async () => {
    try {
        const respuesta = await fetch('/proyecto01/reportes/metricas');
        const datos = await respuesta.json();
        
        console.log('Métricas recibidas:', datos);
        
        if (datos.codigo === 1) {
            const { data } = datos;
            
            // Actualizar tarjetas de métricas con validación
            const elementoVentasHoy = document.getElementById('total-ventas-hoy');
            const elementoIngresosHoy = document.getElementById('ingresos-hoy');
            const elementoClientes = document.getElementById('total-clientes');
            const elementoDispositivos = document.getElementById('total-dispositivos');
            const elementoStockTotal = document.getElementById('stock-total');
            const elementoReparaciones = document.getElementById('reparaciones-pendientes');
            const elementoValorInventario = document.getElementById('valor-inventario');
            
            if (elementoVentasHoy) elementoVentasHoy.textContent = (data?.ventas_hoy?.total_ventas_hoy || '0');
            if (elementoIngresosHoy) elementoIngresosHoy.textContent = `Q${parseFloat(data?.ventas_hoy?.ingresos_hoy || 0).toFixed(2)}`;
            if (elementoClientes) elementoClientes.textContent = (data?.clientes?.total_clientes_activos || '0');
            if (elementoDispositivos) elementoDispositivos.textContent = (data?.inventario?.total_dispositivos || '0');
            if (elementoStockTotal) elementoStockTotal.textContent = `Stock: ${data?.inventario?.stock_total || '0'}`;
            if (elementoReparaciones) elementoReparaciones.textContent = (data?.reparaciones?.reparaciones_pendientes || '0');
            if (elementoValorInventario) elementoValorInventario.textContent = `Q${parseFloat(data?.valor_inventario?.valor_inventario || 0).toFixed(2)}`;
            
        } else {
            mostrarError('Error al cargar métricas principales: ' + (datos.mensaje || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error al cargar métricas:', error);
        mostrarError('Error de conexión al cargar métricas');
    }
};

// Función para cargar datos de ventas y crear gráfico
const cargarGraficoVentas = async (tipo = 'ventas_diarias') => {
    try {
        const respuesta = await fetch(`/proyecto01/reportes/graficos?tipo=${tipo}`);
        const datos = await respuesta.json();
        
        console.log('Datos gráfico ventas:', datos);
        
        if (datos.codigo === 1 && validarDatos(datos.data, 'cargarGraficoVentas')) {
            const ctx = document.getElementById('graficoVentas');
            if (!ctx) {
                console.error('Elemento graficoVentas no encontrado');
                return;
            }
            
            if (graficoVentas) {
                graficoVentas.destroy();
            }
            
            // Preparar datos con validación
            const labels = datos.data.map(item => {
                if (item.label) {
                    const fecha = new Date(item.label);
                    return isNaN(fecha.getTime()) ? item.label : fecha.toLocaleDateString('es-GT');
                }
                return 'Sin fecha';
            });
            
            const values = datos.data.map(item => parseFloat(item.value || 0));
            
            graficoVentas = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Ingresos (Q)',
                        data: values,
                        backgroundColor: 'rgba(13, 110, 253, 0.2)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Ingresos (Q)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Fecha'
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Tendencia de Ventas'
                        }
                    }
                }
            });
        } else {
            console.warn('No hay datos válidos para el gráfico de ventas');
        }
    } catch (error) {
        console.error('Error al cargar gráfico de ventas:', error);
    }
};

// Función para cargar gráfico de reparaciones por estado
const cargarGraficoReparaciones = async () => {
    try {
        const respuesta = await fetch('/proyecto01/reportes/graficos?tipo=reparaciones_estado');
        const datos = await respuesta.json();
        
        console.log('Datos gráfico reparaciones:', datos);
        
        if (datos.codigo === 1 && validarDatos(datos.data, 'cargarGraficoReparaciones')) {
            const ctx = document.getElementById('graficoReparaciones');
            if (!ctx) {
                console.error('Elemento graficoReparaciones no encontrado');
                return;
            }
            
            if (graficoReparaciones) {
                graficoReparaciones.destroy();
            }
            
            // Colores para diferentes estados
            const colores = {
                'RECIBIDO': '#6c757d',
                'EN_DIAGNOSTICO': '#17a2b8',
                'DIAGNOSTICADO': '#ffc107',
                'EN_REPARACION': '#007bff',
                'REPARADO': '#28a745',
                'ENTREGADO': '#343a40',
                'CANCELADO': '#dc3545'
            };
            
            const labels = datos.data.map(item => item.label || 'Sin estado');
            const values = datos.data.map(item => parseInt(item.value || 0));
            const backgroundColors = labels.map(label => colores[label] || '#6c757d');
            
            graficoReparaciones = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: backgroundColors,
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Reparaciones por Estado'
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        } else {
            console.warn('No hay datos válidos para el gráfico de reparaciones');
        }
    } catch (error) {
        console.error('Error al cargar gráfico de reparaciones:', error);
    }
};

// Función para cargar gráfico de marcas más vendidas
const cargarGraficoMarcasVendidas = async () => {
    try {
        const respuesta = await fetch('/proyecto01/reportes/graficos?tipo=marcas_vendidas');
        const datos = await respuesta.json();
        
        console.log('Datos gráfico marcas:', datos);
        
        if (datos.codigo === 1 && validarDatos(datos.data, 'cargarGraficoMarcasVendidas')) {
            const ctx = document.getElementById('graficoMarcasVendidas');
            if (!ctx) {
                console.error('Elemento graficoMarcasVendidas no encontrado');
                return;
            }
            
            if (graficoMarcasVendidas) {
                graficoMarcasVendidas.destroy();
            }
            
            // Tomar solo las primeras 10 marcas con validación
            const topMarcas = datos.data.slice(0, 10);
            const labels = topMarcas.map(item => item.label || 'Sin marca');
            const values = topMarcas.map(item => parseInt(item.value || 0));
            
            // Generar colores dinámicos
            const backgroundColors = labels.map((_, index) => {
                const hue = (index * 360) / labels.length;
                return `hsla(${hue}, 70%, 60%, 0.8)`;
            });
            
            graficoMarcasVendidas = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Unidades Vendidas',
                        data: values,
                        backgroundColor: backgroundColors,
                        borderColor: backgroundColors.map(color => color.replace('0.8', '1')),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Unidades Vendidas'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Marcas'
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Top 10 Marcas Más Vendidas'
                        }
                    }
                }
            });
        } else {
            console.warn('No hay datos válidos para el gráfico de marcas');
        }
    } catch (error) {
        console.error('Error al cargar gráfico de marcas:', error);
    }
};

// Función para cargar ranking de vendedores
const cargarRankingVendedores = async () => {
    try {
        const respuesta = await fetch('/proyecto01/reportes/ventas?periodo=30');
        const datos = await respuesta.json();
        
        console.log('Datos ranking vendedores:', datos);
        
        const contenedor = document.getElementById('ranking-vendedores');
        if (!contenedor) {
            console.error('Elemento ranking-vendedores no encontrado');
            return;
        }
        
        if (datos.codigo === 1 && datos.data && validarDatos(datos.data.ranking_vendedores, 'cargarRankingVendedores')) {
            if (datos.data.ranking_vendedores.length === 0) {
                contenedor.innerHTML = '<div class="text-center text-muted p-4">No hay datos de vendedores disponibles</div>';
                return;
            }
            
            let html = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Vendedor</th><th>Ventas</th><th>Ingresos</th></tr></thead><tbody>';
            
            datos.data.ranking_vendedores.forEach((vendedor, index) => {
                const badgeClass = index === 0 ? 'bg-warning' : index === 1 ? 'bg-secondary' : index === 2 ? 'bg-dark' : 'bg-light text-dark';
                html += `
                    <tr>
                        <td>
                            <span class="badge ${badgeClass} me-2">${index + 1}</span>
                            ${vendedor.vendedor || 'Sin nombre'}
                        </td>
                        <td>${vendedor.total_ventas || 0}</td>
                        <td class="text-success fw-bold">Q${parseFloat(vendedor.total_ingresos || 0).toFixed(2)}</td>
                    </tr>
                `;
            });
            
            html += '</tbody></table></div>';
            contenedor.innerHTML = html;
        } else {
            contenedor.innerHTML = '<div class="text-center text-muted p-4">No hay datos de vendedores disponibles</div>';
        }
    } catch (error) {
        console.error('Error al cargar ranking de vendedores:', error);
        const contenedor = document.getElementById('ranking-vendedores');
        if (contenedor) {
            contenedor.innerHTML = '<div class="text-center text-danger p-4">Error al cargar datos</div>';
        }
    }
};

// Función para cargar análisis de inventario
const cargarAnalisisInventario = async () => {
    try {
        const respuesta = await fetch('/proyecto01/reportes/inventario');
        const datos = await respuesta.json();
        
        console.log('Datos análisis inventario:', datos);
        
        if (datos.codigo === 1 && datos.data) {
            // Cargar stock bajo
            const stockBajoContainer = document.getElementById('stock-bajo');
            if (stockBajoContainer) {
                if (validarDatos(datos.data.stock_bajo, 'stock_bajo') && datos.data.stock_bajo.length > 0) {
                    let html = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Dispositivo</th><th>Serie</th><th>Stock</th><th>Precio</th></tr></thead><tbody>';
                    
                    datos.data.stock_bajo.forEach(item => {
                        const alertClass = (item.stock_disponible || 0) <= 2 ? 'table-danger' : 'table-warning';
                        html += `
                            <tr class="${alertClass}">
                                <td><strong>${item.marca_nombre || 'Sin marca'}</strong><br><small>${item.marca_modelo || 'Sin modelo'}</small></td>
                                <td><code class="small">${item.numero_serie || 'Sin serie'}</code></td>
                                <td><span class="badge bg-danger">${item.stock_disponible || 0}</span></td>
                                <td>Q${parseFloat(item.precio_venta || 0).toFixed(2)}</td>
                            </tr>
                        `;
                    });
                    
                    html += '</tbody></table></div>';
                    stockBajoContainer.innerHTML = html;
                } else {
                    stockBajoContainer.innerHTML = '<div class="text-center text-success p-4"><i class="bi bi-check-circle fs-1"></i><br>No hay productos con stock bajo</div>';
                }
            }
            
            // Cargar valor por marca
            const valorMarcaContainer = document.getElementById('valor-por-marca');
            if (valorMarcaContainer) {
                if (validarDatos(datos.data.valor_por_marca, 'valor_por_marca') && datos.data.valor_por_marca.length > 0) {
                    let html = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Marca</th><th>Dispositivos</th><th>Stock</th><th>Valor Total</th></tr></thead><tbody>';
                    
                    datos.data.valor_por_marca.forEach(item => {
                        html += `
                            <tr>
                                <td><strong>${item.marca || 'Sin marca'}</strong></td>
                                <td>${item.cantidad_dispositivos || 0}</td>
                                <td>${item.stock_total || 0}</td>
                                <td class="text-success fw-bold">Q${parseFloat(item.valor_total || 0).toFixed(2)}</td>
                            </tr>
                        `;
                    });
                    
                    html += '</tbody></table></div>';
                    valorMarcaContainer.innerHTML = html;
                } else {
                    valorMarcaContainer.innerHTML = '<div class="text-center text-muted p-4">No hay datos de valor por marca</div>';
                }
            }
        }
    } catch (error) {
        console.error('Error al cargar análisis de inventario:', error);
    }
};

// Función principal para cargar todo el dashboard
const cargarDashboard = async () => {
    console.log('Iniciando carga del dashboard...');
    
    // Mostrar indicadores de carga
    document.querySelectorAll('.loading-placeholder').forEach(element => {
        element.innerHTML = '<div class="text-center p-4"><div class="spinner-border" role="status"></div></div>';
    });
    
    try {
        // Cargar métricas principales primero
        await cargarMetricasPrincipales();
        
        // Luego cargar gráficos
        await cargarGraficoVentas();
        await cargarGraficoReparaciones();
        await cargarGraficoMarcasVendidas();
        
        // Finalmente cargar datos de tablas
        await cargarRankingVendedores();
        await cargarAnalisisInventario();
        
        console.log('Dashboard cargado exitosamente');
        
        Swal.fire({
            icon: 'success',
            title: 'Dashboard actualizado',
            text: 'Los datos disponibles se han cargado correctamente',
            timer: 1500,
            showConfirmButton: false
        });
        
    } catch (error) {
        console.error('Error al cargar dashboard:', error);
        mostrarError('Error al actualizar el dashboard');
    }
};

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM cargado, iniciando dashboard...');
    
    // Cargar dashboard inicial
    setTimeout(() => {
        cargarDashboard();
    }, 500);
    
    // Botón actualizar dashboard
    if (BtnActualizarDashboard) {
        BtnActualizarDashboard.addEventListener('click', cargarDashboard);
    }
    
    // Filtros de período
    document.querySelectorAll('.periodo-filter').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            cargarDashboard();
        });
    });
    
    // Botones de tipo de gráfico de ventas
    document.querySelectorAll('.grafico-periodo').forEach(btn => {
        btn.addEventListener('click', (e) => {
            document.querySelectorAll('.grafico-periodo').forEach(b => b.classList.remove('active'));
            e.target.classList.add('active');
            
            const tipo = e.target.dataset.tipo;
            cargarGraficoVentas(tipo);
        });
    });
});

console.log('Módulo de reportes y dashboard inicializado correctamente');