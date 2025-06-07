<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Usuarios;

class RegistroController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        $router->render('registro/index', []);
    }

    public static function buscarAPI()
    {
        getHeadersApi();

        try {
            $query = "SELECT 
                    usuario_id,
                    usuario_nom1,
                    usuario_nom2,
                    usuario_ape1,
                    usuario_ape2,
                    usuario_tel,
                    usuario_direc,
                    usuario_dpi,
                    usuario_correo,
                    usuario_fotografia,
                    usuario_situacion
                  FROM usuario 
                  WHERE usuario_situacion = 1
                  ORDER BY usuario_id DESC";

            $usuarios = Usuarios::fetchArray($query);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Usuarios encontrados exitosamente',
                'data' => $usuarios
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al buscar los usuarios',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarAPI()
    {
        getHeadersApi();

        // Validaciones básicas de campos obligatorios
        if (empty($_POST['usuario_nom1'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El primer nombre es obligatorio'
            ]);
            return;
        }

        if (empty($_POST['usuario_ape1'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El primer apellido es obligatorio'
            ]);
            return;
        }

        if (empty($_POST['usuario_tel']) || strlen($_POST['usuario_tel']) != 8) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El teléfono debe tener exactamente 8 dígitos'
            ]);
            return;
        }

        if (empty($_POST['usuario_dpi']) || strlen($_POST['usuario_dpi']) != 13) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El DPI debe tener exactamente 13 dígitos'
            ]);
            return;
        }

        if (empty($_POST['usuario_direc'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La dirección es obligatoria'
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

        if (empty($_POST['usuario_contra']) || strlen($_POST['usuario_contra']) < 10) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La contraseña debe tener al menos 10 caracteres'
            ]);
            return;
        }

        // Validar complejidad de contraseña
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
                'mensaje' => 'La contraseña debe contener al menos un carácter especial'
            ]);
            return;
        }

        // Validar confirmación de contraseña
        if ($_POST['usuario_contra'] !== $_POST['confirmar_contra']) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Las contraseñas no coinciden'
            ]);
            return;
        }

        try {
            // Verificar si ya existe usuario con ese DPI
            $usuarioExistenteDpi = Usuarios::where('usuario_dpi', $_POST['usuario_dpi']);
            if (!empty($usuarioExistenteDpi)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe un usuario registrado con este DPI'
                ]);
                return;
            }

            // Verificar si ya existe usuario con ese correo
            $usuarioExistenteCorreo = Usuarios::where('usuario_correo', $_POST['usuario_correo']);
            if (!empty($usuarioExistenteCorreo)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe un usuario registrado con este correo electrónico'
                ]);
                return;
            }

            // Procesar fotografía
            $nombreFotografia = '';
            try {
                $nombreFotografia = self::procesarFotografia();
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => $e->getMessage()
                ]);
                return;
            }

            // Sanitizar datos
            $usuario_nom1 = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_nom1']))));
            $usuario_nom2 = !empty($_POST['usuario_nom2']) ? ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_nom2'])))) : '';
            $usuario_ape1 = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_ape1']))));
            $usuario_ape2 = !empty($_POST['usuario_ape2']) ? ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_ape2'])))) : '';
            $usuario_tel = filter_var($_POST['usuario_tel'], FILTER_SANITIZE_NUMBER_INT);
            $usuario_direc = trim(htmlspecialchars($_POST['usuario_direc']));
            $usuario_dpi = filter_var($_POST['usuario_dpi'], FILTER_SANITIZE_NUMBER_INT);
            $usuario_correo = filter_var($_POST['usuario_correo'], FILTER_SANITIZE_EMAIL);

            $usuario_contra_hash = password_hash($_POST['usuario_contra'], PASSWORD_DEFAULT);
            $usuario_token = bin2hex(random_bytes(32));

            // Crear usuario
            $usuario = new Usuarios([
                'usuario_nom1' => $usuario_nom1,
                'usuario_nom2' => $usuario_nom2,
                'usuario_ape1' => $usuario_ape1,
                'usuario_ape2' => $usuario_ape2,
                'usuario_tel' => $usuario_tel,
                'usuario_direc' => $usuario_direc,
                'usuario_dpi' => $usuario_dpi,
                'usuario_correo' => $usuario_correo,
                'usuario_contra' => $usuario_contra_hash,
                'usuario_token' => $usuario_token,
                'usuario_fotografia' => $nombreFotografia,
                'usuario_situacion' => 1
            ]);

            $resultado = $usuario->crear();

            if ($resultado['resultado'] == 1) {
                http_response_code(201);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Usuario registrado correctamente',
                    'datos' => [
                        'usuario_id' => $resultado['id'],
                        'usuario_token' => $usuario_token
                    ]
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al registrar el usuario'
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

    private static function procesarFotografia()
    {
        // Si no hay archivo, retornar vacío
        if (!isset($_FILES['usuario_fotografia']) || $_FILES['usuario_fotografia']['error'] === UPLOAD_ERR_NO_FILE) {
            return '';
        }

        $archivo = $_FILES['usuario_fotografia'];

        // Validar errores de subida
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error al subir el archivo: ' . $archivo['error']);
        }

        // Validar tamaño (máximo 2MB)
        $tamañoMaximo = 2 * 1024 * 1024; // 2MB en bytes
        if ($archivo['size'] > $tamañoMaximo) {
            throw new Exception('El archivo es muy grande. Máximo permitido: 2MB');
        }

        // Validar tipo de archivo
        $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $tipoMime = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);

        if (!in_array($tipoMime, $tiposPermitidos)) {
            throw new Exception('Tipo de archivo no permitido. Solo se permiten: JPG, JPEG, PNG');
        }

        // Usar directorio existente fotosUsuarios
        $directorioDestino = __DIR__ . '/../storage/fotosUsuarios/';

        // Verificar que el directorio existe, si no, crearlo
        if (!is_dir($directorioDestino)) {
            if (!mkdir($directorioDestino, 0755, true)) {
                throw new Exception('No se pudo crear el directorio de fotografías');
            }
        }

        // Generar nombre único para el archivo
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'usuario_' . uniqid() . '_' . time() . '.' . $extension;
        $rutaCompleta = $directorioDestino . $nombreArchivo;

        // Mover archivo al directorio de destino
        if (!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            throw new Exception('Error al guardar el archivo en el servidor');
        }

        // Retornar solo el nombre del archivo para guardar en la BD
        return $nombreArchivo;
    }

    public static function modificarAPI()
    {
        getHeadersApi();

        if (empty($_POST['usuario_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de usuario es requerido'
            ]);
            return;
        }

        try {
            $usuario = Usuarios::find($_POST['usuario_id']);
            if (!$usuario) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Usuario no encontrado'
                ]);
                return;
            }

            // Validaciones básicas
            if (empty($_POST['usuario_nom1']) || empty($_POST['usuario_ape1'])) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Nombre y apellido son obligatorios'
                ]);
                return;
            }

            // Sincronizar datos
            $usuario->sincronizar($_POST);

            $resultado = $usuario->guardar();

            if ($resultado['resultado']) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Usuario modificado correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al modificar usuario'
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
                'mensaje' => 'ID de usuario es requerido'
            ]);
            return;
        }

        try {
            $query = "UPDATE usuario SET usuario_situacion = 0 WHERE usuario_id = " . intval($_GET['id']);
            $resultado = Usuarios::SQL($query);

            if ($resultado) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Usuario eliminado correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al eliminar usuario'
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
