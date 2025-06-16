<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;

class ConfiguracionController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        isAuth(); // Verificar autenticación
        hasPermission(['ADMIN']); // Solo administradores
        $router->render('configuracion/index', []);
    }

    // MÉTODO PARA OBTENER CONFIGURACIONES
    public static function obtenerConfiguracionAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();

        try {
            // Configuraciones básicas del sistema
            $configuracion = [
                'sistema' => [
                    'nombre' => 'Sistema de Gestión de Celulares',
                    'version' => '1.0.0',
                    'desarrollador' => 'Tu Empresa',
                    'ultima_actualizacion' => date('Y-m-d H:i:s')
                ],
                'base_datos' => [
                    'host' => $_ENV['DB_HOST'] ?? 'localhost',
                    'servidor' => $_ENV['DB_SERVER'] ?? 'N/A',
                    'nombre_bd' => $_ENV['DB_NAME'] ?? 'celulares_db'
                ],
                'usuarios' => [
                    'total_usuarios' => self::contarUsuarios(),
                    'usuarios_activos' => self::contarUsuariosActivos(),
                    'administradores' => self::contarAdministradores()
                ],
                'estadisticas' => [
                    'total_clientes' => self::contarClientes(),
                    'total_inventario' => self::contarInventario(),
                    'ventas_mes' => self::contarVentasMes(),
                    'reparaciones_pendientes' => self::contarReparacionesPendientes()
                ]
            ];

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Configuración obtenida exitosamente',
                'data' => $configuracion
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener la configuración',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTODO PARA RESPALDO DE BASE DE DATOS (simulado)
    public static function crearRespaldoAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();

        try {
            // Simulación de respaldo
            $fecha = date('Y-m-d_H-i-s');
            $nombreArchivo = "respaldo_celulares_{$fecha}.sql";

            // Aquí normalmente ejecutarías un comando para crear el respaldo
            // Por ahora solo simularemos el proceso

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Respaldo creado exitosamente',
                'archivo' => $nombreArchivo,
                'fecha' => date('Y-m-d H:i:s'),
                'tamaño' => '2.5 MB (simulado)'
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al crear el respaldo',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTODOS AUXILIARES PARA ESTADÍSTICAS
    private static function contarUsuarios()
    {
        try {
            $consulta = "SELECT COUNT(*) as total FROM usuario_login2025 WHERE usu_situacion != 0";
            $resultado = self::fetchFirst($consulta);
            return $resultado['total'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    private static function contarUsuariosActivos()
    {
        try {
            $consulta = "SELECT COUNT(*) as total FROM usuario_login2025 WHERE usu_situacion = 1";
            $resultado = self::fetchFirst($consulta);
            return $resultado['total'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    private static function contarAdministradores()
    {
        try {
            $consulta = "SELECT COUNT(*) as total FROM usuario_login2025 WHERE usu_catalogo = 'ADMIN' AND usu_situacion = 1";
            $resultado = self::fetchFirst($consulta);
            return $resultado['total'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    private static function contarClientes()
    {
        try {
            $consulta = "SELECT COUNT(*) as total FROM clientes WHERE situacion != 0";
            $resultado = self::fetchFirst($consulta);
            return $resultado['total'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    private static function contarInventario()
    {
        try {
            $consulta = "SELECT COUNT(*) as total FROM inventario WHERE situacion = 1";
            $resultado = self::fetchFirst($consulta);
            return $resultado['total'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    private static function contarVentasMes()
    {
        try {
            $consulta = "SELECT COUNT(*) as total FROM ventas 
                        WHERE situacion = 1 
                        AND MONTH(fecha_venta) = MONTH(TODAY)
                        AND YEAR(fecha_venta) = YEAR(TODAY)";
            $resultado = self::fetchFirst($consulta);
            return $resultado['total'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    private static function contarReparacionesPendientes()
    {
        try {
            $consulta = "SELECT COUNT(*) as total FROM reparaciones 
                        WHERE situacion = 1 
                        AND estado NOT IN ('ENTREGADO', 'CANCELADO')";
            $resultado = self::fetchFirst($consulta);
            return $resultado['total'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }
}