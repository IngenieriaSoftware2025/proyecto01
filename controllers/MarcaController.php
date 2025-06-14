<?php

namespace Controllers;

use Model\ActiveRecord;
use Model\Marcas;
use MVC\Router;
use Exception;

class MarcaController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        isAuth();
        $router->render('marcas/index', []);
    }

    // MÉTODO PARA VERIFICAR DUPLICADOS
    private static function verificarDuplicados($nombre, $modelo, $id = null)
    {
        $condicion = $id ? " AND id != " . intval($id) : "";
        
        $query = "SELECT COUNT(*) as total FROM marcas 
                  WHERE LOWER(nombre) = LOWER('" . trim($nombre) . "') 
                  AND LOWER(modelo) = LOWER('" . trim($modelo) . "') 
                  AND situacion = 1" . $condicion;
        
        $resultado = self::fetchFirst($query);
        return $resultado['total'] > 0;
    }

    public static function guardarAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();

        if (empty($_POST['nombre'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre de la marca es obligatorio'
            ]);
            return;
        }

        if (empty($_POST['modelo'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El modelo es obligatorio'
            ]);
            return;
        }

        try {
             $nombre = trim($_POST['nombre']);
            $modelo = trim($_POST['modelo']);

            // VERIFICAR DUPLICADOS
            if (self::verificarDuplicados($nombre, $modelo)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe una marca con el mismo nombre y modelo'
                ]);
                return;
            }
            
            $_POST['nombre'] = ucwords(strtolower(trim(htmlspecialchars($_POST['nombre']))));
            $_POST['descripcion'] = htmlspecialchars($_POST['descripcion'] ?? '');
            $_POST['modelo'] = htmlspecialchars($_POST['modelo']);

            $marca = new Marcas([
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'],
                'modelo' => $_POST['modelo'],
                'situacion' => 1
            ]);

            $crear = $marca->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Marca guardada exitosamente'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar la marca',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function buscarAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']);
        getHeadersApi();
        try {
            $consulta = "SELECT * FROM marcas WHERE situacion = 1 ORDER BY nombre";
            $marcas = self::fetchArray($consulta);

            if (count($marcas) > 0) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Éxito al obtener las marcas',
                    'data' => $marcas
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No hay marcas registradas'
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

    public static function modificarAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();

        $id = $_POST['id'];

        if (empty($_POST['nombre'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre de la marca es obligatorio'
            ]);
            return;
        }

        if (empty($_POST['modelo'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El modelo es obligatorio'
            ]);
            return;
        }

        try {
            $nombre = trim($_POST['nombre']);
            $modelo = trim($_POST['modelo']);

            // VERIFICAR DUPLICADOS EXCLUYENDO EL REGISTRO ACTUAL
            if (self::verificarDuplicados($nombre, $modelo, $id)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe otra marca con el mismo nombre y modelo'
                ]);
                return;
            }
            
            $marca = Marcas::find($id);
            $marca->sincronizar([
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'],
                'modelo' => $_POST['modelo']
            ]);

            $marca->actualizar();
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Marca modificada exitosamente'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar la marca',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function eliminarAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
            $update = "UPDATE marcas SET situacion = 0 WHERE id = " . self::$db->quote($id);
            self::SQL($update);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Marca eliminada correctamente'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar la marca',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
