<?php

namespace Model;

use Model\ActiveRecord;

class Ventas extends ActiveRecord 
{
    public static $tabla = 'ventas';
    
    public static $columnasDB = [
       'venta_id',
       'cliente_id',
       'usuario_id',
       'total',
       'fecha_venta',
       'estado',
       'observaciones',
       'situacion' 
    ];

    public static $idTabla = 'venta_id';
    public $venta_id;
    public $cliente_id;
    public $usuario_id;
    public $total;
    public $fecha_venta;
    public $estado;
    public $observaciones;
    public $situacion;

    public function __construct($args = [])
    {
        $this->venta_id = $args['venta_id'] ?? null;
        $this->cliente_id = $args['cliente_id'] ?? '';
        $this->usuario_id = $args['usuario_id'] ?? '';
        $this->total = $args['total'] ?? 0.00;
        $this->fecha_venta = $args['fecha_venta'] ?? null;
        $this->estado = $args['estado'] ?? 'COMPLETADA';
        $this->observaciones = $args['observaciones'] ?? '';
        $this->situacion = $args['situacion'] ?? 1;
    }
}
?>