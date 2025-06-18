<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;

class HistorialController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        isAuth(); // Verificar autenticación
        hasPermission(['ADMIN']); // Solo administradores
        $router->render('historial/index', []);
    }

    // MÉTODO PARA REGISTRAR ACTIVIDAD
    public static function registrarActividad($usuarioId, $modulo, $accion, $tablaAfectada = null, $registroId = null, $descripcion = '', $datosAnteriores = null, $datosNuevos = null)
    {
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'N/A';

            // Convertir arrays a JSON si es necesario
            $datosAnteriorJson = $datosAnteriores ? json_encode($datosAnteriores) : null;
            $datosNuevosJson = $datosNuevos ? json_encode($datosNuevos) : null;

            $query = "INSERT INTO historial_actividades (
            historial_usuario_id, historial_modulo, historial_accion, 
            historial_tabla_afectada, historial_registro_id, historial_descripcion,
            historial_datos_anteriores, historial_datos_nuevos, historial_ip,
            historial_fecha
            ) VALUES (
            $usuarioId, '$modulo', '$accion', 
            " . ($tablaAfectada ? "'$tablaAfectada'" : "NULL") . ", 
            " . ($registroId ? $registroId : "NULL") . ", 
            '$descripcion',
            " . ($datosAnteriorJson ? "'$datosAnteriorJson'" : "NULL") . ",
            " . ($datosNuevosJson ? "'$datosNuevosJson'" : "NULL") . ",
            '$ip',
            CURRENT YEAR TO MINUTE
            )";

            return self::SQL($query);
        } catch (Exception $e) {
            error_log("Error registrando actividad: " . $e->getMessage());
            return false;
        }
    }

    // MÉTODO PARA BUSCAR HISTORIAL
    public static function buscarHistorialAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();

        try {
            $filtroFecha = $_GET['fecha_inicio'] ?? null;
            $filtroUsuario = $_GET['usuario_id'] ?? null;
            $filtroModulo = $_GET['modulo'] ?? null;
            $limite = $_GET['limite'] ?? 100;

            $whereConditions = ["h.historial_situacion = 1"];

            if ($filtroFecha) {
                $fechaInformix = date('Y-m-d H:i:s', strtotime($filtroFecha));
                $whereConditions[] = "h.historial_fecha >= '$fechaInformix'";
            }

            if ($filtroUsuario) {
                $whereConditions[] = "h.historial_usuario_id = " . intval($filtroUsuario);
            }

            if ($filtroModulo) {
                $whereConditions[] = "h.historial_modulo = '$filtroModulo'";
            }

            $whereClause = implode(' AND ', $whereConditions);

            $consulta = "SELECT FIRST " . intval($limite) . "
            h.historial_id,
            h.historial_modulo,
            h.historial_accion,
            h.historial_tabla_afectada,
            h.historial_registro_id,
            h.historial_descripcion,
            h.historial_datos_anteriores,
            h.historial_datos_nuevos,
            h.historial_ip,
            h.historial_fecha,
            u.usu_nombre as usuario_nombre,
            u.usu_codigo as usuario_codigo
            FROM historial_actividades h
            INNER JOIN usuario_login2025 u ON h.historial_usuario_id = u.usu_id
            WHERE $whereClause
            ORDER BY h.historial_fecha DESC";

            $actividades = self::fetchArray($consulta);

            // Procesar datos para mejor visualización
            $actividadesProcesadas = [];
            foreach ($actividades as $actividad) {
                $fechaFormateada = date('d/m/Y H:i', strtotime($actividad['historial_fecha']));

                $actividadesProcesadas[] = [
                    'historial_id' => $actividad['historial_id'],
                    'fecha' => $fechaFormateada,
                    'usuario' => $actividad['usuario_nombre'] . ' (' . $actividad['usuario_codigo'] . ')',
                    'modulo' => $actividad['historial_modulo'],
                    'accion' => $actividad['historial_accion'],
                    'descripcion' => $actividad['historial_descripcion'],
                    'tabla_afectada' => $actividad['historial_tabla_afectada'],
                    'registro_id' => $actividad['historial_registro_id'],
                    'ip' => $actividad['historial_ip'],
                    'datos_anteriores' => $actividad['historial_datos_anteriores'],
                    'datos_nuevos' => $actividad['historial_datos_nuevos']
                ];
            }

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Historial obtenido exitosamente',
                'data' => $actividadesProcesadas,
                'total' => count($actividadesProcesadas)
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener historial',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTODO PARA OBTENER ESTADÍSTICAS DE ACTIVIDAD
    public static function estadisticasActividadAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();

        try {
            // Actividades por día (últimos 7 días)
            $queryPorDia = "SELECT 
                   DAY(historial_fecha) || '/' || MONTH(historial_fecha) as dia,
                   COUNT(*) as cantidad
               FROM historial_actividades 
               WHERE historial_fecha >= CURRENT - 7 UNITS DAY
               AND historial_situacion = 1
               GROUP BY DAY(historial_fecha), MONTH(historial_fecha)
               ORDER BY historial_fecha DESC";

            $actividadesPorDia = self::fetchArray($queryPorDia);

            // Actividades por módulo
            $queryPorModulo = "SELECT 
                                 historial_modulo as modulo,
                                 COUNT(*) as cantidad
                             FROM historial_actividades 
                             WHERE historial_fecha >= (TODAY - 30)
                             AND historial_situacion = 1
                             GROUP BY historial_modulo
                             ORDER BY cantidad DESC";

            $actividadesPorModulo = self::fetchArray($queryPorModulo);

            // Usuarios más activos
            $queryUsuariosActivos = "SELECT FIRST 10
                           u.usu_nombre as usuario,
                           COUNT(*) as actividades
                       FROM historial_actividades h
                       INNER JOIN usuario_login2025 u ON h.historial_usuario_id = u.usu_id
                       WHERE h.historial_fecha >= CURRENT - 30 UNITS DAY
                       AND h.historial_situacion = 1
                       GROUP BY u.usu_nombre, u.usu_id
                       ORDER BY actividades DESC";

            $usuariosActivos = self::fetchArray($queryUsuariosActivos);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Estadísticas obtenidas exitosamente',
                'data' => [
                    'actividades_por_dia' => $actividadesPorDia,
                    'actividades_por_modulo' => $actividadesPorModulo,
                    'usuarios_activos' => $usuariosActivos
                ]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener estadísticas',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTODO PARA OBTENER USUARIOS PARA FILTRO
    public static function obtenerUsuariosAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();

        try {
            $consulta = "SELECT usu_id, usu_nombre, usu_codigo 
                        FROM usuario_login2025 
                        WHERE usu_situacion = 1 
                        ORDER BY usu_nombre";

            $usuarios = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Usuarios obtenidos exitosamente',
                'data' => $usuarios
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener usuarios',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTODO PARA OBTENER MÓDULOS PARA FILTRO
    public static function obtenerModulosAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();

        try {
            $consulta = "SELECT DISTINCT historial_modulo as modulo
                        FROM historial_actividades 
                        WHERE historial_situacion = 1 
                        ORDER BY historial_modulo";

            $modulos = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Módulos obtenidos exitosamente',
                'data' => $modulos
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener módulos',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
