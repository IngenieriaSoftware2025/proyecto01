<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Ventas;
use Model\VentaDetalle;
use Model\Clientes;
use Model\Inventario;

class VentasController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        isAuth(); // Verificar autenticación
        $router->render('ventas/index', []);
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

    // MÉTODO PARA BUSCAR INVENTARIO DISPONIBLE
    public static function buscarInventarioDisponibleAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);

        try {
            $consulta = "SELECT i.id, i.numero_serie, i.precio_venta, i.stock_disponible,
                               i.estado_dispositivo, m.nombre as marca_nombre, m.modelo as marca_modelo
                        FROM inventario i 
                        INNER JOIN marcas m ON i.marca_id = m.id 
                        WHERE i.situacion = 1 AND i.estado_inventario = 'DISPONIBLE' 
                        AND i.stock_disponible > 0
                        ORDER BY m.nombre, m.modelo";

            $inventario = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Inventario disponible obtenido exitosamente',
                'data' => $inventario
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener inventario disponible',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarAPI()
    {
        hasPermissionApi(['ADMIN']); // Solo ADMIN puede crear ventas

        // Validaciones básicas
        if (empty($_POST['cliente_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar un cliente'
            ]);
            return;
        }

        if (empty($_POST['dispositivos']) || !is_array($_POST['dispositivos'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe agregar al menos un dispositivo a la venta'
            ]);
            return;
        }

        if (empty($_POST['total']) || floatval($_POST['total']) <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El total de la venta debe ser mayor a 0'
            ]);
            return;
        }

        try {
            // Sanitizar datos de entrada
            $clienteId = intval($_POST['cliente_id']);
            $usuarioId = $_SESSION['user_id']; // Usuario que realiza la venta
            $total = floatval($_POST['total']);
            $observaciones = htmlspecialchars($_POST['observaciones'] ?? '');
            $dispositivos = $_POST['dispositivos'];

            // Verificar que el cliente existe usando consulta directa
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

            // Verificar stock disponible para todos los dispositivos
            foreach ($dispositivos as $dispositivo) {
                $inventarioId = intval($dispositivo['inventario_id']);
                $cantidadSolicitada = intval($dispositivo['cantidad']);

                $queryStock = "SELECT stock_disponible FROM inventario WHERE id = $inventarioId AND situacion = 1";
                $stockActual = self::fetchFirst($queryStock);

                if (!$stockActual || $stockActual['stock_disponible'] < $cantidadSolicitada) {
                    http_response_code(400);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => "Stock insuficiente para el dispositivo ID: $inventarioId"
                    ]);
                    return;
                }
            }

            // Crear la venta principal
            $fechaHoy = date('Y-m-d');
            $queryVenta = "INSERT INTO ventas (cliente_id, usuario_id, total, fecha_venta, observaciones, situacion) 
                      VALUES ($clienteId, $usuarioId, $total, '$fechaHoy', '$observaciones', 1)";

            $resultadoVenta = self::SQL($queryVenta);

            if ($resultadoVenta) {
                // Obtener ID de la venta recién creada
                $ventaId = self::$db->lastInsertId('ventas');

                // Insertar detalle de venta y actualizar stock
                foreach ($dispositivos as $dispositivo) {
                    $inventarioId = intval($dispositivo['inventario_id']);
                    $cantidad = intval($dispositivo['cantidad']);
                    $precioUnitario = floatval($dispositivo['precio_unitario']);
                    $subtotal = $cantidad * $precioUnitario;

                    // Insertar detalle
                    $queryDetalle = "INSERT INTO venta_detalle (venta_id, inventario_id, cantidad, precio_unitario, subtotal) 
                                VALUES ($ventaId, $inventarioId, $cantidad, $precioUnitario, $subtotal)";

                    self::SQL($queryDetalle);

                    // Actualizar stock
                    $queryUpdateStock = "UPDATE inventario SET stock_disponible = stock_disponible - $cantidad 
                                    WHERE id = $inventarioId";
                    self::SQL($queryUpdateStock);

                    // Si el stock llega a 0, cambiar estado a VENDIDO
                    $queryVerificarStock = "UPDATE inventario SET estado_inventario = 'VENDIDO' 
                                       WHERE id = $inventarioId AND stock_disponible = 0";
                    self::SQL($queryVerificarStock);
                }

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Venta registrada exitosamente',
                    'datos' => [
                        'venta_id' => $ventaId,
                        'total' => $total
                    ]
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al registrar la venta'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al procesar la venta',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTODO PARA BUSCAR HISTORIAL DE VENTAS
    public static function buscarAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);

        try {
            $consulta = "SELECT v.venta_id, v.total, TO_CHAR(v.fecha_venta, '%d/%m/%Y') as fecha_venta,
                               v.estado, v.observaciones,
                               c.nombre as cliente_nombre, c.apellido as cliente_apellido,
                               u.usu_nombre as vendedor_nombre
                        FROM ventas v
                        INNER JOIN clientes c ON v.cliente_id = c.id
                        INNER JOIN usuario_login2025 u ON v.usuario_id = u.usu_id
                        WHERE v.situacion = 1
                        ORDER BY v.fecha_venta DESC, v.venta_id DESC";

            $ventas = self::fetchArray($consulta);

            if (count($ventas) > 0) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Historial de ventas obtenido exitosamente',
                    'data' => $ventas
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No hay ventas registradas'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener historial de ventas',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTODO PARA VER DETALLE DE UNA VENTA
    public static function verDetalleAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);

        if (empty($_GET['venta_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de venta es requerido'
            ]);
            return;
        }

        try {
            $ventaId = intval($_GET['venta_id']);

            // Obtener información general de la venta
            $queryVenta = "SELECT v.venta_id, v.total, TO_CHAR(v.fecha_venta, '%d/%m/%Y') as fecha_venta,
                                 v.estado, v.observaciones,
                                 c.nombre as cliente_nombre, c.apellido as cliente_apellido, c.telefono,
                                 u.usu_nombre as vendedor_nombre
                          FROM ventas v
                          INNER JOIN clientes c ON v.cliente_id = c.id
                          INNER JOIN usuario_login2025 u ON v.usuario_id = u.usu_id
                          WHERE v.venta_id = $ventaId AND v.situacion = 1";

            $venta = self::fetchFirst($queryVenta);

            if (!$venta) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Venta no encontrada'
                ]);
                return;
            }

            // Obtener detalle de dispositivos vendidos
            $queryDetalle = "SELECT vd.cantidad, vd.precio_unitario, vd.subtotal,
                                   i.numero_serie, i.estado_dispositivo,
                                   m.nombre as marca_nombre, m.modelo as marca_modelo
                            FROM venta_detalle vd
                            INNER JOIN inventario i ON vd.inventario_id = i.id
                            INNER JOIN marcas m ON i.marca_id = m.id
                            WHERE vd.venta_id = $ventaId
                            ORDER BY m.nombre, m.modelo";

            $detalle = self::fetchArray($queryDetalle);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Detalle de venta obtenido exitosamente',
                'data' => [
                    'venta' => $venta,
                    'detalle' => $detalle
                ]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener detalle de venta',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function anularAPI()
    {
        hasPermissionApi(['ADMIN']);

        if (empty($_GET['venta_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de venta es requerido'
            ]);
            return;
        }

        try {
            $ventaId = intval($_GET['venta_id']);

            // Verificar que la venta existe y está activa usando consulta directa
            $queryVenta = "SELECT COUNT(*) as total FROM ventas WHERE venta_id = $ventaId AND situacion = 1";
            $ventaExiste = self::fetchFirst($queryVenta);

            if (!$ventaExiste || $ventaExiste['total'] == 0) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Venta no encontrada'
                ]);
                return;
            }

            // Restaurar stock de los dispositivos vendidos
            $queryDetalle = "SELECT inventario_id, cantidad FROM venta_detalle WHERE venta_id = $ventaId";
            $detalles = self::fetchArray($queryDetalle);

            foreach ($detalles as $detalle) {
                $inventarioId = $detalle['inventario_id'];
                $cantidad = $detalle['cantidad'];

                // Restaurar stock
                $queryRestaurar = "UPDATE inventario SET 
                              stock_disponible = stock_disponible + $cantidad,
                              estado_inventario = 'DISPONIBLE'
                              WHERE id = $inventarioId";
                self::SQL($queryRestaurar);
            }

            // Anular la venta
            $queryAnular = "UPDATE ventas SET situacion = 0, estado = 'ANULADA' WHERE venta_id = $ventaId";
            $resultado = self::SQL($queryAnular);

            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Venta anulada correctamente. Stock restaurado.'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al anular la venta'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al anular la venta',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
