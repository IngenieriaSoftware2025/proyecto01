<?php

namespace Controllers;

use Model\ActiveRecord;
use Model\Aplicacion;
use MVC\Router;
use Exception;

class AplicacionController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        $router->render('aplicacion/index', []);
    }

    
}