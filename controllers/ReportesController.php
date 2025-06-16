<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;

class ReportesController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        isAuth();
        $router->render('reportes/index', []);
    }


    // 1. VENTAS POR MES (Gráfico de Barras)
    public static function ventasPorMesAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);
        getHeadersApi();

        try {
            $consulta = "SELECT 
            fecha_venta,
            COUNT(*) as cantidad_ventas,
            NVL(SUM(total), 0) as total_ingresos
            FROM ventas 
            WHERE fecha_venta >= (TODAY - 180 UNITS DAY) 
            AND situacion = 1 
            AND estado = 'COMPLETADA'
            GROUP BY fecha_venta
            ORDER BY fecha_venta DESC";

            $datos = self::fetchArray($consulta);

            $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            $datosFormateados = [];

            foreach ($datos as $dato) {
                $datosFormateados[] = [
                    'label' => $meses[$dato['mes'] - 1],
                    'ventas' => $dato['total_ventas'],
                    'monto' => $dato['monto_total']
                ];
            }

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Datos de ventas por mes obtenidos exitosamente',
                'data' => $datosFormateados
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener datos de ventas por mes',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // 2. ESTADO DE REPARACIONES (Gráfico de Dona)
    public static function estadoReparacionesAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);
        getHeadersApi();

        try {
            $consulta = "SELECT 
                            estado,
                            COUNT(*) as cantidad
                        FROM reparaciones 
                        WHERE situacion = 1 
                        GROUP BY estado
                        ORDER BY cantidad DESC";

            $datos = self::fetchArray($consulta);

            $estadosFormateados = [];
            foreach ($datos as $dato) {
                $estadoLabel = str_replace('_', ' ', $dato['estado']);
                $estadosFormateados[] = [
                    'label' => ucwords(strtolower($estadoLabel)),
                    'value' => $dato['cantidad'],
                    'estado_original' => $dato['estado']
                ];
            }

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Datos de estado de reparaciones obtenidos exitosamente',
                'data' => $estadosFormateados
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener datos de estado de reparaciones',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // 3. MARCAS MÁS VENDIDAS (Gráfico de Barras Horizontal)
    public static function marcasMasVendidasAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);
        getHeadersApi();

        try {
            $consulta = "SELECT 
                            m.nombre as marca,
                            SUM(vd.cantidad) as total_vendido
                        FROM venta_detalle vd
                        INNER JOIN inventario i ON vd.inventario_id = i.id
                        INNER JOIN marcas m ON i.marca_id = m.id
                        INNER JOIN ventas v ON vd.venta_id = v.venta_id
                        WHERE v.situacion = 1
                        GROUP BY m.nombre
                        ORDER BY total_vendido DESC
                        LIMIT 5";

            $datos = self::fetchArray($consulta);

            $datosFormateados = [];
            foreach ($datos as $dato) {
                $datosFormateados[] = [
                    'label' => $dato['fecha_venta'],
                    'ventas' => $dato['cantidad_ventas'],
                    'monto' => $dato['total_ingresos']
                ];
            }

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Datos de marcas más vendidas obtenidos exitosamente',
                'data' => $datosFormateados
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener datos de marcas más vendidas',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // 4. INVENTARIO POR ESTADO (Gráfico de Pastel)
    public static function inventarioPorEstadoAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);
        getHeadersApi();

        try {
            $consulta = "SELECT 
                            estado_dispositivo as estado,
                            SUM(stock_disponible) as cantidad
                        FROM inventario 
                        WHERE situacion = 1 
                        GROUP BY estado_dispositivo
                        ORDER BY cantidad DESC";

            $datos = self::fetchArray($consulta);

            $datosFormateados = [];
            foreach ($datos as $dato) {
                $datosFormateados[] = [
                    'label' => ucwords(strtolower($dato['estado'])),
                    'value' => $dato['cantidad']
                ];
            }

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Datos de inventario por estado obtenidos exitosamente',
                'data' => $datosFormateados
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener datos de inventario por estado',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTRICAS PRINCIPALES DEL DASHBOARD
    public static function estadisticasGeneralesAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);
        getHeadersApi();

        try {
            $estadisticas = [];

            $queryClientes = "SELECT COUNT(*) as total FROM clientes WHERE situacion IN (1,2,3)";
            $resultClientes = self::fetchFirst($queryClientes);
            $estadisticas['total_clientes'] = $resultClientes['total'] ?? 0;

            $queryVentasHoy = "SELECT COUNT(*) as total, SUM(total) as monto 
                                FROM ventas 
                                WHERE situacion = 1 AND fecha_venta = TODAY";
            $resultVentasHoy = self::fetchFirst($queryVentasHoy);
            $estadisticas['ventas_hoy'] = $resultVentasHoy['total'] ?? 0;
            $estadisticas['monto_ventas_hoy'] = $resultVentasHoy['monto'] ?? 0;

            $queryVentas = "SELECT COUNT(*) as total, SUM(total) as monto 
                           FROM ventas 
                           WHERE situacion = 1 
                           AND MONTH(fecha_venta) = MONTH(TODAY)
                           AND YEAR(fecha_venta) = YEAR(TODAY)";
            $resultVentas = self::fetchFirst($queryVentas);
            $estadisticas['ventas_mes'] = $resultVentas['total'] ?? 0;
            $estadisticas['monto_ventas_mes'] = $resultVentas['monto'] ?? 0;

            $queryReparaciones = "SELECT COUNT(*) as total FROM reparaciones 
                                 WHERE situacion = 1 AND estado NOT IN ('ENTREGADO', 'CANCELADO')";
            $resultReparaciones = self::fetchFirst($queryReparaciones);
            $estadisticas['reparaciones_pendientes'] = $resultReparaciones['total'] ?? 0;

            $queryInventario = "SELECT COUNT(*) as total_dispositivos, SUM(stock_disponible) as stock_total 
                               FROM inventario WHERE situacion = 1";
            $resultInventario = self::fetchFirst($queryInventario);
            $estadisticas['total_dispositivos'] = $resultInventario['total_dispositivos'] ?? 0;
            $estadisticas['stock_total'] = $resultInventario['stock_total'] ?? 0;

            $queryValor = "SELECT SUM(precio_venta * stock_disponible) as valor_inventario 
                          FROM inventario WHERE situacion = 1";
            $resultValor = self::fetchFirst($queryValor);
            $estadisticas['valor_inventario'] = $resultValor['valor_inventario'] ?? 0;

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Estadísticas generales obtenidas exitosamente',
                'data' => $estadisticas
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener estadísticas generales',
                'detalle' => $e->getMessage()
            ]);
        }
    }


    // MÉTODO PARA OBTENER DATOS DE VENTAS POR PERÍODO
    public static function datosGraficoVentasAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);
        getHeadersApi();

        try {
            $consulta = "SELECT 
                            TO_CHAR(fecha_venta, '%m/%Y') as periodo,
                            COUNT(*) as total_ventas,
                            SUM(total) as monto_total
                        FROM ventas 
                        WHERE situacion = 1 
                        AND fecha_venta >= (TODAY - 180) 
                        GROUP BY TO_CHAR(fecha_venta, '%m/%Y')
                        ORDER BY TO_CHAR(fecha_venta, '%Y%m')";

            $datos = self::fetchArray($consulta);

            if (count($datos) > 0) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Datos para gráficos obtenidos exitosamente',
                    'data' => $datos
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'No hay datos de ventas disponibles',
                    'data' => []
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener datos de ventas',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTODO PARA OBTENER DATOS DE REPARACIONES POR ESTADO
    public static function datosGraficoReparacionesAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);
        getHeadersApi();

        try {
            $consulta = "SELECT 
                            estado,
                            COUNT(*) as cantidad
                        FROM reparaciones 
                        WHERE situacion = 1 
                        GROUP BY estado
                        ORDER BY cantidad DESC";

            $datos = self::fetchArray($consulta);

            if (count($datos) > 0) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Datos para gráficos obtenidos exitosamente',
                    'data' => $datos
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'No hay datos de reparaciones disponibles',
                    'data' => []
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener datos de reparaciones',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTODO PARA OBTENER MARCAS MÁS VENDIDAS
    public static function datosGraficoMarcasAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);
        getHeadersApi();

        try {
            $consulta = "SELECT 
                            m.nombre as marca,
                            SUM(vd.cantidad) as total_vendido
                        FROM venta_detalle vd
                        INNER JOIN inventario i ON vd.inventario_id = i.id
                        INNER JOIN marcas m ON i.marca_id = m.id
                        INNER JOIN ventas v ON vd.venta_id = v.venta_id
                        WHERE v.situacion = 1
                        GROUP BY m.nombre
                        ORDER BY total_vendido DESC
                        LIMIT 10";

            $datos = self::fetchArray($consulta);

            if (count($datos) > 0) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Datos para gráficos obtenidos exitosamente',
                    'data' => $datos
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'No hay datos de marcas vendidas disponibles',
                    'data' => []
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener datos de marcas',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTODO PARA OBTENER RANKING DE VENDEDORES
    public static function rankingVendedoresAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);
        getHeadersApi();

        try {
            $consulta = "SELECT 
                            u.usu_nombre as vendedor,
                            COUNT(v.venta_id) as total_ventas,
                            SUM(v.total) as monto_total,
                            AVG(v.total) as promedio_venta
                        FROM ventas v
                        INNER JOIN usuario_login2025 u ON v.usuario_id = u.usu_id
                        WHERE v.situacion = 1
                        AND v.fecha_venta >= (TODAY - 90)
                        GROUP BY u.usu_nombre, u.usu_id
                        ORDER BY monto_total DESC
                        LIMIT 10";

            $datos = self::fetchArray($consulta);

            if (count($datos) > 0) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Reporte de ventas obtenido exitosamente',
                    'data' => [
                        'vendedores' => $datos,
                        'total_general' => array_sum(array_column($datos, 'monto_total'))
                    ]
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'No hay datos de vendedores disponibles',
                    'data' => [
                        'vendedores' => [],
                        'total_general' => 0
                    ]
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener ranking de vendedores',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTODO PARA ANÁLISIS DE INVENTARIO
    public static function analisisInventarioAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);
        getHeadersApi();

        try {
            $estadisticas = [
                'total_dispositivos' => 0,
                'stock_total' => 0,
                'valor_inventario' => 0,
                'dispositivos_bajo_stock' => 0,
                'marcas_activas' => 0
            ];

            $query1 = "SELECT COUNT(*) as total FROM inventario WHERE situacion = 1";
            $result1 = self::fetchFirst($query1);
            $estadisticas['total_dispositivos'] = $result1['total'] ?? 0;

            $query2 = "SELECT SUM(stock_disponible) as stock FROM inventario WHERE situacion = 1";
            $result2 = self::fetchFirst($query2);
            $estadisticas['stock_total'] = $result2['stock'] ?? 0;

            $query3 = "SELECT SUM(precio_venta * stock_disponible) as valor FROM inventario WHERE situacion = 1";
            $result3 = self::fetchFirst($query3);
            $estadisticas['valor_inventario'] = $result3['valor'] ?? 0;

            $query4 = "SELECT COUNT(*) as bajo_stock FROM inventario WHERE situacion = 1 AND stock_disponible <= 5";
            $result4 = self::fetchFirst($query4);
            $estadisticas['dispositivos_bajo_stock'] = $result4['bajo_stock'] ?? 0;

            $query5 = "SELECT COUNT(DISTINCT marca_id) as marcas FROM inventario WHERE situacion = 1";
            $result5 = self::fetchFirst($query5);
            $estadisticas['marcas_activas'] = $result5['marcas'] ?? 0;

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Análisis de inventario obtenido exitosamente',
                'data' => $estadisticas
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener análisis de inventario',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
