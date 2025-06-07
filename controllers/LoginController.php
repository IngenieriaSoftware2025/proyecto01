<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;

//C:\docker\app03_jemg\views\clientes\index.php
class LoginController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        $router->render('login/index', [], 'layouts/layoutLogin');
    }
}