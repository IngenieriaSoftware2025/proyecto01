<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Inventario;
use Model\Marcas;

class InventarioController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        $router->render('inventario/index', []);
    }

    public static function guardarAPI()
    {
        getHeadersApi();

        if (empty($_POST['marca_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La marca es obligatoria'
            ]);
            return;
        }

        if (empty($_POST['numero_serie'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El número de serie es obligatorio'
            ]);
            return;
        }

        if (empty($_POST['precio_compra']) || $_POST['precio_compra'] <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio de compra debe ser mayor a 0'
            ]);
            return;
        }

        if (empty($_POST['precio_venta']) || $_POST['precio_venta'] <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio de venta debe ser mayor a 0'
            ]);
            return;
        }

        try {
            $consulta_serie = "SELECT COUNT(*) as total FROM inventario WHERE numero_serie = '" . 
            htmlspecialchars($_POST['numero_serie']) . "' AND situacion = 1";
            $existe_serie = self::fetchFirst($consulta_serie);
            
            if ($existe_serie && $existe_serie['total'] > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe un dispositivo con ese número de serie'
                ]);
                return;
            }

            $marca_id = intval($_POST['marca_id']);
            $numero_serie = htmlspecialchars($_POST['numero_serie']);
            $precio_compra = floatval($_POST['precio_compra']);
            $precio_venta = floatval($_POST['precio_venta']);
            $stock_disponible = intval($_POST['stock_disponible'] ?? 1);
            $estado_dispositivo = htmlspecialchars($_POST['estado_dispositivo'] ?? 'NUEVO');
            $observaciones = htmlspecialchars($_POST['observaciones'] ?? '');

            if (!in_array($estado_dispositivo, ['NUEVO', 'USADO', 'REPARADO'])) {
                $estado_dispositivo = 'NUEVO';
            }

            $query = "INSERT INTO inventario (marca_id, numero_serie, precio_compra, precio_venta, stock_disponible, estado_dispositivo, estado_inventario, observaciones, situacion) 
                      VALUES ($marca_id, '$numero_serie', $precio_compra, $precio_venta, $stock_disponible, '$estado_dispositivo', 'DISPONIBLE', '$observaciones', 1)";
            
            $resultado = self::SQL($query);
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Dispositivo agregado al inventario exitosamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al guardar el dispositivo'
                ]);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error interno del servidor',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function buscarAPI()
    {
        getHeadersApi();
        try {
            $consulta = "SELECT i.id, i.numero_serie, i.precio_compra, i.precio_venta, 
                               i.stock_disponible, i.estado_dispositivo, i.estado_inventario, 
                               i.fecha_ingreso, i.observaciones,
                               m.nombre as marca_nombre, m.modelo as marca_modelo
                        FROM inventario i 
                        INNER JOIN marcas m ON i.marca_id = m.id 
                        WHERE i.situacion = 1 
                        ORDER BY i.fecha_ingreso DESC";
            
            $inventario = self::fetchArray($consulta);

            if (count($inventario) > 0) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Inventario obtenido exitosamente',
                    'data' => $inventario
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No hay dispositivos en inventario',
                    'detalle' => 'inventario vacío'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error de conexión',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function buscarMarcasAPI()
    {
        getHeadersApi();
        try {
            $consulta = "SELECT id, nombre, modelo FROM marcas WHERE situacion = 1 ORDER BY nombre, modelo";
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

    public static function modificarAPI()
    {
        getHeadersApi();

        $id = $_POST['id'];

        if (empty($_POST['marca_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La marca es obligatoria'
            ]);
            return;
        }

        if (empty($_POST['numero_serie'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El número de serie es obligatorio'
            ]);
            return;
        }

        if (empty($_POST['precio_compra']) || $_POST['precio_compra'] <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio de compra debe ser mayor a 0'
            ]);
            return;
        }

        if (empty($_POST['precio_venta']) || $_POST['precio_venta'] <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio de venta debe ser mayor a 0'
            ]);
            return;
        }

        try {
            $consulta_serie = "SELECT COUNT(*) as total FROM inventario WHERE numero_serie = '" . 
                            htmlspecialchars($_POST['numero_serie']) . "' AND id != " . $id . " AND situacion = 1";
            $existe_serie = self::fetchFirst($consulta_serie);
            
            if ($existe_serie && $existe_serie['total'] > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe otro dispositivo con ese número de serie'
                ]);
                return;
            }

            $dispositivo = Inventario::find($id);
            
            if (!$dispositivo) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Dispositivo no encontrado'
                ]);
                return;
            }

            $dispositivo->sincronizar([
                'marca_id' => $_POST['marca_id'],
                'numero_serie' => $_POST['numero_serie'],
                'precio_compra' => $_POST['precio_compra'],
                'precio_venta' => $_POST['precio_venta'],
                'stock_disponible' => $_POST['stock_disponible'],
                'estado_dispositivo' => $_POST['estado_dispositivo'],
                'observaciones' => $_POST['observaciones']
            ]);

            $resultado = $dispositivo->actualizar();
            
            if ($resultado['resultado']) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Dispositivo modificado exitosamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al modificar el dispositivo'
                ]);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error interno del servidor',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function eliminarAPI()
    {
        getHeadersApi();
        
        if (empty($_GET['id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de dispositivo es requerido'
            ]);
            return;
        }

        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
            
            $update = "UPDATE inventario SET situacion = 0 WHERE id = " . self::$db->quote($id);
            $resultado = self::SQL($update);
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Dispositivo eliminado del inventario correctamente'
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar el dispositivo',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}

?>