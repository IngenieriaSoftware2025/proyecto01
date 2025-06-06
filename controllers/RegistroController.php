<?php

namespace Controllers;

use Model\ActiveRecord;
use Model\Usuario;
use MVC\Router;
use Exception;

class RegistroController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        $router->render('registro/index', []);
    }

    public static function guardarAPI()
    {
        getHeadersApi();
        if (empty($_POST['usuario_nom1'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El primer nombre del usuario es obligatorio'
            ]);
            return;
        }
        if (empty($_POST['usuario_ape1'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El primer apellido del usuario es obligatorio'
            ]);
            return;
        }
        if (empty($_POST['usuario_tel']) || strlen($_POST['usuario_tel']) != 8) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El teléfono debe tener 8 dígitos'
            ]);
            return;
        }
        if (empty($_POST['usuario_direc'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La dirección del usuario es obligatoria'
            ]);
            return;
        }
        if (empty($_POST['usuario_dpi']) || strlen($_POST['usuario_dpi']) != 13) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El DPI debe tener 13 dígitos'
            ]);
            return;
        }
        if (empty($_POST['usuario_correo']) || !filter_var($_POST['usuario_correo'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electrónico es obligatorio y debe ser válido'
            ]);
            return;
        }
        if (empty($_POST['usuario_contra'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La contraseña es obligatoria'
            ]);
            return;
        }
        if (strlen($_POST['usuario_contra']) < 10) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La contraseña debe tener al menos 10 caracteres'
            ]);
            return;
        }
        if (!preg_match('/[A-Z]/', $_POST['usuario_contra'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La contraseña debe contener al menos una letra mayúscula'
            ]);
            return;
        }
        if (!preg_match('/[a-z]/', $_POST['usuario_contra'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La contraseña debe contener al menos una letra minúscula'
            ]);
            return;
        }
        if (!preg_match('/[0-9]/', $_POST['usuario_contra'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La contraseña debe contener al menos un número'
            ]);
            return;
        }
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};:\'"\\|,.<>\/?]/', $_POST['usuario_contra'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La contraseña debe contener al menos un carácter especial (!@#$%^&*()_+-=[]{};\'"\\|,.<>/?)'
            ]);
            return;
        }

        try {
            $usuarioExistenteDpi = Usuario::where('usuario_dpi', $_POST['usuario_dpi']);
            if ($usuarioExistenteDpi) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe un usuario registrado con este DPI'
                ]);
                return;
            }

            $usuarioExistenteCorreo = Usuario::where('usuario_correo', $_POST['usuario_correo']);
            if ($usuarioExistenteCorreo) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe un usuario registrado con este correo electrónico'
                ]);
                return;
            }

            $_POST['usuario_nom1'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_nom1']))));
            $_POST['usuario_nom2'] = !empty($_POST['usuario_nom2']) ? ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_nom2'])))) : '';
            $_POST['usuario_ape1'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_ape1']))));
            $_POST['usuario_ape2'] = !empty($_POST['usuario_ape2']) ? ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_ape2'])))) : '';
            $_POST['usuario_tel'] = filter_var($_POST['usuario_tel'], FILTER_SANITIZE_NUMBER_INT);
            $_POST['usuario_direc'] = trim(htmlspecialchars($_POST['usuario_direc']));
            $_POST['usuario_dpi'] = filter_var($_POST['usuario_dpi'], FILTER_SANITIZE_NUMBER_INT);
            $_POST['usuario_correo'] = filter_var($_POST['usuario_correo'], FILTER_SANITIZE_EMAIL);
            
            $usuario_contra_hash = password_hash($_POST['usuario_contra'], PASSWORD_DEFAULT);
            $usuario_token = bin2hex(random_bytes(32));

            $usuario = new Usuario([
                'usuario_nom1' => $_POST['usuario_nom1'],
                'usuario_nom2' => $_POST['usuario_nom2'],
                'usuario_ape1' => $_POST['usuario_ape1'],
                'usuario_ape2' => $_POST['usuario_ape2'],
                'usuario_tel' => $_POST['usuario_tel'],
                'usuario_direc' => $_POST['usuario_direc'],
                'usuario_dpi' => $_POST['usuario_dpi'],
                'usuario_correo' => $_POST['usuario_correo'],
                'usuario_contra' => $usuario_contra_hash,
                'usuario_token' => $usuario_token,
                'usuario_fotografia' => $_POST['usuario_fotografia'] ?? '',
                'usuario_situacion' => 1
            ]);

            $crear = $usuario->crear();

            http_response_code(201);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Usuario guardado exitosamente',
                'datos' => [
                    'usuario_id' => $crear,
                    'usuario_token' => $usuario_token
                ]
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar el usuario',
                'detalle' => $e->getMessage()
            ]);
        }
    }

}
?>

