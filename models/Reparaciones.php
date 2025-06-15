<?php

namespace Model;

use Model\ActiveRecord;

class Reparaciones extends ActiveRecord
{
    public static $tabla = 'reparaciones';

    public static $columnasDB = [
        'reparacion_id',
        'cliente_id',
        'dispositivo_marca',
        'dispositivo_modelo',
        'dispositivo_serie',
        'dispositivo_imei',
        'problema_reportado',
        'diagnostico',
        'solucion_aplicada',
        'tipo_servicio_id',
        'tecnico_asignado',
        'estado',
        'fecha_ingreso',
        'fecha_diagnostico',
        'fecha_finalizacion',
        'fecha_entrega',
        'presupuesto_inicial',
        'costo_final',
        'anticipo',
        'observaciones',
        'situacion'
    ];

    public static $idTabla = 'reparacion_id';

    public $reparacion_id;
    public $cliente_id;
    public $dispositivo_marca;
    public $dispositivo_modelo;
    public $dispositivo_serie;
    public $dispositivo_imei;
    public $problema_reportado;
    public $diagnostico;
    public $solucion_aplicada;
    public $tipo_servicio_id;
    public $tecnico_asignado;
    public $estado;
    public $fecha_ingreso;
    public $fecha_diagnostico;
    public $fecha_finalizacion;
    public $fecha_entrega;
    public $presupuesto_inicial;
    public $costo_final;
    public $anticipo;
    public $observaciones;
    public $situacion;

    public function __construct($args = [])
    {
        $this->reparacion_id = $args['reparacion_id'] ?? null;
        $this->cliente_id = $args['cliente_id'] ?? '';
        $this->dispositivo_marca = $args['dispositivo_marca'] ?? '';
        $this->dispositivo_modelo = $args['dispositivo_modelo'] ?? '';
        $this->dispositivo_serie = $args['dispositivo_serie'] ?? '';
        $this->dispositivo_imei = $args['dispositivo_imei'] ?? '';
        $this->problema_reportado = $args['problema_reportado'] ?? '';
        $this->diagnostico = $args['diagnostico'] ?? '';
        $this->solucion_aplicada = $args['solucion_aplicada'] ?? '';
        $this->tipo_servicio_id = $args['tipo_servicio_id'] ?? null;
        $this->tecnico_asignado = $args['tecnico_asignado'] ?? null;
        $this->estado = $args['estado'] ?? 'RECIBIDO';
        $this->fecha_ingreso = $args['fecha_ingreso'] ?? null;
        $this->fecha_diagnostico = $args['fecha_diagnostico'] ?? null;
        $this->fecha_finalizacion = $args['fecha_finalizacion'] ?? null;
        $this->fecha_entrega = $args['fecha_entrega'] ?? null;
        $this->presupuesto_inicial = $args['presupuesto_inicial'] ?? 0.00;
        $this->costo_final = $args['costo_final'] ?? 0.00;
        $this->anticipo = $args['anticipo'] ?? 0.00;
        $this->observaciones = $args['observaciones'] ?? '';
        $this->situacion = $args['situacion'] ?? 1;
    }
}
