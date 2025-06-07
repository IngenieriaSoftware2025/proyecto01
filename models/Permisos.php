<?php
// crea nombre de espacio Model
namespace Model;
// Importa la clase ActiveRecord del nombre de espacio Model
use Model\ActiveRecord;
// Crea la clase de instancia Permiso y hereda las funciones de ActiveRecord
class Permisos extends ActiveRecord {
    
    // Crea las propiedades de la clase
    public static $tabla = 'permiso';
    public static $idTabla = 'permiso_id';
    public static $columnasDB = 
    [
        'permiso_app_id',
        'permiso_nombre',
        'permiso_clave',
        'permiso_desc',
        'permiso_fecha',
        'permiso_situacion'
    ];
    
    // Crea las variables para almacenar los datos
    public $permiso_id;
    public $permiso_app_id;
    public $permiso_nombre;
    public $permiso_clave;
    public $permiso_desc;
    public $permiso_fecha;
    public $permiso_situacion;
    
    public function __construct($permiso = [])
    {
        $this->permiso_id = $permiso['permiso_id'] ?? null;
        $this->permiso_app_id = $permiso['permiso_app_id'] ?? 0;
        $this->permiso_nombre = $permiso['permiso_nombre'] ?? '';
        $this->permiso_clave = $permiso['permiso_clave'] ?? '';
        $this->permiso_desc = $permiso['permiso_desc'] ?? '';
        $this->permiso_fecha = $permiso['permiso_fecha'] ?? null;
        $this->permiso_situacion = $permiso['permiso_situacion'] ?? 1;
    }

}