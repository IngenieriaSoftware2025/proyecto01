<?php
// crea nombre de espacio Model
namespace Model;
// Importa la clase ActiveRecord del nombre de espacio Model
use Model\ActiveRecord;
// Crea la clase de instancia Rutas y hereda las funciones de ActiveRecord
class Rutas extends ActiveRecord {
    
    // Crea las propiedades de la clase
    public static $tabla = 'rutas';
    public static $idTabla = 'ruta_id';
    public static $columnasDB = 
    [
        'ruta_app_id',
        'ruta_nombre',
        'ruta_descripcion',
        'ruta_situacion'
    ];
    
    // Crea las variables para almacenar los datos
    public $ruta_id;
    public $ruta_app_id;
    public $ruta_nombre;
    public $ruta_descripcion;
    public $ruta_situacion;
    
    public function __construct($ruta = [])
    {
        $this->ruta_id = $ruta['ruta_id'] ?? null;
        $this->ruta_app_id = $ruta['ruta_app_id'] ?? 0;
        $this->ruta_nombre = $ruta['ruta_nombre'] ?? '';
        $this->ruta_descripcion = $ruta['ruta_descripcion'] ?? '';
        $this->ruta_situacion = $ruta['ruta_situacion'] ?? 1;
    }

}