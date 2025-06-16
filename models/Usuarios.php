<?php

namespace Model;

use Model\ActiveRecord;

class Usuario extends ActiveRecord {
    public static $tabla = 'usuario_login2025';
    public static $idTabla = 'usu_id';
    public static $columnasDB = [
        'usu_nombre',
        'usu_password', 
        'usu_catalogo',
        'usu_situacion'
    ];
    
    public $usu_id;
    public $usu_nombre;
    public $usu_password;
    public $usu_catalogo;
    public $usu_situacion;
    
    public function __construct($usuario = [])
    {
        $this->usu_id = $usuario['usu_id'] ?? null;
        $this->usu_nombre = $usuario['usu_nombre'] ?? '';
        $this->usu_password = $usuario['usu_password'] ?? '';
        $this->usu_catalogo = $usuario['usu_catalogo'] ?? '';
        $this->usu_situacion = $usuario['usu_situacion'] ?? 1;
    }
}