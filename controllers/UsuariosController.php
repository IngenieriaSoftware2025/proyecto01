<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Usuario;

class UsuariosController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        isAuth(); // Verificar autenticación
        hasPermission(['ADMIN']); // Solo administradores
        $router->render('usuarios/index', []);
    }

    // MÉTODO PARA BUSCAR USUARIOS
    public static function buscarAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();

        try {
            $consulta = "SELECT usu_id, usu_codigo, usu_nombre, usu_situacion 
                        FROM usuario_login2025 
                        WHERE usu_situacion IN (1,2) 
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

    // MÉTODO PARA CREAR NUEVO USUARIO
    public static function guardarAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();

        if (empty($_POST['usu_codigo'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El código de usuario es obligatorio'
            ]);
            return;
        }

        if (empty($_POST['usu_nombre'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del usuario es obligatorio'
            ]);
            return;
        }

        if (empty($_POST['usu_password'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La contraseña es obligatoria'
            ]);
            return;
        }

        try {
            $codigo = intval($_POST['usu_codigo']);
            $nombre = trim(htmlspecialchars($_POST['usu_nombre']));
            $password = password_hash($_POST['usu_password'], PASSWORD_DEFAULT);
            $situacion = intval($_POST['usu_situacion'] ?? 1);

            $consulta = "SELECT COUNT(*) as total FROM usuario_login2025 WHERE usu_codigo = $codigo";
            $resultado = self::fetchFirst($consulta);

            if ($resultado['total'] > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe un usuario con este código'
                ]);
                return;
            }

            $query = "INSERT INTO usuario_login2025 (usu_codigo, usu_nombre, usu_password, usu_situacion) 
         VALUES ($codigo, '$nombre', '$password', $situacion)";

            $resultado = self::SQL($query);

            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Usuario creado exitosamente'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al crear el usuario'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar el usuario',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTODO PARA MODIFICAR USUARIO
    public static function modificarAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();

        $id = filter_var($_POST['usu_id'], FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID de usuario inválido']);
            return;
        }

        if (empty($_POST['usu_nombre'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El nombre de usuario es obligatorio']);
            return;
        }

        try {
            $nombre = trim(htmlspecialchars($_POST['usu_nombre']));
            $codigo = intval($_POST['usu_codigo']);
            $situacion = intval($_POST['usu_situacion'] ?? 1);

            // Verificar duplicados excluyendo el registro actual
            $consulta = "SELECT COUNT(*) as total FROM usuario_login2025 
                        WHERE usu_nombre = '$nombre' AND usu_id != $id";
            $resultado = self::fetchFirst($consulta);

            if ($resultado['total'] > 0) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Ya existe otro usuario con este nombre']);
                return;
            }

            // Construir query de actualización
            $query = "UPDATE usuario_login2025 SET 
                        usu_nombre = '$nombre',
                        usu_situacion = $situacion";

            // Solo actualizar contraseña si se proporciona
            if (!empty($_POST['usu_password'])) {
                $password = password_hash($_POST['usu_password'], PASSWORD_DEFAULT);
                $query .= ", usu_password = '$password'";
            }

            $query .= " WHERE usu_id = $id";

            $resultado = self::$db->exec($query);

            if ($resultado !== false) {
                http_response_code(200);
                echo json_encode(['codigo' => 1, 'mensaje' => 'Usuario actualizado exitosamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Error al actualizar el usuario']);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar el usuario',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    // MÉTODO PARA ELIMINAR USUARIO (cambiar situación)
    public static function eliminarAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();

        $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID de usuario inválido']);
            return;
        }

        try {
            // No eliminar físicamente, solo cambiar situación
            $query = "UPDATE usuario_login2025 SET usu_situacion = 0 WHERE usu_id = $id";
            $resultado = self::$db->exec($query);

            if ($resultado !== false) {
                http_response_code(200);
                echo json_encode(['codigo' => 1, 'mensaje' => 'Usuario eliminado exitosamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Error al eliminar el usuario']);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar el usuario',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
