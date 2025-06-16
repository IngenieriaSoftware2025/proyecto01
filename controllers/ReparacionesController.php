<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;

class ReparacionesController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        isAuth(); // Verificar autenticación
        $router->render('reparaciones/index', []);
    }

    // MÉTODO PARA BUSCAR CLIENTES ACTIVOS
    public static function buscarClientesAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);

        try {
            $consulta = "SELECT id, nombre, apellido, telefono FROM clientes 
                        WHERE situacion IN (1,2,3) ORDER BY nombre, apellido";
            $clientes = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Clientes obtenidos exitosamente',
                'data' => $clientes
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener clientes',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTODO PARA BUSCAR TIPOS DE SERVICIO
    public static function buscarTiposServicioAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);

        try {
            $consulta = "SELECT tipo_id, tipo_nombre, tipo_descripcion, precio_base, tiempo_estimado 
                        FROM tipos_servicio WHERE situacion = 1 ORDER BY tipo_nombre";
            $tipos = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Tipos de servicio obtenidos exitosamente',
                'data' => $tipos
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener tipos de servicio',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTODO PARA BUSCAR MARCAS ÚNICAS
    public static function buscarMarcasAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);

        try {
            $consulta = "SELECT DISTINCT nombre FROM marcas WHERE situacion = 1 ORDER BY nombre";
            $marcas = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Marcas obtenidas exitosamente',
                'data' => $marcas
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener marcas',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTODO PARA BUSCAR MODELOS POR MARCA
    public static function buscarModelosPorMarcaAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);

        if (empty($_GET['marca'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Marca es requerida'
            ]);
            return;
        }

        try {
            $marca = htmlspecialchars($_GET['marca']);
            $consulta = "SELECT modelo FROM marcas WHERE nombre = '$marca' AND situacion = 1 ORDER BY modelo";
            $modelos = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Modelos obtenidos exitosamente',
                'data' => $modelos
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener modelos',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTODO PARA BUSCAR TÉCNICOS DISPONIBLES (NO ADMINISTRADORES)
    public static function buscarTecnicosAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);

        try {
            $consulta = "SELECT usu_id, usu_nombre FROM usuario_login2025 
            WHERE usu_situacion = 1 
            ORDER BY usu_nombre";
            $tecnicos = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Técnicos obtenidos exitosamente',
                'data' => $tecnicos
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener técnicos',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTODO PARA CREAR NUEVA REPARACIÓN
    public static function guardarAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']); // Ambos pueden recibir reparaciones

        // Validaciones básicas
        if (empty($_POST['cliente_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar un cliente'
            ]);
            return;
        }

        if (empty($_POST['dispositivo_marca']) || empty($_POST['dispositivo_modelo'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La marca y modelo del dispositivo son obligatorios'
            ]);
            return;
        }

        if (empty($_POST['problema_reportado'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe describir el problema reportado'
            ]);
            return;
        }

        try {
            // Verificar que el cliente existe
            $clienteId = intval($_POST['cliente_id']);
            $queryCliente = "SELECT COUNT(*) as total FROM clientes WHERE id = $clienteId AND situacion IN (1,2,3)";
            $clienteExiste = self::fetchFirst($queryCliente);

            if (!$clienteExiste || $clienteExiste['total'] == 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El cliente seleccionado no existe o está inactivo'
                ]);
                return;
            }

            // Sanitizar datos
            $dispositivoMarca = htmlspecialchars($_POST['dispositivo_marca']);
            $dispositivoModelo = htmlspecialchars($_POST['dispositivo_modelo']);
            $dispositivoSerie = htmlspecialchars($_POST['dispositivo_serie'] ?? '');
            $dispositivoImei = htmlspecialchars($_POST['dispositivo_imei'] ?? '');
            $problemaReportado = htmlspecialchars($_POST['problema_reportado']);
            $tipoServicioId = !empty($_POST['tipo_servicio_id']) ? intval($_POST['tipo_servicio_id']) : 'NULL';
            $presupuestoInicial = !empty($_POST['presupuesto_inicial']) ? floatval($_POST['presupuesto_inicial']) : 0.00;
            $anticipo = !empty($_POST['anticipo']) ? floatval($_POST['anticipo']) : 0.00;
            $observaciones = htmlspecialchars($_POST['observaciones'] ?? '');

            // Crear la reparación
            $fechaHoy = date('m/d/Y');
            $queryReparacion = "INSERT INTO reparaciones (
                cliente_id, dispositivo_marca, dispositivo_modelo, dispositivo_serie, dispositivo_imei,
                problema_reportado, tipo_servicio_id, presupuesto_inicial, anticipo, 
                observaciones, fecha_ingreso, estado, situacion
            ) VALUES (
                $clienteId, '$dispositivoMarca', '$dispositivoModelo', '$dispositivoSerie', '$dispositivoImei',
                '$problemaReportado', $tipoServicioId, $presupuestoInicial, $anticipo,
                '$observaciones', 'TODAY', 'RECIBIDO', 1
            )";

            $resultado = self::SQL($queryReparacion);

            if ($resultado) {
                $reparacionId = self::$db->lastInsertId('reparaciones');

                // Registrar en historial
                $usuarioId = $_SESSION['user_id'];
                $queryHistorial = "INSERT INTO reparacion_historial (reparacion_id, estado_anterior, estado_nuevo, usuario_cambio, observaciones)
                                  VALUES ($reparacionId, NULL, 'RECIBIDO', $usuarioId, 'Reparación recibida')";
                self::SQL($queryHistorial);

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Reparación registrada exitosamente',
                    'datos' => [
                        'reparacion_id' => $reparacionId
                    ]
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al registrar la reparación'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al procesar la reparación',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTODO PARA BUSCAR REPARACIONES
    public static function buscarAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);

        try {
            $consulta = "SELECT r.reparacion_id, r.dispositivo_marca, r.dispositivo_modelo, r.dispositivo_serie,
                               r.problema_reportado, r.estado, r.presupuesto_inicial, r.costo_final,
                               TO_CHAR(r.fecha_ingreso, '%d/%m/%Y') as fecha_ingreso,
                               TO_CHAR(r.fecha_entrega, '%d/%m/%Y') as fecha_entrega,
                               c.nombre as cliente_nombre, c.apellido as cliente_apellido, c.telefono,
                               ts.tipo_nombre, ts.precio_base,
                               t.usu_nombre as tecnico_nombre
                        FROM reparaciones r
                        INNER JOIN clientes c ON r.cliente_id = c.id
                        LEFT JOIN tipos_servicio ts ON r.tipo_servicio_id = ts.tipo_id
                        LEFT JOIN usuario_login2025 t ON r.tecnico_asignado = t.usu_id
                        WHERE r.situacion = 1
                        ORDER BY r.fecha_ingreso DESC, r.reparacion_id DESC";

            $reparaciones = self::fetchArray($consulta);

            if (count($reparaciones) > 0) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Reparaciones obtenidas exitosamente',
                    'data' => $reparaciones
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No hay reparaciones registradas'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener reparaciones',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTODO PARA ACTUALIZAR ESTADO DE REPARACIÓN
    public static function actualizarEstadoAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);

        if (empty($_POST['reparacion_id']) || empty($_POST['nuevo_estado'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de reparación y nuevo estado son requeridos'
            ]);
            return;
        }

        try {
            $reparacionId = intval($_POST['reparacion_id']);
            $nuevoEstado = htmlspecialchars($_POST['nuevo_estado']);
            $observaciones = htmlspecialchars($_POST['observaciones'] ?? '');
            $usuarioId = $_SESSION['user_id'];

            // Estados válidos
            $estadosValidos = ['RECIBIDO', 'EN_DIAGNOSTICO', 'DIAGNOSTICADO', 'EN_REPARACION', 'REPARADO', 'ENTREGADO', 'CANCELADO'];

            if (!in_array($nuevoEstado, $estadosValidos)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Estado no válido'
                ]);
                return;
            }

            // Obtener estado actual
            $queryEstadoActual = "SELECT estado FROM reparaciones WHERE reparacion_id = $reparacionId AND situacion = 1";
            $estadoActual = self::fetchFirst($queryEstadoActual);

            if (!$estadoActual) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Reparación no encontrada'
                ]);
                return;
            }

            $estadoAnterior = $estadoActual['estado'];

            // Actualizar estado y fechas según corresponda
            $fechaActual = date('Y-m-d');
            $updateFields = "estado = '$nuevoEstado'";

            switch ($nuevoEstado) {
                case 'EN_DIAGNOSTICO':
                case 'DIAGNOSTICADO':
                    $updateFields .= ", fecha_diagnostico = '$fechaActual'";
                    break;
                case 'REPARADO':
                    $updateFields .= ", fecha_finalizacion = '$fechaActual'";
                    break;
                case 'ENTREGADO':
                    $updateFields .= ", fecha_entrega = '$fechaActual'";
                    break;
            }

            // Actualizar reparación
            $queryUpdate = "UPDATE reparaciones SET $updateFields WHERE reparacion_id = $reparacionId";
            $resultado = self::SQL($queryUpdate);

            if ($resultado) {
                // Registrar en historial
                $queryHistorial = "INSERT INTO reparacion_historial (reparacion_id, estado_anterior, estado_nuevo, usuario_cambio, observaciones)
                                  VALUES ($reparacionId, '$estadoAnterior', '$nuevoEstado', $usuarioId, '$observaciones')";
                self::SQL($queryHistorial);

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Estado actualizado exitosamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al actualizar el estado'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al actualizar estado',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTODO PARA ASIGNAR TÉCNICO
    public static function asignarTecnicoAPI()
    {
        hasPermissionApi(['ADMIN']); // Solo ADMIN puede asignar técnicos

        if (empty($_POST['reparacion_id']) || empty($_POST['tecnico_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de reparación y técnico son requeridos'
            ]);
            return;
        }

        try {
            $reparacionId = intval($_POST['reparacion_id']);
            $tecnicoId = intval($_POST['tecnico_id']);
            $usuarioId = $_SESSION['user_id'];

            // Verificar que el técnico existe
            $queryTecnico = "SELECT usu_nombre FROM usuario_login2025 WHERE usu_id = $tecnicoId AND usu_situacion = 1";
            $tecnico = self::fetchFirst($queryTecnico);

            if (!$tecnico) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El técnico seleccionado no existe'
                ]);
                return;
            }

            // Actualizar técnico asignado
            $queryUpdate = "UPDATE reparaciones SET tecnico_asignado = $tecnicoId WHERE reparacion_id = $reparacionId";
            $resultado = self::SQL($queryUpdate);

            if ($resultado) {
                // Registrar en historial
                $nombreTecnico = $tecnico['usu_nombre'];
                $queryHistorial = "INSERT INTO reparacion_historial (reparacion_id, estado_anterior, estado_nuevo, usuario_cambio, observaciones)
                                  VALUES ($reparacionId, 'ASIGNACION', 'ASIGNACION', $usuarioId, 'Técnico asignado: $nombreTecnico')";
                self::SQL($queryHistorial);

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Técnico asignado exitosamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al asignar técnico'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al asignar técnico',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTODO PARA VER DETALLE Y HISTORIAL DE REPARACIÓN
    public static function verDetalleAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);

        if (empty($_GET['reparacion_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de reparación es requerido'
            ]);
            return;
        }

        try {
            $reparacionId = intval($_GET['reparacion_id']);

            // Obtener información general de la reparación
            $queryReparacion = "SELECT r.*, 
                                      c.nombre as cliente_nombre, c.apellido as cliente_apellido, 
                                      c.telefono, c.nit,
                                      ts.tipo_nombre, ts.tipo_descripcion, ts.precio_base,
                                      t.usu_nombre as tecnico_nombre,
                                      TO_CHAR(r.fecha_ingreso, '%d/%m/%Y') as fecha_ingreso_formato,
                                      TO_CHAR(r.fecha_diagnostico, '%d/%m/%Y') as fecha_diagnostico_formato,
                                      TO_CHAR(r.fecha_finalizacion, '%d/%m/%Y') as fecha_finalizacion_formato,
                                      TO_CHAR(r.fecha_entrega, '%d/%m/%Y') as fecha_entrega_formato
                              FROM reparaciones r
                              INNER JOIN clientes c ON r.cliente_id = c.id
                              LEFT JOIN tipos_servicio ts ON r.tipo_servicio_id = ts.tipo_id
                              LEFT JOIN usuario_login2025 t ON r.tecnico_asignado = t.usu_id
                              WHERE r.reparacion_id = $reparacionId AND r.situacion = 1";

            $reparacion = self::fetchFirst($queryReparacion);

            if (!$reparacion) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Reparación no encontrada'
                ]);
                return;
            }

            // Obtener historial de cambios
            $queryHistorial = "SELECT rh.*, u.usu_nombre as usuario_nombre,
                                     TO_CHAR(rh.fecha_cambio, '%d/%m/%Y %H:%M') as fecha_cambio_formato
                              FROM reparacion_historial rh
                              INNER JOIN usuario_login2025 u ON rh.usuario_cambio = u.usu_id
                              WHERE rh.reparacion_id = $reparacionId
                              ORDER BY rh.fecha_cambio DESC";

            $historial = self::fetchArray($queryHistorial);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Detalle de reparación obtenido exitosamente',
                'data' => [
                    'reparacion' => $reparacion,
                    'historial' => $historial
                ]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener detalle de reparación',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTODO PARA ELIMINAR/CANCELAR REPARACIÓN
    public static function eliminarAPI()
    {
        hasPermissionApi(['ADMIN']); // Solo ADMIN puede cancelar

        if (empty($_GET['reparacion_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de reparación es requerido'
            ]);
            return;
        }

        try {
            $reparacionId = intval($_GET['reparacion_id']);
            $usuarioId = $_SESSION['user_id'];

            // Actualizar estado a cancelado
            $queryUpdate = "UPDATE reparaciones SET situacion = 0, estado = 'CANCELADO' WHERE reparacion_id = $reparacionId";
            $resultado = self::SQL($queryUpdate);

            if ($resultado) {
                // Registrar en historial
                $queryHistorial = "INSERT INTO reparacion_historial (reparacion_id, estado_anterior, estado_nuevo, usuario_cambio, observaciones)
                                  VALUES ($reparacionId, 'ACTIVO', 'CANCELADO', $usuarioId, 'Reparación cancelada por administrador')";
                self::SQL($queryHistorial);

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Reparación cancelada correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al cancelar la reparación'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al cancelar reparación',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
