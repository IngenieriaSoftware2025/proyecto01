<?php 
require_once __DIR__ . '/../includes/app.php';


use MVC\Router;
use Controllers\AppController;
use Controllers\AplicacionController;
use Controllers\LoginController;
use Controllers\PermisosController;
use Controllers\RegistroController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class,'index']);

//usuarios
$router->get('/registro', [RegistroController::class, 'renderizarPagina']);
$router->post('/registro/guardarAPI', [RegistroController::class, 'guardarAPI']);
$router->get('/registro/buscarAPI', [RegistroController::class, 'buscarAPI']);
$router->post('/registro/modificarAPI', [RegistroController::class, 'modificarAPI']);
$router->get('/registro/eliminarAPI', [RegistroController::class, 'eliminarAPI']);


//login
$router->get('/login', [LoginController::class, 'renderizarPagina']);



// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
