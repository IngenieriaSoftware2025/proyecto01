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
        $router->render('usuarios/index', []);
    }


    public static function guardarAPI()
    {
        getHeadersApi();
    
        
        //saniticacion de nombre y validaccion con capital
        $_POST['usuario_nom1'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_nom1']))));
        
        $cantidad_nombre = strlen($_POST['usuario_nom1']);
        
        if ($cantidad_nombre < 2) {
            
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Nombre debe de tener mas de 1 caracteres'
            ]);
            exit;
        }
        
        $_POST['usuario_nom2'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_nom2']))));
        
        $cantidad_nombre = strlen($_POST['usuario_nom2']);
        
        if ($cantidad_nombre < 2) {
          
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Nombre debe de tener mas de 1 caracteres'
            ]);
            exit;
        }
        
        $_POST['usuario_ape1'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_ape1']))));
        $cantidad_apellido = strlen($_POST['usuario_ape1']);
        
        if ($cantidad_apellido < 2) {
         
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Nombre debe de tener mas de 1 caracteres'
            ]);
            exit;
        }
        
        $_POST['usuario_ape2'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_ape2']))));
        $cantidad_apellido = strlen($_POST['usuario_ape2']);
        
        if ($cantidad_apellido < 2) {
            
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Nombre debe de tener mas de 1 caracteres'
            ]);
            exit;
        }
        
        $_POST['usuario_tel'] = filter_var($_POST['usuario_tel'], FILTER_SANITIZE_NUMBER_INT);
        if (strlen($_POST['usuario_tel']) != 8) {
        
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El telefono debe de tener 8 numeros'
            ]);
            exit;
        }
        
        $_POST['usuario_direc'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_direc']))));
        
        $_POST['usuario_tel'] = filter_var($_POST['usuario_tel'], FILTER_VALIDATE_INT);
        if (strlen($_POST['usuario_direc']) >= 13) {
           
            
            echo json_encode([
                'codigo' => 0,
                'mennsaje' => 'La cantidad de digitos del DPI debe de ser igual a 13'
            ]);
            exit;
        }
        
        $_POST['usuario_correo'] = filter_var($_POST['usuario_correo'], FILTER_SANITIZE_EMAIL);
        
        if (!filter_var($_POST['usuario_correo'], FILTER_VALIDATE_EMAIL)){
          
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electronico no es valido'
            ]);
        }
        
        
        // VALIDACIÓN: Contraseña
        if (strlen($_POST['usuario_contra']) < 8) {
      
            
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La contraseña debe tener al menos 8 caracteres'
            ]);
            exit;
        }
        
        // VALIDACIÓN: Confirmar contraseña
        if ($_POST['usuario_contra'] !== $_POST['confirmar_contra']) {
            
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Las contraseñas no coinciden'
            ]);
            exit;
        }
        
        $_POST['usuario_token'] = uniqid();
        $dpi = $_POST['usuario_dpi'];
        $_POST['usuario_fecha_creacion'] = '';
        $_POST['usuario_fecha_contra'] = '';
        
        $file = $_FILES['usuario_fotografia'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        
      
        
        
        
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
  
        // Extensiones permitidas
        $allowed = ['jpg', 'jpeg', 'png'];
        
        if (!in_array($fileExtension, $allowed)) {
            
            http_response_code(400);
            echo json_encode([
                'codigo' => 2,
                'mensaje' => 'Solo puede cargar archivos JPG, PNG o JPEG',
            ]);
            exit;
        }
        
        if ($fileSize >= 2000000) {
            
            http_response_code(400);
            echo json_encode([
                'codigo' => 2,
                'mensaje' => 'La imgagen debe pesar menos de 2MB',
            ]);
            exit;
        }
        
        if ($fileError === 0) {
                $ruta = "storage/fotosUsuarios/$dpi.$fileExtension";
                $subido = move_uploaded_file($file['tmp_name'], __DIR__ . "../../" . $ruta);
                
                if ($subido) {
                    
                    $_POST['usuario_contra'] = password_hash($_POST['usuario_contra'], PASSWORD_DEFAULT);
                    $foto = base64_encode(file_get_contents(__DIR__ . '/../' . $ruta));
                    $_SESSION['user']->foto = $foto;
                    $usuario = new Usuarios($_POST);
                    $usuario->usuario_fotografia = $ruta;
                    $resultado = $usuario->crear();

                    if($resultado['resultado'] ==1){
                        
                        http_response_code(200);
                        echo json_encode([
                            'codigo' => 1,
                            'mensaje' => 'Usuario registrado correctamente',
                        ]);
                        
                        exit;
                    }else{
                        
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error en registrar al usuario',
                    'datos' => $_POST,
                    'usuario' => $usuario,
                ]);
                exit;


                    }
                } 
            } else {
                
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error en la carga de fotografia',
                ]);
                exit;
            }
        
            

    }
    
}