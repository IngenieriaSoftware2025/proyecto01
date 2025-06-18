<?php

namespace Model;

use Model\ActiveRecord;

class HistorialActividades extends ActiveRecord 
{
    public static $tabla = 'historial_actividades';
    
    public static $columnasDB = [
       'historial_id',
       'historial_usuario_id',
       'historial_modulo',
       'historial_accion',
       'historial_tabla_afectada',
       'historial_registro_id',
       'historial_descripcion',
       'historial_datos_anteriores',
       'historial_datos_nuevos',
       'historial_ip',
       'historial_fecha',
       'historial_situacion'
    ];

    public static $idTabla = 'historial_id';
    
    public $historial_id;
    public $historial_usuario_id;
    public $historial_modulo;
    public $historial_accion;
    public $historial_tabla_afectada;
    public $historial_registro_id;
    public $historial_descripcion;
    public $historial_datos_anteriores;
    public $historial_datos_nuevos;
    public $historial_ip;
    public $historial_fecha;
    public $historial_situacion;

    public function __construct($args = [])
    {
        $this->historial_id = $args['historial_id'] ?? null;
        $this->historial_usuario_id = $args['historial_usuario_id'] ?? null;
        $this->historial_modulo = $args['historial_modulo'] ?? '';
        $this->historial_accion = $args['historial_accion'] ?? '';
        $this->historial_tabla_afectada = $args['historial_tabla_afectada'] ?? null;
        $this->historial_registro_id = $args['historial_registro_id'] ?? null;
        $this->historial_descripcion = $args['historial_descripcion'] ?? '';
        $this->historial_datos_anteriores = $args['historial_datos_anteriores'] ?? null;
        $this->historial_datos_nuevos = $args['historial_datos_nuevos'] ?? null;
        $this->historial_ip = $args['historial_ip'] ?? '';
        $this->historial_fecha = $args['historial_fecha'] ?? null;
        $this->historial_situacion = $args['historial_situacion'] ?? 1;
    }
}
?>