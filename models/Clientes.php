<?php

namespace Model;

use Model\ActiveRecord;

class Clientes extends ActiveRecord 
{
    public static $tabla = 'clientes';
    
    public static $columnasDB = [
       'id',
       'nombre',
       'apellido',
       'telefono',
       'nit',
       'correo',
       'situacion' 
    ];

    public static $idTabla = 'id';
    public $id;
    public $nombre;
    public $apellido;
    public $telefono;
    public $nit;
    public $correo;
    public $situacion;  

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->nit = $args['nit'] ?? '';
        $this->correo = $args['correo'] ?? '';
        $this->situacion = $args['situacion'] ?? 1;
    }
}

?>