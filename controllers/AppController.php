<?php

namespace Controllers;

use MVC\Router;

class AppController {
    public static function index(Router $router){
        session_start();
        
        // Verificar si hay sesión activa
        if (!isset($_SESSION['auth_user']) || !isset($_SESSION['login'])) {
            header('Location: /proyecto01/login');
            exit;
        }
        
        // Mostrar página principal con datos de sesión
        $router->render('pages/index', [
            'usuario_nombre' => $_SESSION['usuario_nombre'] ?? 'Usuario'
        ]);
    }
} 