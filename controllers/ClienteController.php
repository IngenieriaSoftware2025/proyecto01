<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Clientes;

class ClienteController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        $router->render('clientes/index', []);
    }

    public static function guardarAPI()
    {
        getHeadersApi();

        if (empty($_POST['nombre'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del cliente es obligatorio'
            ]);
            return;
        }

        if (empty($_POST['apellido'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El apellido del cliente es obligatorio'
            ]);
            return;
        }

        if (strlen($_POST['telefono']) != 8) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'telefono debe tener 8 digitos'
            ]);
            return;
        }

        if (!empty($_POST['correo']) && !filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electrónico no es válido'
            ]);
            return;
        }

        try {
            $_POST['nombre'] = ucwords(strtolower(trim(htmlspecialchars($_POST['nombre']))));
            $_POST['apellido'] = ucwords(strtolower(trim(htmlspecialchars($_POST['apellido']))));
            $_POST['telefono'] = filter_var($_POST['telefono'], FILTER_SANITIZE_NUMBER_INT);
            $_POST['nit'] = htmlspecialchars($_POST['nit'] ?? '');
            $_POST['correo'] = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL);

            $cliente = new Clientes([
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'telefono' => $_POST['telefono'],
                'nit' => $_POST['nit'],
                'correo' => $_POST['correo'],
                'situacion' => 1
            ]);

            $crear = $cliente->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Cliente guardado exitosamente'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar el cliente',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function buscarAPI()
    {
        getHeadersApi();
        try {
            $consulta = "SELECT * FROM clientes WHERE situacion = 1 ORDER BY nombre, apellido";
            $cliente = self::fetchArray($consulta);

            if (count($cliente) > 0) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Exito al obtener los datos de los clientes',
                    'data' => $cliente
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al obtener los datos de los clientes',
                    'detalle' => 'no hay clientes'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error de conexion',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function modificarAPI()
    {
        getHeadersApi();

        $id = $_POST['id'];

        if (empty($_POST['nombre'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del cliente es obligatorio'
            ]);
            return;
        }

        if (empty($_POST['apellido'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El apellido del cliente es obligatorio'
            ]);
            return;
        }

        if (strlen($_POST['telefono']) != 8) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'telefono debe tener 8 digitos'
            ]);
            return;
        }

        if (!empty($_POST['correo']) && !filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electrónico no es válido'
            ]);
            return;
        }

        try {
            $cliente = Clientes::find($id);
            $cliente->sincronizar([
                'nombre' => $_POST['nombre'],
                'apellido' => $_POST['apellido'],
                'telefono' => $_POST['telefono'],
                'nit' => $_POST['nit'],
                'correo' => $_POST['correo']
            ]);

            $cliente->actualizar();
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Cliente modificado exitosamente'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar el cliente',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function eliminarAPI($id)
    {
        getHeadersApi();
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
            $update = "UPDATE clientes SET situacion = 0 WHERE id = " . self::$db->quote($id);
            self::SQL($update);
        
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Cliente eliminado correctamente'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar el cliente',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}

?>