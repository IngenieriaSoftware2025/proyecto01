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
        isAuth(); // Verificar que esté logueado
        $router->render('clientes/index', []);
    }

    // MÉTODO PARA VALIDAR TELÉFONO GUATEMALTECO
    private static function validarTelefono($telefono)
    {
        return preg_match('/^[2-8]\d{7}$/', $telefono);
    }

    // MÉTODO PARA VALIDAR NIT GUATEMALTECO
    private static function validarNit($nit)
    {
        $nit = trim($nit);

        if (preg_match('/^(\d+)-?([\dkK])$/', $nit, $matches)) {
            $numero = $matches[1];
            $verificador = strtolower($matches[2]) === 'k' ? 10 : intval($matches[2]);

            $add = 0;
            for ($i = 0; $i < strlen($numero); $i++) {
                // FÓRMULA CORREGIDA: Equivalente exacta del JavaScript
                $add += ((($i - strlen($numero)) * -1) + 1) * intval($numero[$i]);
            }

            return ((11 - ($add % 11)) % 11) === $verificador;
        }

        return false;
    }

    // MÉTODO PARA VALIDAR DPI GUATEMALTECO
    private static function validarDpi($dpi)
    {
        return preg_match('/^\d{13}$/', $dpi);
    }

    // MÉTODO PARA DETECTAR TIPO DE DOCUMENTO
    private static function detectarTipoDocumento($documento)
    {
        $documento = trim($documento);

        if (preg_match('/^[\d]+-?[\dkK]$/', $documento)) {
            return ['tipo' => 'NIT', 'valido' => self::validarNit($documento)];
        } elseif (preg_match('/^\d{13}$/', $documento)) {
            return ['tipo' => 'DPI', 'valido' => self::validarDpi($documento)];
        }

        return ['tipo' => 'DESCONOCIDO', 'valido' => false];
    }

    // MÉTODO PARA VERIFICAR DUPLICADOS
    private static function verificarDuplicados($documento, $id = null)
    {
        $condicion = $id ? " AND id != " . intval($id) : "";
        $documento = trim($documento);

        $query = "SELECT COUNT(*) as total FROM clientes 
                  WHERE nit = '" . $documento . "' AND situacion IN (1,2,3)" . $condicion;

        $resultado = self::fetchFirst($query);
        return $resultado['total'] > 0;
    }

    public static function guardarAPI()
    {
        hasPermissionApi(['ADMIN', 'USER']); // Ambos roles pueden crear clientes
        getHeadersApi();

        // VALIDACIONES BÁSICAS
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

        // VALIDACIÓN TELÉFONO GUATEMALTECO
        if (empty($_POST['telefono']) || !self::validarTelefono($_POST['telefono'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El teléfono debe tener 8 dígitos y comenzar con 2,3,4,5,6,7 u 8'
            ]);
            return;
        }

        // VALIDACIÓN EMAIL
        if (!empty($_POST['correo']) && !filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electrónico no es válido'
            ]);
            return;
        }

        // VALIDACIÓN DOCUMENTO (NIT/DPI)
        $documento = trim($_POST['nit'] ?? '');
        if (!empty($documento)) {
            $validacionDoc = self::detectarTipoDocumento($documento);

            if (!$validacionDoc['valido']) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El documento ingresado no es válido. Use formato NIT (123456-7)'
                ]);
                return;
            }

            // VERIFICAR DUPLICADOS
            if (self::verificarDuplicados($documento)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe un cliente registrado con este documento'
                ]);
                return;
            }
        }

        try {
            // SANITIZAR DATOS
            $nombre = ucwords(strtolower(trim(htmlspecialchars($_POST['nombre']))));
            $apellido = ucwords(strtolower(trim(htmlspecialchars($_POST['apellido']))));
            $telefono = filter_var($_POST['telefono'], FILTER_SANITIZE_NUMBER_INT);
            $correo = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL);
            $situacion = intval($_POST['situacion'] ?? 1);

            // CREAR CLIENTE
            $cliente = new Clientes([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'telefono' => $telefono,
                'nit' => $documento,
                'correo' => $correo,
                'situacion' => $situacion
            ]);

            $crear = $cliente->crear();
            registrarActividad(
                'CLIENTES',
                'CREATE',
                'clientes',
                null, // Se podría obtener el ID del cliente creado
                'Cliente creado: ' . $nombre . ' ' . $apellido,
                null,
                [
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'telefono' => $telefono,
                    'nit' => $documento
                ]
            );
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
        hasPermissionApi(['ADMIN', 'USER']); // Ambos roles pueden ver clientes
        getHeadersApi();
        try {
            $consulta = "SELECT id, nombre, apellido, telefono, nit, correo, situacion 
                        FROM clientes WHERE situacion IN (1,2,3) ORDER BY nombre, apellido";
            $clientes = self::fetchArray($consulta);

            // PROCESAR DATOS PARA LA VISTA
            $clientesProcesados = [];
            foreach ($clientes as $cliente) {
                $tipoDoc = self::detectarTipoDocumento($cliente['nit']);

                $clientesProcesados[] = [
                    'id' => $cliente['id'],
                    'nombre' => $cliente['nombre'],
                    'apellido' => $cliente['apellido'],
                    'telefono' => $cliente['telefono'],
                    'nit' => $cliente['nit'],
                    'tipo_documento' => $tipoDoc['tipo'],
                    'correo' => $cliente['correo'],
                    'situacion' => $cliente['situacion'],
                    'estado_texto' => self::getEstadoTexto($cliente['situacion'])
                ];
            }

            if (count($clientesProcesados) > 0) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Clientes encontrados exitosamente',
                    'data' => $clientesProcesados
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No hay clientes registrados'
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

    // MÉTODO PARA OBTENER TEXTO DEL ESTADO
    private static function getEstadoTexto($situacion)
    {
        switch ($situacion) {
            case 1:
                return 'Activo';
            case 2:
                return 'Inactivo';
            case 3:
                return 'Moroso';
            default:
                return 'Desconocido';
        }
    }

    public static function modificarAPI()
    {
        hasPermissionApi(['ADMIN']);
        getHeadersApi();

        $id = $_POST['id'];

        // VALIDACIONES BÁSICAS
        if (empty($_POST['nombre'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El nombre del cliente es obligatorio']);
            return;
        }

        if (empty($_POST['apellido'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El apellido del cliente es obligatorio']);
            return;
        }

        if (empty($_POST['telefono']) || !self::validarTelefono($_POST['telefono'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El teléfono debe tener 8 dígitos y comenzar con 2,3,4,5,6,7 u 8']);
            return;
        }

        // VALIDACIÓN DOCUMENTO
        $documento = trim($_POST['nit'] ?? '');
        if (!empty($documento)) {
            $validacionDoc = self::detectarTipoDocumento($documento);

            if (!$validacionDoc['valido']) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'El documento ingresado no es válido']);
                return;
            }

            if (self::verificarDuplicados($documento, $id)) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Ya existe otro cliente con este documento']);
                return;
            }
        }

        try {
            // 1. OBTENER DATOS ANTERIORES USANDO QUERY DIRECTA
            $queryDatosAnteriores = "SELECT * FROM clientes WHERE id = " . intval($id);
            $clienteArray = self::fetchFirst($queryDatosAnteriores);

            $datosAnteriores = [
                'nombre' => $clienteArray['nombre'],
                'apellido' => $clienteArray['apellido'],
                'telefono' => $clienteArray['telefono'],
                'nit' => $clienteArray['nit'],
                'correo' => $clienteArray['correo'],
                'situacion' => $clienteArray['situacion']
            ];

            // 2. PREPARAR DATOS NUEVOS
            $datosNuevos = [
                'nombre' => ucwords(strtolower(trim($_POST['nombre']))),
                'apellido' => ucwords(strtolower(trim($_POST['apellido']))),
                'telefono' => $_POST['telefono'],
                'nit' => $documento,
                'correo' => $_POST['correo'],
                'situacion' => $_POST['situacion'] ?? 1
            ];

            // 3. ACTUALIZAR CLIENTE
            $cliente = Clientes::find($id);
            $cliente->sincronizar($datosNuevos);
            $cliente->actualizar();

            // 4. REGISTRAR ACTIVIDAD
            registrarActividad(
                'CLIENTES',
                'UPDATE',
                'clientes',
                $id,
                'Cliente modificado: ' . $datosNuevos['nombre'] . ' ' . $datosNuevos['apellido'],
                $datosAnteriores,
                $datosNuevos
            );

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

    public static function eliminarAPI()
    {
        hasPermissionApi(['ADMIN']); // Solo ADMIN puede eliminar
        getHeadersApi();
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
            $update = "UPDATE clientes SET situacion = 0 WHERE id = " . self::$db->quote($id);
            self::SQL($update);
            registrarActividad(
                'CLIENTES',
                'DELETE',
                'clientes',
                $id,
                'Cliente eliminado ID: ' . $id
            );
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
