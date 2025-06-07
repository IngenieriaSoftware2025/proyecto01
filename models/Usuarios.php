<?php
// crea nombre de espacio Model
namespace Model;
// Importa la clase ActiveRecord del nombre de espacio Model
use Model\ActiveRecord;
// Crea la clase de instancia Usuario y hereda las funciones de ActiveRecord
class Usuarios extends ActiveRecord {
    
    // Crea las propiedades de la clase
    public static $tabla = 'usuario';
    public static $idTabla = 'usuario_id';
    public static $columnasDB = 
    [
        'usuario_nom1',
        'usuario_nom2',
        'usuario_ape1',
        'usuario_ape2',
        'usuario_tel',
        'usuario_direc',
        'usuario_dpi',
        'usuario_correo',
        'usuario_contra',
        'usuario_token',
        'usuario_fecha_creacion',
        'usuario_fecha_contra',
        'usuario_fotografia',
        'usuario_situacion'
    ];
    
    // Crea las variables para almacenar los datos
    public $usuario_id;
    public $usuario_nom1;
    public $usuario_nom2;
    public $usuario_ape1;
    public $usuario_ape2;
    public $usuario_tel;
    public $usuario_direc;
    public $usuario_dpi;
    public $usuario_correo;
    public $usuario_contra;
    public $usuario_token;
    public $usuario_fecha_creacion;
    public $usuario_fecha_contra;
    public $usuario_fotografia;
    public $usuario_situacion;
    
    public function __construct($usuario = [])
    {
        $this->usuario_id = $usuario['usuario_id'] ?? null;
        $this->usuario_nom1 = $usuario['usuario_nom1'] ?? '';
        $this->usuario_nom2 = $usuario['usuario_nom2'] ?? '';
        $this->usuario_ape1 = $usuario['usuario_ape1'] ?? '';
        $this->usuario_ape2 = $usuario['usuario_ape2'] ?? '';
        $this->usuario_tel = $usuario['usuario_tel'] ?? 0;
        $this->usuario_direc = $usuario['usuario_direc'] ?? '';
        $this->usuario_dpi = $usuario['usuario_dpi'] ?? '';
        $this->usuario_correo = $usuario['usuario_correo'] ?? '';
        $this->usuario_contra = $usuario['usuario_contra'] ?? '';
        $this->usuario_token = $usuario['usuario_token'] ?? '';
        $this->usuario_fecha_creacion = $usuario['usuario_fecha_creacion'] ?? '';
        $this->usuario_fecha_contra = $usuario['usuario_fecha_contra'] ?? '';
        $this->usuario_fotografia = $usuario['usuario_fotografia'] ?? null;
        $this->usuario_situacion = $usuario['usuario_situacion'] ?? 1;
    }

}