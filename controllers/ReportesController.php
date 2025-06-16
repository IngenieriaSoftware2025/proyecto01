<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;

class ReportesController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        isAuth(); // Verificar autenticación
        $router->render('reportes/index', []);
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
            // Estadísticas generales
            $estadisticas = [
                'total_dispositivos' => 0,
                'stock_total' => 0,
                'valor_inventario' => 0,
                'dispositivos_bajo_stock' => 0,
                'marcas_activas' => 0
            ];

            // Total de dispositivos
            $query1 = "SELECT COUNT(*) as total FROM inventario WHERE situacion = 1";
            $result1 = self::fetchFirst($query1);
            $estadisticas['total_dispositivos'] = $result1['total'] ?? 0;

            // Stock total
            $query2 = "SELECT SUM(stock_disponible) as stock FROM inventario WHERE situacion = 1";
            $result2 = self::fetchFirst($query2);
            $estadisticas['stock_total'] = $result2['stock'] ?? 0;

            // Valor del inventario
            $query3 = "SELECT SUM(precio_venta * stock_disponible) as valor FROM inventario WHERE situacion = 1";
            $result3 = self::fetchFirst($query3);
            $estadisticas['valor_inventario'] = $result3['valor'] ?? 0;

            // Dispositivos con bajo stock (≤ 5)
            $query4 = "SELECT COUNT(*) as bajo_stock FROM inventario WHERE situacion = 1 AND stock_disponible <= 5";
            $result4 = self::fetchFirst($query4);
            $estadisticas['dispositivos_bajo_stock'] = $result4['bajo_stock'] ?? 0;

            // Marcas activas
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

    // MÉTODO PARA OBTENER ESTADÍSTICAS GENERALES
    public static function estadisticasGeneralesAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);
        getHeadersApi();

        try {
            $estadisticas = [];

            // Total de clientes activos
            $queryClientes = "SELECT COUNT(*) as total FROM clientes WHERE situacion IN (1,2,3)";
            $resultClientes = self::fetchFirst($queryClientes);
            $estadisticas['total_clientes'] = $resultClientes['total'] ?? 0;

            // Total de ventas del mes actual
            $queryVentas = "SELECT COUNT(*) as total, SUM(total) as monto 
                           FROM ventas 
                           WHERE situacion = 1 
                           AND MONTH(fecha_venta) = MONTH(TODAY)
                           AND YEAR(fecha_venta) = YEAR(TODAY)";
            $resultVentas = self::fetchFirst($queryVentas);
            $estadisticas['ventas_mes'] = $resultVentas['total'] ?? 0;
            $estadisticas['monto_ventas_mes'] = $resultVentas['monto'] ?? 0;

            // Reparaciones pendientes
            $queryReparaciones = "SELECT COUNT(*) as total FROM reparaciones 
                                 WHERE situacion = 1 AND estado NOT IN ('ENTREGADO', 'CANCELADO')";
            $resultReparaciones = self::fetchFirst($queryReparaciones);
            $estadisticas['reparaciones_pendientes'] = $resultReparaciones['total'] ?? 0;

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
}