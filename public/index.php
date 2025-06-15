<?php 
require_once __DIR__ . '/../includes/app.php';


use MVC\Router;
use Controllers\AppController;
use Controllers\ClienteController;
use Controllers\MarcaController;
use Controllers\InventarioController;
use Controllers\VentasController;
use Controllers\ReparacionesController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class,'index']);
$router->post('/API/login', [AppController::class,'loginAPI']);
$router->get('/verificarSesion', [AppController::class,'verificarSesion']);
$router->get('/logout', [AppController::class,'logout']);
$router->get('/inicio', [AppController::class,'renderInicio']);

// Rutas para Clientes
$router->get('/clientes', [ClienteController::class, 'renderizarPagina']);
$router->post('/clientes/guardarAPI', [ClienteController::class, 'guardarAPI']);
$router->get('/clientes/buscarAPI', [ClienteController::class, 'buscarAPI']);
$router->post('/clientes/modificarAPI', [ClienteController::class, 'modificarAPI']);
$router->get('/clientes/eliminarAPI', [ClienteController::class, 'eliminarAPI']);

// Rutas para Marcas
$router->get('/marcas', [MarcaController::class, 'renderizarPagina']);
$router->post('/marcas/guardarAPI', [MarcaController::class, 'guardarAPI']);
$router->get('/marcas/buscarAPI', [MarcaController::class, 'buscarAPI']);
$router->post('/marcas/modificarAPI', [MarcaController::class, 'modificarAPI']);
$router->get('/marcas/eliminarAPI', [MarcaController::class, 'eliminarAPI']);

// Rutas para Inventario
$router->get('/inventario', [InventarioController::class, 'renderizarPagina']);
$router->post('/inventario/guardarAPI', [InventarioController::class, 'guardarAPI']);
$router->get('/inventario/buscarAPI', [InventarioController::class, 'buscarAPI']);
$router->get('/inventario/buscarMarcasAPI', [InventarioController::class, 'buscarMarcasAPI']);
$router->post('/inventario/modificarAPI', [InventarioController::class, 'modificarAPI']);
$router->get('/inventario/eliminarAPI', [InventarioController::class, 'eliminarAPI']);

// Rutas para Ventas
$router->get('/ventas', [VentasController::class, 'renderizarPagina']);
$router->post('/ventas/guardarAPI', [VentasController::class, 'guardarAPI']);
$router->get('/ventas/buscarAPI', [VentasController::class, 'buscarAPI']);
$router->get('/ventas/buscarClientesAPI', [VentasController::class, 'buscarClientesAPI']);
$router->get('/ventas/buscarInventarioDisponibleAPI', [VentasController::class, 'buscarInventarioDisponibleAPI']);
$router->get('/ventas/verDetalleAPI', [VentasController::class, 'verDetalleAPI']);
$router->get('/ventas/anularAPI', [VentasController::class, 'anularAPI']);

// Rutas para Reparaciones
$router->get('/reparaciones', [ReparacionesController::class, 'renderizarPagina']);
$router->post('/reparaciones/guardarAPI', [ReparacionesController::class, 'guardarAPI']);
$router->get('/reparaciones/buscarAPI', [ReparacionesController::class, 'buscarAPI']);
$router->get('/reparaciones/buscarClientesAPI', [ReparacionesController::class, 'buscarClientesAPI']);
$router->get('/reparaciones/buscarTiposServicioAPI', [ReparacionesController::class, 'buscarTiposServicioAPI']);
$router->get('/reparaciones/buscarTecnicosAPI', [ReparacionesController::class, 'buscarTecnicosAPI']);
$router->get('/reparaciones/buscarMarcasAPI', [ReparacionesController::class, 'buscarMarcasAPI']);
$router->post('/reparaciones/actualizarEstadoAPI', [ReparacionesController::class, 'actualizarEstadoAPI']);
$router->post('/reparaciones/asignarTecnicoAPI', [ReparacionesController::class, 'asignarTecnicoAPI']);
$router->get('/reparaciones/verDetalleAPI', [ReparacionesController::class, 'verDetalleAPI']);
$router->get('/reparaciones/eliminarAPI', [ReparacionesController::class, 'eliminarAPI']);
$router->get('/reparaciones/buscarMarcasAPI', [ReparacionesController::class, 'buscarMarcasAPI']);
$router->get('/reparaciones/buscarModelosPorMarcaAPI', [ReparacionesController::class, 'buscarModelosPorMarcaAPI']);


// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
