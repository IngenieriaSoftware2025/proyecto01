<?php
// crea nombre de espacio Model
namespace Model;
// Importa la clase ActiveRecord del nombre de espacio Model
use Model\ActiveRecord;
// Crea la clase de instancia HistorialAct y hereda las funciones de ActiveRecord
class HistorialAct extends ActiveRecord {
    
    // Crea las propiedades de la clase
    public static $tabla = 'historial_act';
    public static $idTabla = 'historial_id';
    public static $columnasDB = 
    [
        'historial_usuario_id',
        'historial_fecha',
        'historial_ruta',
        'historial_ejecucion',
        'historial_situacion'
    ];
    
    // Crea las variables para almacenar los datos
    public $historial_id;
    public $historial_usuario_id;
    public $historial_fecha;
    public $historial_ruta;
    public $historial_ejecucion;
    public $historial_situacion;
    
    public function __construct($historial = [])
    {
        $this->historial_id = $historial['historial_id'] ?? null;
        $this->historial_usuario_id = $historial['historial_usuario_id'] ?? 0;
        $this->historial_fecha = $historial['historial_fecha'] ?? null;
        $this->historial_ruta = $historial['historial_ruta'] ?? 0;
        $this->historial_ejecucion = $historial['historial_ejecucion'] ?? '';
        $this->historial_situacion = $historial['historial_situacion'] ?? 1;
    }

}