<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use DateTime;

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

    // MÉTODO PRIVADO PARA FORMATEAR FECHA INFORMIX
    private static function formatearFechaInformix($fecha)
    {
        try {
            // Eliminar caracteres no deseados
            $fechaLimpia = trim($fecha);
            $fechaLimpia = preg_replace('/[^0-9\-]/', '', $fechaLimpia);
            
            // Validar y formatear
            $fechaObj = DateTime::createFromFormat('Y-m-d', $fechaLimpia);
            if ($fechaObj && $fechaObj->format('Y-m-d') === $fechaLimpia) {
                return $fechaObj->format('Y-m-d 00:00:00');
            }
            return null;
        } catch (Exception $e) {
            error_log("Error formateando fecha: " . $e->getMessage());
            return null;
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

            // Procesar filtro de fecha con validación mejorada
            if ($filtroFecha) {
                $fechaFormateada = self::formatearFechaInformix($filtroFecha);
                if ($fechaFormateada) {
                    $whereConditions[] = "h.historial_fecha >= TO_DATE('$fechaFormateada', '%Y-%m-%d %H:%M:%S')";
                }
            }

            // Procesar filtro de usuario
            if ($filtroUsuario) {
                $usuarioId = intval($filtroUsuario);
                if ($usuarioId > 0) {
                    $whereConditions[] = "h.historial_usuario_id = $usuarioId";
                }
            }

            // Procesar filtro de módulo
            if ($filtroModulo) {
                $moduloLimpio = trim($filtroModulo);
                $moduloLimpio = addslashes($moduloLimpio);
                $whereConditions[] = "h.historial_modulo = '$moduloLimpio'";
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
                // Formatear fecha de manera segura
                $fechaFormateada = 'N/A';
                if ($actividad['historial_fecha']) {
                    try {
                        $fechaObj = new DateTime($actividad['historial_fecha']);
                        $fechaFormateada = $fechaObj->format('d/m/Y H:i');
                    } catch (Exception $e) {
                        $fechaFormateada = $actividad['historial_fecha'];
                    }
                }

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
            $errorMessage = $e->getMessage();
            
            // Manejo específico para errores de fecha
            if (strpos($errorMessage, '22008') !== false || strpos($errorMessage, '-1264') !== false) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error en formato de fecha',
                    'detalle' => 'Verifique que la fecha esté en formato YYYY-MM-DD'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al obtener historial',
                    'detalle' => $errorMessage
                ]);
            }
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