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
        isAuth();
        $router->render('inventario/index', []);
    }

    // MÉTODO PARA VALIDAR NÚMERO DE SERIE
    private static function validarNumeroSerie($numeroSerie)
    {
        // Formato: letras seguidas de números, mínimo 6 caracteres
        return preg_match('/^[A-Z0-9]{6,20}$/', strtoupper($numeroSerie));
    }

    // MÉTODO PARA VERIFICAR DUPLICADOS
    private static function verificarDuplicados($numeroSerie, $id = null)
    {
        $condicion = $id ? " AND id != " . intval($id) : "";
        $numeroSerie = strtoupper(trim($numeroSerie));

        $query = "SELECT COUNT(*) as total FROM inventario 
                  WHERE UPPER(numero_serie) = '" . $numeroSerie . "' 
                  AND situacion = 1" . $condicion;

        $resultado = self::fetchFirst($query);
        return $resultado['total'] > 0;
    }

    public static function guardarAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);
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

        // VALIDACIÓN NÚMERO DE SERIE
        if (!self::validarNumeroSerie($_POST['numero_serie'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Número de serie inválido. Use formato alfanumérico de 6-20 caracteres'
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

        // VALIDACIÓN STOCK
        if (!isset($_POST['stock_disponible']) || intval($_POST['stock_disponible']) < 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El stock debe ser mayor o igual a 0'
            ]);
            return;
        }

        try {
            $numeroSerie = strtoupper(trim($_POST['numero_serie']));

            // VERIFICAR DUPLICADOS
            if (self::verificarDuplicados($numeroSerie)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe un dispositivo con este número de serie'
                ]);
                return;
            }

            // VERIFICAR QUE LA MARCA EXISTE
            $marca = Marcas::find($_POST['marca_id']);
            if (!$marca) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La marca seleccionada no existe'
                ]);
                return;
            }


            // SANITIZAR DATOS
            $marcaId = intval($_POST['marca_id']);
            $precioCompra = floatval($_POST['precio_compra']);
            $precioVenta = floatval($_POST['precio_venta']);
            $stockDisponible = intval($_POST['stock_disponible']);
            $estadoDispositivo = htmlspecialchars($_POST['estado_dispositivo'] ?? 'NUEVO');
            $observaciones = htmlspecialchars($_POST['observaciones'] ?? '');

            // VALIDAR ESTADO DISPOSITIVO
            $estadosValidos = ['NUEVO', 'USADO', 'REPARADO'];
            if (!in_array($estadoDispositivo, $estadosValidos)) {
                $estadoDispositivo = 'NUEVO';
            }

            // CREAR INVENTARIO USANDO QUERY DIRECTA PARA MANEJAR FECHA
            $fechaHoy = date('m/d/Y');
            // Usar función TODAY de Informix directamente
            $query = "INSERT INTO inventario (marca_id, numero_serie, precio_compra, precio_venta, stock_disponible, estado_dispositivo, estado_inventario, fecha_ingreso, observaciones, situacion) 
          VALUES ($marcaId, '$numeroSerie', $precioCompra, $precioVenta, $stockDisponible, '$estadoDispositivo', 'DISPONIBLE', TODAY, '$observaciones', 1)";

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
                    'mensaje' => 'Error al guardar el dispositivo en la base de datos'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar el dispositivo',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function buscarAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);
        getHeadersApi();
        try {
            $consulta = "SELECT i.id, i.marca_id, i.numero_serie, i.precio_compra, i.precio_venta, 
                               i.stock_disponible, i.estado_dispositivo, i.estado_inventario, 
                               TO_CHAR(i.fecha_ingreso, '%d/%m/%Y') as fecha_ingreso, 
                               i.observaciones,
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
                    'mensaje' => 'No hay dispositivos en inventario'
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
        hasPermissionApi(['ADMIN', 'USER']);
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
        hasPermissionApi(['ADMIN']);
        getHeadersApi();

        if (empty($_POST['id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de dispositivo es requerido'
            ]);
            return;
        }

        $id = $_POST['id'];

        // VALIDACIONES BÁSICAS
        if (empty($_POST['marca_id'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Debe seleccionar una marca']);
            return;
        }

        if (empty($_POST['numero_serie']) || !self::validarNumeroSerie($_POST['numero_serie'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Número de serie inválido']);
            return;
        }

        if (empty($_POST['precio_compra']) || floatval($_POST['precio_compra']) <= 0) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El precio de compra debe ser mayor a 0']);
            return;
        }

        if (empty($_POST['precio_venta']) || floatval($_POST['precio_venta']) <= 0) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El precio de venta debe ser mayor a 0']);
            return;
        }

        if (!isset($_POST['stock_disponible']) || intval($_POST['stock_disponible']) < 0) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El stock debe ser mayor o igual a 0']);
            return;
        }

        try {
            $numeroSerie = strtoupper(trim($_POST['numero_serie']));

            // VERIFICAR DUPLICADOS EXCLUYENDO EL REGISTRO ACTUAL
            if (self::verificarDuplicados($numeroSerie, $id)) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Ya existe otro dispositivo con este número de serie']);
                return;
            }

            // VERIFICAR QUE EL DISPOSITIVO EXISTE
            $inventario = Inventario::find($id);
            if (!$inventario) {
                http_response_code(404);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Dispositivo no encontrado']);
                return;
            }

            // SANITIZAR DATOS
            $marcaId = intval($_POST['marca_id']);
            $precioCompra = floatval($_POST['precio_compra']);
            $precioVenta = floatval($_POST['precio_venta']);
            $stockDisponible = intval($_POST['stock_disponible']);
            $estadoDispositivo = htmlspecialchars($_POST['estado_dispositivo'] ?? 'NUEVO');
            $observaciones = htmlspecialchars($_POST['observaciones'] ?? '');

            // VALIDAR ESTADO DISPOSITIVO
            $estadosValidos = ['NUEVO', 'USADO', 'REPARADO'];
            if (!in_array($estadoDispositivo, $estadosValidos)) {
                $estadoDispositivo = 'NUEVO';
            }

            // ACTUALIZAR USANDO QUERY DIRECTA
            $query = "UPDATE inventario SET 
                        marca_id = $marcaId,
                        numero_serie = '$numeroSerie',
                        precio_compra = $precioCompra,
                        precio_venta = $precioVenta,
                        stock_disponible = $stockDisponible,
                        estado_dispositivo = '$estadoDispositivo',
                        observaciones = '$observaciones'
                      WHERE id = " . intval($id);

            $resultado = self::SQL($query);

            if ($resultado) {
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
                'mensaje' => 'Error al modificar el dispositivo',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function eliminarAPI()
    {
        hasPermissionApi(['ADMIN']);
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

            $update = "UPDATE inventario SET situacion = 0 WHERE id = " . intval($id);
            $resultado = self::SQL($update);

            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Dispositivo eliminado del inventario correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al eliminar el dispositivo'
                ]);
            }
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
