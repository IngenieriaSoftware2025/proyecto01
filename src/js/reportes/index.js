import Chart from "chart.js/auto";
import Swal from "sweetalert2";

let graficoVentasMes = null;
let graficoEstadoReparaciones = null;
let graficoMarcasVendidas = null;
let graficoInventarioEstado = null;


const BtnActualizarDashboard = document.getElementById('BtnActualizarDashboard');
const mostrarError = (mensaje) => {
    console.error('Error:', mensaje);
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: mensaje,
        confirmButtonText: 'Entendido'
    });
};

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

const actualizarTimestamp = () => {
    const elemento = document.getElementById('ultimaActualizacion');
    if (elemento) {
        elemento.textContent = new Date().toLocaleString('es-GT');
    }
};

const cargarGraficoVentasMes = async () => {
    try {
        const respuesta = await fetch('/proyecto01/reportes/ventasPorMes');
        const datos = await respuesta.json();
        
        console.log('Datos ventas por mes:', datos);
        
        if (datos.codigo === 1 && validarDatos(datos.data, 'cargarGraficoVentasMes')) {
            const ctx = document.getElementById('graficoVentasMes');
            if (!ctx) {
                console.error('Elemento graficoVentasMes no encontrado');
                return;
            }
            
            if (graficoVentasMes) {
                graficoVentasMes.destroy();
            }
            
            const labels = datos.data.map(item => item.label);
            const ventas = datos.data.map(item => item.ventas);
            const montos = datos.data.map(item => parseFloat(item.monto || 0));
            
            graficoVentasMes = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Cantidad de Ventas',
                            data: ventas,
                            backgroundColor: 'rgba(54, 162, 235, 0.8)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Ingresos (Q)',
                            data: montos,
                            backgroundColor: 'rgba(255, 99, 132, 0.8)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 2,
                            yAxisID: 'y1',
                            type: 'line'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Meses'
                            }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Cantidad de Ventas'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Ingresos (Q)'
                            },
                            grid: {
                                drawOnChartArea: false,
                            },
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Ventas y Ingresos Mensuales'
                        },
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    }
                }
            });
        } else {
            console.warn('No hay datos válidos para el gráfico de ventas por mes');
        }
    } catch (error) {
        console.error('Error al cargar gráfico de ventas por mes:', error);
    }
};

const cargarGraficoEstadoReparaciones = async () => {
    try {
        const respuesta = await fetch('/proyecto01/reportes/estadoReparaciones');
        const datos = await respuesta.json();
        
        console.log('Datos estado reparaciones:', datos);
        
        if (datos.codigo === 1 && validarDatos(datos.data, 'cargarGraficoEstadoReparaciones')) {
            const ctx = document.getElementById('graficoEstadoReparaciones');
            if (!ctx) {
                console.error('Elemento graficoEstadoReparaciones no encontrado');
                return;
            }
            
            if (graficoEstadoReparaciones) {
                graficoEstadoReparaciones.destroy();
            }
            
            const labels = datos.data.map(item => item.label);
            const values = datos.data.map(item => item.value);
            const colores = [
                '#6c757d', 
                '#17a2b8', 
                '#ffc107', 
                '#007bff', 
                '#28a745', 
                '#343a40', 
                '#dc3545'  
            ];
            
            graficoEstadoReparaciones = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colores.slice(0, labels.length),
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
                            text: 'Distribución de Estados'
                        },
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        } else {
            console.warn('No hay datos válidos para el gráfico de estado de reparaciones');
        }
    } catch (error) {
        console.error('Error al cargar gráfico de estado de reparaciones:', error);
    }
};

const cargarGraficoMarcasVendidas = async () => {
    try {
        const respuesta = await fetch('/proyecto01/reportes/marcasMasVendidas');
        const datos = await respuesta.json();
        
        console.log('Datos marcas más vendidas:', datos);
        
        if (datos.codigo === 1 && validarDatos(datos.data, 'cargarGraficoMarcasVendidas')) {
            const ctx = document.getElementById('graficoMarcasVendidas');
            if (!ctx) {
                console.error('Elemento graficoMarcasVendidas no encontrado');
                return;
            }
            
            if (graficoMarcasVendidas) {
                graficoMarcasVendidas.destroy();
            }
            
            const labels = datos.data.map(item => item.label);
            const values = datos.data.map(item => item.value);
            const backgroundColors = labels.map((_, index) => {
                const hue = (index * 72) % 360; 
                return `hsla(${hue}, 70%, 60%, 0.8)`;
            });
            
            const borderColors = backgroundColors.map(color => 
                color.replace('0.8', '1')
            );
            
            graficoMarcasVendidas = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Unidades Vendidas',
                        data: values,
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y', 
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Unidades Vendidas'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Marcas'
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Top 5 Marcas por Ventas'
                        },
                        legend: {
                            display: false
                        }
                    }
                }
            });
        } else {
            console.warn('No hay datos válidos para el gráfico de marcas más vendidas');
        }
    } catch (error) {
        console.error('Error al cargar gráfico de marcas más vendidas:', error);
    }
};

