<?php

namespace Model;

use Model\ActiveRecord;

class Inventario extends ActiveRecord 
{
    public static $tabla = 'inventario';
    
    public static $columnasDB = [
       'id',
       'marca_id',
       'numero_serie',
       'precio_compra',
       'precio_venta',
       'stock_disponible',
       'estado_dispositivo',
       'estado_inventario',
    //    'fecha_ingreso',
       'observaciones',
       'situacion' 
    ];

    public static $idTabla = 'id';
    public $id;
    public $marca_id;
    public $numero_serie;
    public $precio_compra;
    public $precio_venta;
    public $stock_disponible;
    public $estado_dispositivo;
    public $estado_inventario;
    public $fecha_ingreso;
    public $observaciones;
    public $situacion;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->marca_id = $args['marca_id'] ?? '';
        $this->numero_serie = $args['numero_serie'] ?? '';
        $this->precio_compra = $args['precio_compra'] ?? 0.00;
        $this->precio_venta = $args['precio_venta'] ?? 0.00;
        $this->stock_disponible = $args['stock_disponible'] ?? 1;
        $this->estado_dispositivo = $args['estado_dispositivo'] ?? 'NUEVO';
        $this->estado_inventario = $args['estado_inventario'] ?? 'DISPONIBLE';
        $this->fecha_ingreso = $args['fecha_ingreso'];
        $this->observaciones = $args['observaciones'] ?? '';
        $this->situacion = $args['situacion'] ?? 1;
    }
}

?>