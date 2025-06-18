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

    // MÉTODO PRIVADO PARA OBTENER ROL POR ID
    private static function obtenerRolPorId($rolId)
    {
        try {
            $consulta = "SELECT rol_nombre_ct FROM rol_login2025 WHERE rol_id = $rolId AND rol_situacion = 1";
            $resultado = self::fetchFirst($consulta);
            return $resultado ? $resultado['rol_nombre_ct'] : null;
        } catch (Exception $e) {
            return null;
        }
    }

    // MÉTODO PRIVADO PARA OBTENER ID DE ROL
    private static function obtenerIdRol($rolNombre)
    {
        try {
            $consulta = "SELECT rol_id FROM rol_login2025 WHERE rol_nombre_ct = '$rolNombre' AND rol_situacion = 1";
            $resultado = self::fetchFirst($consulta);
            return $resultado ? $resultado['rol_id'] : null;
        } catch (Exception $e) {
            return null;
        }
    }

    // MÉTODO PARA CREAR NUEVO USUARIO
    public static function guardarAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();

        // Validaciones de entrada
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

        if (empty($_POST['usu_rol'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El rol de usuario es obligatorio'
            ]);
            return;
        }

        try {
            $codigo = intval($_POST['usu_codigo']);
            $nombre = trim(htmlspecialchars($_POST['usu_nombre']));
            $password = password_hash($_POST['usu_password'], PASSWORD_DEFAULT);
            $rolNombre = trim($_POST['usu_rol']);
            $situacion = intval($_POST['usu_situacion'] ?? 1);

            // Validar rango de código
            if ($codigo < 100000 || $codigo > 999999) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El código debe ser de 6 dígitos (100000-999999)'
                ]);
                return;
            }

            // Verificar si el código ya existe
            $consultaCodigo = "SELECT COUNT(*) as total FROM usuario_login2025 WHERE usu_codigo = $codigo";
            $resultadoCodigo = self::fetchFirst($consultaCodigo);

            if ($resultadoCodigo['total'] > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe un usuario con este código'
                ]);
                return;
            }

            // Verificar si el nombre ya existe
            $consultaNombre = "SELECT COUNT(*) as total FROM usuario_login2025 WHERE usu_nombre = '$nombre'";
            $resultadoNombre = self::fetchFirst($consultaNombre);

            if ($resultadoNombre['total'] > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe un usuario con este nombre'
                ]);
                return;
            }

            // Obtener ID del rol
            $rolId = self::obtenerIdRol($rolNombre);
            if (!$rolId) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Rol seleccionado no válido'
                ]);
                return;
            }

            // Insertar usuario
            $queryUsuario = "INSERT INTO usuario_login2025 (usu_codigo, usu_nombre, usu_password, usu_situacion) 
                           VALUES ($codigo, '$nombre', '$password', $situacion)";

            $resultadoUsuario = self::SQL($queryUsuario);

            if ($resultadoUsuario) {
                // Obtener ID del usuario recién creado
                $queryUltimoId = "SELECT MAX(usu_id) as ultimo_id FROM usuario_login2025 WHERE usu_codigo = $codigo";
                $ultimoUsuario = self::fetchFirst($queryUltimoId);
                $usuarioId = $ultimoUsuario['ultimo_id'];

                // Insertar permiso
                $queryPermiso = "INSERT INTO permiso_login2025 (permiso_usuario, permiso_rol) 
                               VALUES ($usuarioId, $rolId)";
                
                $resultadoPermiso = self::SQL($queryPermiso);

                if ($resultadoPermiso) {
                    http_response_code(200);
                    echo json_encode([
                        'codigo' => 1,
                        'mensaje' => 'Usuario creado exitosamente'
                    ]);
                } else {
                    // Rollback: eliminar usuario si falla la asignación de rol
                    self::SQL("DELETE FROM usuario_login2025 WHERE usu_id = $usuarioId");
                    http_response_code(400);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => 'Error al asignar rol al usuario'
                    ]);
                }
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

            // Verificar duplicado de código excluyendo el registro actual
            $consultaCodigo = "SELECT COUNT(*) as total FROM usuario_login2025 
                             WHERE usu_codigo = $codigo AND usu_id != $id";
            $resultadoCodigo = self::fetchFirst($consultaCodigo);

            if ($resultadoCodigo['total'] > 0) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Ya existe otro usuario con este código']);
                return;
            }

            // Verificar duplicado de nombre excluyendo el registro actual
            $consultaNombre = "SELECT COUNT(*) as total FROM usuario_login2025 
                             WHERE usu_nombre = '$nombre' AND usu_id != $id";
            $resultadoNombre = self::fetchFirst($consultaNombre);

            if ($resultadoNombre['total'] > 0) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Ya existe otro usuario con este nombre']);
                return;
            }

            // Construir query de actualización
            $query = "UPDATE usuario_login2025 SET 
                        usu_nombre = '$nombre',
                        usu_codigo = $codigo,
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
            // Verificar que el usuario existe y no está ya eliminado
            $consultaExiste = "SELECT COUNT(*) as total FROM usuario_login2025 
                             WHERE usu_id = $id AND usu_situacion != 0";
            $resultadoExiste = self::fetchFirst($consultaExiste);

            if ($resultadoExiste['total'] == 0) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Usuario no encontrado o ya eliminado']);
                return;
            }

            // No eliminar físicamente, solo cambiar situación
            $queryUsuario = "UPDATE usuario_login2025 SET usu_situacion = 0 WHERE usu_id = $id";
            $resultadoUsuario = self::$db->exec($queryUsuario);

            // También deshabilitar permisos
            $queryPermisos = "UPDATE permiso_login2025 SET permiso_situacion = 0 WHERE permiso_usuario = $id";
            self::$db->exec($queryPermisos);

            if ($resultadoUsuario !== false) {
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

    // MÉTODO PARA OBTENER ROLES DISPONIBLES
    public static function obtenerRolesAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();

        try {
            $consulta = "SELECT rol_id, rol_nombre, rol_nombre_ct 
                        FROM rol_login2025 
                        WHERE rol_situacion = 1 
                        ORDER BY rol_nombre";
            $roles = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Roles obtenidos exitosamente',
                'data' => $roles
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener roles',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}