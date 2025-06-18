<?php

function debuguear($variable) {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function s($html) {
    $s = htmlspecialchars($html);
    return $s;
}

// Función que revisa que el usuario este autenticado
function isAuth() {
    session_start();
    if(!isset($_SESSION['login'])) {
        header('Location: /proyecto01/');
    }
}
function isAuthApi() {
    getHeadersApi();
    session_start();
    if(!isset($_SESSION['login'])) {
        echo json_encode([    
            "mensaje" => "No esta autenticado",

            "codigo" => 4,
        ]);
        exit;
    }
}

function isNotAuth(){
    session_start();
    if(isset($_SESSION['login'])) {
        header('Location: /proyecto01/inicio');
    }
}


function hasPermission(array $roles_permitidos){
    session_start();
    
    if(!isset($_SESSION['login']) || !isset($_SESSION['rol'])) {
        header('Location: /proyecto01/');
        exit;
    }

    $rol_usuario = $_SESSION['rol'];
    
    if(!in_array($rol_usuario, $roles_permitidos)) {
        header('Location: /proyecto01/inicio');
        exit;
    }
}

function hasPermissionApi(array $roles_permitidos){
    getHeadersApi();
    session_start();
    
    if(!isset($_SESSION['login']) || !isset($_SESSION['rol'])) {
        echo json_encode([     
            "mensaje" => "No está autenticado",
            "codigo" => 4,
        ]);
        exit;
    }

    $rol_usuario = $_SESSION['rol'];
    
    if(!in_array($rol_usuario, $roles_permitidos)) {
        echo json_encode([     
            "mensaje" => "No tiene permisos para esta acción",
            "codigo" => 4,
        ]);
        exit;
    }
}

function getHeadersApi(){
    return header("Content-type:application/json; charset=utf-8");
}

function asset($ruta){
    return "/". $_ENV['APP_NAME']."/public/" . $ruta;
}

// Función para registrar actividades del usuario
function registrarActividad($modulo, $accion, $tablaAfectada = null, $registroId = null, $descripcion = '', $datosAnteriores = null, $datosNuevos = null) {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    require_once __DIR__ . '/../controllers/HistorialController.php';
    return Controllers\HistorialController::registrarActividad(
        $_SESSION['user_id'],
        $modulo,
        $accion,
        $tablaAfectada,
        $registroId,
        $descripcion,
        $datosAnteriores,
        $datosNuevos
    );
}