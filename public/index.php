<?php 
require_once __DIR__ . '/../includes/app.php';


use MVC\Router;
use Controllers\AppController;
use Controllers\LoginController;
use Controllers\RegistroController;
use Controllers\AplicacionController;
use Controllers\PermisosController;

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
$router->post('/registro/modificarAPI', [RegistroController::class,'modificarAPI']);
$router->get('/registro/eliminarAPI', [RegistroController::class,'eliminarAPI']);

//RUTAS PARA APLICACIONES
$router->get('/aplicaciones', [AplicacionController::class, 'renderizarPagina']);
$router->post('/aplicaciones/guardar', [AplicacionController::class, 'guardarAPI']);
$router->get('/aplicaciones/buscarAPI', [AplicacionController::class, 'buscarAPI']);
$router->post('/aplicaciones/modificarAPI', [AplicacionController::class, 'modificarAPI']);
$router->get('/aplicaciones/eliminarAPI', [AplicacionController::class, 'eliminarAPI']);

//RUTAS PARA PERMISOS
$router->get('/permisos', [PermisosController::class, 'renderizarPagina']);
$router->post('/permisos/guardar', [PermisosController::class, 'guardarAPI']);
$router->get('/permisos/buscarAPI', [PermisosController::class, 'buscarAPI']);
$router->get('/permisos/buscarAplicacionesAPI', [PermisosController::class, 'buscarAplicacionesAPI']);
$router->post('/permisos/modificarAPI', [PermisosController::class, 'modificarAPI']);
$router->get('/permisos/eliminarAPI', [PermisosController::class, 'eliminarAPI']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
