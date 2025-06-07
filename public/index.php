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

//RUTAS PARA USUARIOS
$router->get('/usuarios', [RegistroController::class,'renderizarPagina']);
$router->post('/usuarios/guardar', [RegistroController::class,'guardarAPI']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
