<?php

namespace Model;

use Model\ActiveRecord;

class VentaDetalle extends ActiveRecord 
{
    public static $tabla = 'venta_detalle';
    
    public static $columnasDB = [
       'detalle_id',
       'venta_id',
       'inventario_id',
       'cantidad',
       'precio_unitario',
       'subtotal'
    ];

    public static $idTabla = 'detalle_id';
    public $detalle_id;
    public $venta_id;
    public $inventario_id;
    public $cantidad;
    public $precio_unitario;
    public $subtotal;

    public function __construct($args = [])
    {
        $this->detalle_id = $args['detalle_id'] ?? null;
        $this->venta_id = $args['venta_id'] ?? '';
        $this->inventario_id = $args['inventario_id'] ?? '';
        $this->cantidad = $args['cantidad'] ?? 1;
        $this->precio_unitario = $args['precio_unitario'] ?? 0.00;
        $this->subtotal = $args['subtotal'] ?? 0.00;
    }
}
?>