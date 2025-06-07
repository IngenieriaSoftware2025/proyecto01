<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Aplicaciones;

class AplicacionController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        $router->render('aplicaciones/index', []);
    }

    public static function buscarAPI()
    {
        getHeadersApi();
        
        try {
            $query = "SELECT 
                        app_id,
                        app_nombre_largo,
                        app_nombre_medium,
                        app_nombre_corto,
                        app_fecha_creacion,
                        app_situacion
                      FROM aplicacion 
                      WHERE app_situacion = 1
                      ORDER BY app_id DESC";
                      
            $aplicaciones = Aplicaciones::fetchArray($query);
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Aplicaciones encontradas exitosamente',
                'data' => $aplicaciones
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar las aplicaciones',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarAPI()
    {
        getHeadersApi();
        
        // Validaciones básicas
        if (empty($_POST['app_nombre_largo'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre largo es obligatorio'
            ]);
            return;
        }
        
        if (empty($_POST['app_nombre_medium'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre medium es obligatorio'
            ]);
            return;
        }
        
        if (empty($_POST['app_nombre_corto'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre corto es obligatorio'
            ]);
            return;
        }

        try {
            // Verificar si ya existe aplicación con ese nombre corto
            $query = "SELECT COUNT(*) as total FROM aplicacion WHERE app_nombre_corto = '" . $_POST['app_nombre_corto'] . "'";
            $existe = Aplicaciones::fetchFirst($query);
            
            if ($existe && $existe['total'] > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe una aplicación con ese nombre corto'
                ]);
                return;
            }

            // Sanitizar datos
            $nombre_largo = trim(htmlspecialchars($_POST['app_nombre_largo']));
            $nombre_medium = trim(htmlspecialchars($_POST['app_nombre_medium']));
            $nombre_corto = trim(htmlspecialchars($_POST['app_nombre_corto']));

            // Crear aplicación usando query directa
            $query = "INSERT INTO aplicacion (app_nombre_largo, app_nombre_medium, app_nombre_corto, app_situacion) 
                      VALUES ('$nombre_largo', '$nombre_medium', '$nombre_corto', 1)";
            
            $resultado = Aplicaciones::SQL($query);
            
            if ($resultado) {
                http_response_code(201);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Aplicación guardada correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al guardar la aplicación'
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

    public static function modificarAPI()
    {
        getHeadersApi();
        
        if (empty($_POST['app_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de aplicación es requerido'
            ]);
            return;
        }

        try {
            // Validaciones básicas
            if (empty($_POST['app_nombre_largo']) || empty($_POST['app_nombre_medium']) || empty($_POST['app_nombre_corto'])) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Todos los campos son obligatorios'
                ]);
                return;
            }

            $app_id = intval($_POST['app_id']);
            $nombre_largo = trim(htmlspecialchars($_POST['app_nombre_largo']));
            $nombre_medium = trim(htmlspecialchars($_POST['app_nombre_medium']));
            $nombre_corto = trim(htmlspecialchars($_POST['app_nombre_corto']));

            $query = "UPDATE aplicacion SET 
                        app_nombre_largo = '$nombre_largo',
                        app_nombre_medium = '$nombre_medium',
                        app_nombre_corto = '$nombre_corto'
                      WHERE app_id = $app_id";
            
            $resultado = Aplicaciones::SQL($query);
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Aplicación modificada correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al modificar aplicación'
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
                'mensaje' => 'ID de aplicación es requerido'
            ]);
            return;
        }

        try {
            $query = "UPDATE aplicacion SET app_situacion = 0 WHERE app_id = " . intval($_GET['id']);
            $resultado = Aplicaciones::SQL($query);
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Aplicación eliminada correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al eliminar aplicación'
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
}