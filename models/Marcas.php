<?php

namespace Model;

use Model\ActiveRecord;

class Marcas extends ActiveRecord 
{
    public static $tabla = 'marcas';
    
    public static $columnasDB = [
       'id',
       'nombre',
       'descripcion',
       'modelo',
    //    'fecha_creacion',
       'situacion' 
    ];

    public static $idTabla = 'id';
    public $id;
    public $nombre;
    public $descripcion;
    public $modelo;
    public $fecha_creacion;
    public $situacion;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->modelo = $args['modelo'] ?? '';
        $this->fecha_creacion = $args['fecha_creacion'];
        $this->situacion = $args['situacion'] ?? 1;
    }
}

?>