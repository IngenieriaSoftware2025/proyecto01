<?php 
require_once __DIR__ . '/../includes/app.php';


use MVC\Router;
use Controllers\AppController;
use Controllers\LoginController;
use Controllers\RegistroController;
$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class,'index']);


//RUTAS LOGIN
$router->get('/login', [LoginController::class,'renderizarPagina']);
$router->post('/login/loginAPI', [LoginController::class, 'loginAPI']);
$router->get('/login/logout', [LoginController::class, 'logout']);
$router->get('/login/verificarSesion', [LoginController::class, 'verificarSesion']);

//RUTAS PARA registro
$router->get('/registro', [RegistroController::class,'renderizarPagina']);
$router->post('/registro/guardar', [RegistroController::class,'guardarAPI']);
$router->get('/registro/buscarAPI', [RegistroController::class,'buscarAPI']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