const cargarGraficoInventarioEstado = async () => {
    try {
        const respuesta = await fetch('/proyecto01/reportes/inventarioPorEstado');
        const datos = await respuesta.json();
        
        console.log('Datos inventario por estado:', datos);
        
        if (datos.codigo === 1 && validarDatos(datos.data, 'cargarGraficoInventarioEstado')) {
            const ctx = document.getElementById('graficoInventarioEstado');
            if (!ctx) {
                console.error('Elemento graficoInventarioEstado no encontrado');
                return;
            }
            
            if (graficoInventarioEstado) {
                graficoInventarioEstado.destroy();
            }
            
            const labels = datos.data.map(item => item.label);
            const values = datos.data.map(item => item.value);
            const colores = [
                '#28a745', 
                '#ffc107',
                '#dc3545', 
                '#6c757d',
                '#17a2b8'  
            ];
            
            graficoInventarioEstado = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colores.slice(0, labels.length),
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
                            text: 'Distribución por Estado'
                        },
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        } else {
            console.warn('No hay datos válidos para el gráfico de inventario por estado');
        }
    } catch (error) {
        console.error('Error al cargar gráfico de inventario por estado:', error);
    }
};

const cargarMetricasPrincipales = async () => {
    try {
        const respuesta = await fetch('/proyecto01/reportes/estadisticasGenerales');
        const datos = await respuesta.json();
        
        console.log('Métricas recibidas:', datos);
        
        if (datos.codigo === 1 && datos.data) {
            const { data } = datos;
            const elementos = {
                'total-ventas-hoy': data.ventas_hoy || 0,
                'ingresos-hoy': `Q${parseFloat(data.monto_ventas_hoy || 0).toFixed(2)}`,
                'total-clientes': data.total_clientes || 0,
                'reparaciones-pendientes': data.reparaciones_pendientes || 0,
                'total-dispositivos': data.total_dispositivos || 0,
                'stock-total': data.stock_total || 0,
                'valor-inventario': `Q${parseFloat(data.valor_inventario || 0).toFixed(2)}`
            };
            
            Object.entries(elementos).forEach(([id, valor]) => {
                const elemento = document.getElementById(id);
                if (elemento) {
                    elemento.textContent = valor;
                }
            });
        } else {
            mostrarError('Error al cargar métricas principales: ' + (datos.mensaje || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error al cargar métricas:', error);
        mostrarError('Error de conexión al cargar métricas');
    }
};

const cargarDashboard = async () => {
    console.log('Iniciando carga del dashboard...');
    Swal.fire({
        title: 'Cargando Dashboard',
        text: 'Obteniendo datos actualizados...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        await cargarMetricasPrincipales();
        await Promise.all([
            cargarGraficoVentasMes(),
            cargarGraficoEstadoReparaciones(),
            cargarGraficoMarcasVendidas(),
            cargarGraficoInventarioEstado()
        ]);

        actualizarTimestamp();
        
        console.log('Dashboard cargado exitosamente');
        
        Swal.fire({
            icon: 'success',
            title: 'Dashboard Actualizado',
            text: 'Todos los datos se han cargado correctamente',
            timer: 2000,
            showConfirmButton: false
        });
        
    } catch (error) {
        console.error('Error al cargar dashboard:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error al Cargar Dashboard',
            text: 'Ocurrió un error al actualizar los datos',
            confirmButtonText: 'Reintentar'
        });
    }
};

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM cargado, iniciando dashboard...');
    
    setTimeout(() => {
        cargarDashboard();
    }, 500);
    
    if (BtnActualizarDashboard) {
        BtnActualizarDashboard.addEventListener('click', (e) => {
            e.preventDefault();
            cargarDashboard();
        });
    }
    
    setInterval(() => {
        console.log('Auto-actualizando dashboard...');
        cargarDashboard();
    }, 300000); // 5 minutos
});

window.addEventListener('error', (event) => {
    console.error('Error global capturado:', event.error);
});

console.log('Módulo de dashboard con 4 gráficas inicializado correctamente');