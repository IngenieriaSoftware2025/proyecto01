<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use MVC\Router;

class AppController extends ActiveRecord
{
    public static function index(Router $router)
    {
        $router->render('login/index', [], 'layouts/layoutLogin');
    }

    public static function loginAPI()
    {
        getHeadersApi();

        // Validaciones básicas
        if (empty($_POST['usu_codigo'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El código de usuario es obligatorio'
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
            $usuario = filter_var($_POST['usu_codigo'], FILTER_SANITIZE_NUMBER_INT);
            $contrasena = htmlspecialchars($_POST['usu_password']);

            $queryExisteUser = "SELECT usu_id, usu_nombre, usu_password FROM usuario_login2025 
                               WHERE usu_codigo = $usuario AND usu_situacion = 1";

            $existeUsuario = self::fetchFirst($queryExisteUser);

            if ($existeUsuario) {
                $passDB = $existeUsuario['usu_password'];

                if (password_verify($contrasena, $passDB)) {
                    session_start();

                    $nombreUser = $existeUsuario['usu_nombre'];
                    $usuarioId = $existeUsuario['usu_id'];

                    $_SESSION['user'] = $nombreUser;
                    $_SESSION['user_id'] = $usuarioId;
                    $_SESSION['login'] = true;
                    $_SESSION['tiempo_limite'] = time() + (2 * 60 * 60); // 2 horas

                    // Obtener rol del usuario
                    $sqlPermisos = "SELECT permiso_rol, rol_nombre_ct FROM permiso_login2025
                                   INNER JOIN rol_login2025 ON rol_id = permiso_rol
                                   INNER JOIN usuario_login2025 ON usu_id = permiso_usuario
                                   WHERE usu_codigo = $usuario AND permiso_login2025.permiso_situacion = 1";

                    $permiso = self::fetchFirst($sqlPermisos);

                    if ($permiso) {
                        $_SESSION['rol'] = $permiso['rol_nombre_ct'];
                        $_SESSION['rol_id'] = $permiso['permiso_rol'];
                    } else {
                        $_SESSION['rol'] = 'USER';
                        $_SESSION['rol_id'] = 2;
                    }

                    // Registrar actividad de login exitoso
                    registrarActividad(
                        'LOGIN',
                        'LOGIN',
                        null,
                        null,
                        'Usuario logueado exitosamente: ' . $nombreUser . ' (' . $usuario . ')'
                    );

                    http_response_code(200);
                    echo json_encode([
                        'codigo' => 1,
                        'mensaje' => 'Usuario logueado exitosamente',
                        'datos' => [
                            'usuario_nombre' => $nombreUser,
                            'rol' => $_SESSION['rol'],
                            'redirect_url' => '/proyecto01/inicio'
                        ]
                    ]);
                } else {
                    http_response_code(401);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => 'La contraseña que ingresó es incorrecta'
                    ]);
                }
            } else {
                http_response_code(401);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El usuario que intenta loguearse NO EXISTE'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al intentar loguearse',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function verificarSesion()
    {
        getHeadersApi();
        session_start();

        if (!isset($_SESSION['login']) || !isset($_SESSION['tiempo_limite'])) {
            http_response_code(401);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Sesión no válida',
                'redirect_url' => '/proyecto01/'
            ]);
            return;
        }

        if (time() > $_SESSION['tiempo_limite']) {
            session_destroy();
            http_response_code(401);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Sesión expirada',
                'redirect_url' => '/proyecto01/'
            ]);
            return;
        }

        $_SESSION['tiempo_limite'] = time() + (2 * 60 * 60);

        http_response_code(200);
        echo json_encode([
            'codigo' => 1,
            'mensaje' => 'Sesión válida',
            'datos' => [
                'usuario_nombre' => $_SESSION['user'],
                'rol' => $_SESSION['rol']
            ]
        ]);
    }

    public static function logout()
    {
        session_start();
        
        // Registrar actividad de logout
        if (isset($_SESSION['user'])) {
            registrarActividad(
                'LOGIN',
                'LOGOUT',
                null,
                null,
                'Usuario cerró sesión: ' . $_SESSION['user']
            );
        }
        
        session_destroy();

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        header('Location: /proyecto01/');
        exit;
    }

    public static function renderInicio(Router $router)
    {
        session_start();

        // Verificar si hay sesión activa
        if (!isset($_SESSION['login'])) {
            header('Location: /proyecto01/');
            exit;
        }

        $router->render('pages/index', [
            'usuario_nombre' => $_SESSION['user'],
            'rol' => $_SESSION['rol']
        ]);
    }
}