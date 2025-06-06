<?php

namespace Controllers;

use Model\ActiveRecord;
use Model\Permisos;
use MVC\Router;
use Exception;

class PermisosController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        $router->render('permisos/index', []);
    }

    
}