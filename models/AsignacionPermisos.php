<?php
// crea nombre de espacio Model
namespace Model;
// Importa la clase ActiveRecord del nombre de espacio Model
use Model\ActiveRecord;
// Crea la clase de instancia AsigPermisos y hereda las funciones de ActiveRecord
class AsigPermisos extends ActiveRecord {
    
    // Crea las propiedades de la clase
    public static $tabla = 'asig_permisos';
    public static $idTabla = 'asignacion_id';
    public static $columnasDB = 
    [
        'asignacion_usuario_id',
        'asignacion_app_id',
        'asignacion_permiso_id',
        'asignacion_fecha',
        'asignacion_usuario_asigno',
        'asignacion_motivo',
        'asignacion_situacion'
    ];
    
    // Crea las variables para almacenar los datos
    public $asignacion_id;
    public $asignacion_usuario_id;
    public $asignacion_app_id;
    public $asignacion_permiso_id;
    public $asignacion_fecha;
    public $asignacion_usuario_asigno;
    public $asignacion_motivo;
    public $asignacion_situacion;
    
    public function __construct($asignacion = [])
    {
        $this->asignacion_id = $asignacion['asignacion_id'] ?? null;
        $this->asignacion_usuario_id = $asignacion['asignacion_usuario_id'] ?? 0;
        $this->asignacion_app_id = $asignacion['asignacion_app_id'] ?? 0;
        $this->asignacion_permiso_id = $asignacion['asignacion_permiso_id'] ?? 0;
        $this->asignacion_fecha = $asignacion['asignacion_fecha'] ?? null;
        $this->asignacion_usuario_asigno = $asignacion['asignacion_usuario_asigno'] ?? 0;
        $this->asignacion_motivo = $asignacion['asignacion_motivo'] ?? '';
        $this->asignacion_situacion = $asignacion['asignacion_situacion'] ?? 1;
    }

}