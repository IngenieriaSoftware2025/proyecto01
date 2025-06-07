<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Permisos;
use Model\Aplicaciones;

class PermisosController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        $router->render('permisos/index', []);
    }

    public static function buscarAPI()
    {
        getHeadersApi();
        
        try {
            $query = "SELECT 
                        p.permiso_id,
                        p.permiso_app_id,
                        p.permiso_nombre,
                        p.permiso_clave,
                        p.permiso_desc,
                        p.permiso_fecha,
                        p.permiso_situacion,
                        a.app_nombre_corto
                      FROM permiso p
                      LEFT JOIN aplicacion a ON p.permiso_app_id = a.app_id
                      WHERE p.permiso_situacion = 1
                      ORDER BY p.permiso_id DESC";
                      
            $permisos = Permisos::fetchArray($query);
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Permisos encontrados exitosamente',
                'data' => $permisos
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar los permisos',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function buscarAplicacionesAPI()
    {
        getHeadersApi();
        
        try {
            $query = "SELECT app_id, app_nombre_corto FROM aplicacion WHERE app_situacion = 1 ORDER BY app_nombre_corto";
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
                'mensaje' => 'Error al buscar aplicaciones',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarAPI()
    {
        getHeadersApi();
        
        // Validaciones básicas
        if (empty($_POST['permiso_app_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar una aplicación'
            ]);
            return;
        }
        
        if (empty($_POST['permiso_nombre'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del permiso es obligatorio'
            ]);
            return;
        }
        
        if (empty($_POST['permiso_clave'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La clave del permiso es obligatoria'
            ]);
            return;
        }
        
        if (empty($_POST['permiso_desc'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La descripción del permiso es obligatoria'
            ]);
            return;
        }

        try {
            // Verificar si ya existe permiso con esa clave en la misma aplicación
            $app_id = intval($_POST['permiso_app_id']);
            $clave = trim($_POST['permiso_clave']);
            
            $query = "SELECT COUNT(*) as total FROM permiso WHERE permiso_app_id = $app_id AND permiso_clave = '$clave'";
            $existe = Permisos::fetchFirst($query);
            
            if ($existe && $existe['total'] > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe un permiso con esa clave en esta aplicación'
                ]);
                return;
            }

            // Sanitizar datos
            $nombre = trim(htmlspecialchars($_POST['permiso_nombre']));
            $clave = trim(htmlspecialchars($_POST['permiso_clave']));
            $desc = trim(htmlspecialchars($_POST['permiso_desc']));

            // Crear permiso usando query directa
            $query = "INSERT INTO permiso (permiso_app_id, permiso_nombre, permiso_clave, permiso_desc, permiso_situacion) 
                      VALUES ($app_id, '$nombre', '$clave', '$desc', 1)";
            
            $resultado = Permisos::SQL($query);
            
            if ($resultado) {
                http_response_code(201);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Permiso guardado correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al guardar el permiso'
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
        
        if (empty($_POST['permiso_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de permiso es requerido'
            ]);
            return;
        }

        try {
            // Validaciones básicas
            if (empty($_POST['permiso_app_id']) || empty($_POST['permiso_nombre']) || 
                empty($_POST['permiso_clave']) || empty($_POST['permiso_desc'])) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Todos los campos son obligatorios'
                ]);
                return;
            }

            $permiso_id = intval($_POST['permiso_id']);
            $app_id = intval($_POST['permiso_app_id']);
            $nombre = trim(htmlspecialchars($_POST['permiso_nombre']));
            $clave = trim(htmlspecialchars($_POST['permiso_clave']));
            $desc = trim(htmlspecialchars($_POST['permiso_desc']));

            $query = "UPDATE permiso SET 
                        permiso_app_id = $app_id,
                        permiso_nombre = '$nombre',
                        permiso_clave = '$clave',
                        permiso_desc = '$desc'
                      WHERE permiso_id = $permiso_id";
            
            $resultado = Permisos::SQL($query);
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Permiso modificado correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al modificar permiso'
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
                'mensaje' => 'ID de permiso es requerido'
            ]);
            return;
        }

        try {
            $query = "UPDATE permiso SET permiso_situacion = 0 WHERE permiso_id = " . intval($_GET['id']);
            $resultado = Permisos::SQL($query);
            
            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Permiso eliminado correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al eliminar permiso'
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