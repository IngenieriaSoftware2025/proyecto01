<?php

namespace Model;

use Model\ActiveRecord;

class TiposServicio extends ActiveRecord 
{
    public static $tabla = 'tipos_servicio';
    
    public static $columnasDB = [
       'tipo_id',
       'tipo_nombre',
       'tipo_descripcion',
       'precio_base',
       'tiempo_estimado',
       'situacion'
    ];

    public static $idTabla = 'tipo_id';
    
    public $tipo_id;
    public $tipo_nombre;
    public $tipo_descripcion;
    public $precio_base;
    public $tiempo_estimado;
    public $situacion;

    public function __construct($args = [])
    {
        $this->tipo_id = $args['tipo_id'] ?? null;
        $this->tipo_nombre = $args['tipo_nombre'] ?? '';
        $this->tipo_descripcion = $args['tipo_descripcion'] ?? '';
        $this->precio_base = $args['precio_base'] ?? 0.00;
        $this->tiempo_estimado = $args['tiempo_estimado'] ?? 1;
        $this->situacion = $args['situacion'] ?? 1;
    }
}
?>