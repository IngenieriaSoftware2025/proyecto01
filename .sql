create database proyecto01

----------------------------------------------------
---------TABLAS CREADAS FUNCIONALES-----------------
----------------------------------------------------
CREATE TABLE clientes(
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    telefono VARCHAR(8) NOT NULL,
    nit VARCHAR(15),
    correo VARCHAR(150),
    situacion SMALLINT DEFAULT 1
);

CREATE TABLE marcas(
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(250),
    modelo VARCHAR(100),
    fecha_creacion DATE DEFAULT TODAY,
    situacion SMALLINT DEFAULT 1
);

CREATE TABLE inventario (
    id SERIAL PRIMARY KEY,
    marca_id INTEGER NOT NULL,
    numero_serie VARCHAR(50),
    precio_compra DECIMAL(10,2) NOT NULL,
    precio_venta DECIMAL(10,2) NOT NULL,
    stock_disponible INT DEFAULT 1,
    estado_dispositivo VARCHAR(20) DEFAULT 'NUEVO',
    estado_inventario VARCHAR(20) DEFAULT 'DISPONIBLE',
    fecha_ingreso DATE DEFAULT TODAY,
    observaciones VARCHAR(500),
    situacion SMALLINT DEFAULT 1,
    FOREIGN KEY (marca_id) REFERENCES marcas(id)
);

CREATE TABLE usuario_login2025 (
    usu_id SERIAL PRIMARY KEY,
    usu_nombre VARCHAR(50),
    usu_codigo INTEGER,
    usu_password VARCHAR(150),
    usu_situacion SMALLINT DEFAULT 1
); 
INSERT INTO usuario_login2025 (usu_nombre, usu_codigo, usu_password) VALUES('HERBERTH GUZMAN', 649103, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT INTO usuario_login2025 (usu_nombre, usu_codigo, usu_password) VALUES('ANDREA MASELLA', 649104, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT INTO usuario_login2025 (usu_nombre, usu_codigo, usu_password) VALUES('GABRIELA MASELLA', 649105, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
--Código: 649103 - Contraseña: password
--Código: 649104 - Contraseña: password
--Código: 649105 - Contraseña: password

SELECT DISTINCT usu_codigo FROM usuario_login2025 WHERE usu_situacion = 1;

CREATE TABLE rol_login2025 (
    rol_id SERIAL PRIMARY KEY,
    rol_nombre VARCHAR(75),
    rol_nombre_ct VARCHAR(25),
    rol_situacion SMALLINT DEFAULT 1
);
INSERT INTO rol_login2025 (rol_nombre, rol_nombre_ct) VALUES ('ADMINISTRADOR', 'ADMIN');
INSERT INTO rol_login2025 (rol_nombre, rol_nombre_ct) VALUES ('USUARIO', 'USER');

select * from asig_permisos
CREATE TABLE permiso_login2025 (
    permiso_id SERIAL PRIMARY KEY,
    permiso_usuario INTEGER,
    permiso_rol INTEGER,
    permiso_situacion SMALLINT DEFAULT 1,
    FOREIGN KEY (permiso_usuario) REFERENCES usuario_login2025 (usu_id),
    FOREIGN KEY (permiso_rol) REFERENCES rol_login2025 (rol_id)
);
INSERT INTO permiso_login2025 (permiso_usuario, permiso_rol) VALUES (1,1);
INSERT INTO permiso_login2025 (permiso_usuario, permiso_rol) VALUES (2,2);
INSERT INTO permiso_login2025 (permiso_usuario, permiso_rol) VALUES (3,1);

CREATE TABLE ventas (
    venta_id SERIAL PRIMARY KEY,
    cliente_id INTEGER NOT NULL,
    usuario_id INTEGER NOT NULL, 
    total DECIMAL(10,2) NOT NULL,
    fecha_venta DATE DEFAULT TODAY,
    estado VARCHAR(20) DEFAULT 'COMPLETADA',
    observaciones LVARCHAR(500),
    situacion SMALLINT DEFAULT 1,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (usuario_id) REFERENCES usuario_login2025(usu_id)
);

ALTER TABLE ventas ADD (tipo_transaccion VARCHAR(20) DEFAULT 'VENTA');
ALTER TABLE ventas ADD (reparacion_id INTEGER);

ALTER TABLE ventas ADD CONSTRAINT 
(FOREIGN KEY (reparacion_id) REFERENCES reparaciones CONSTRAINT fk_ventas_reparacion);

CREATE TABLE venta_detalle (
    detalle_id SERIAL PRIMARY KEY,
    venta_id INTEGER NOT NULL,
    inventario_id INTEGER NOT NULL,
    cantidad INTEGER NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (venta_id) REFERENCES ventas(venta_id),
    FOREIGN KEY (inventario_id) REFERENCES inventario(id)
);

-- Tabla de tipos de servicio/reparación
CREATE TABLE tipos_servicio (
    tipo_id SERIAL PRIMARY KEY,
    tipo_nombre VARCHAR(100) NOT NULL,
    tipo_descripcion VARCHAR(250),
    precio_base DECIMAL(10,2) DEFAULT 0.00,
    tiempo_estimado INTEGER DEFAULT 1, -- días estimados
    situacion SMALLINT DEFAULT 1
);

-- Tabla principal de reparaciones
CREATE TABLE reparaciones (
    reparacion_id SERIAL PRIMARY KEY,
    cliente_id INTEGER NOT NULL,
    dispositivo_marca VARCHAR(100) NOT NULL,
    dispositivo_modelo VARCHAR(100) NOT NULL,
    dispositivo_serie VARCHAR(50),
    dispositivo_imei VARCHAR(20),
    problema_reportado LVARCHAR(500) NOT NULL,
    diagnostico LVARCHAR(500),
    solucion_aplicada LVARCHAR(500),
    tipo_servicio_id INTEGER,
    tecnico_asignado INTEGER,
    estado VARCHAR(20) DEFAULT 'RECIBIDO',
    fecha_ingreso DATE DEFAULT TODAY,
    fecha_diagnostico DATE,
    fecha_finalizacion DATE,
    fecha_entrega DATE,
    presupuesto_inicial DECIMAL(10,2) DEFAULT 0.00,
    costo_final DECIMAL(10,2) DEFAULT 0.00,
    anticipo DECIMAL(10,2) DEFAULT 0.00,
    observaciones LVARCHAR(500),
    situacion SMALLINT DEFAULT 1,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (tipo_servicio_id) REFERENCES tipos_servicio(tipo_id),
    FOREIGN KEY (tecnico_asignado) REFERENCES usuario_login2025(usu_id)
);

-- Tabla para historial de estados de reparación
CREATE TABLE reparacion_historial (
    historial_id SERIAL PRIMARY KEY,
    reparacion_id INTEGER NOT NULL,
    estado_anterior VARCHAR(20),
    estado_nuevo VARCHAR(20) NOT NULL,
    usuario_cambio INTEGER NOT NULL,
    fecha_cambio DATETIME YEAR TO MINUTE DEFAULT CURRENT YEAR TO MINUTE,
    observaciones VARCHAR(250),
    FOREIGN KEY (reparacion_id) REFERENCES reparaciones(reparacion_id),
    FOREIGN KEY (usuario_cambio) REFERENCES usuario_login2025(usu_id)
);

-- Insertar tipos de servicio básicos
INSERT INTO tipos_servicio (tipo_nombre, tipo_descripcion, precio_base, tiempo_estimado) VALUES ('Reparación de Pantalla', 'Cambio de pantalla LCD/OLED', 150.00, 1);
INSERT INTO tipos_servicio (tipo_nombre, tipo_descripcion, precio_base, tiempo_estimado) VALUES ('Cambio de Batería', 'Reemplazo de batería', 80.00, 1);
INSERT INTO tipos_servicio (tipo_nombre, tipo_descripcion, precio_base, tiempo_estimado) VALUES ('Reparación de Placa', 'Soldadura y reparación de componentes', 200.00, 3);
INSERT INTO tipos_servicio (tipo_nombre, tipo_descripcion, precio_base, tiempo_estimado) VALUES ('Liberación', 'Liberación de operador', 50.00, 1);
INSERT INTO tipos_servicio (tipo_nombre, tipo_descripcion, precio_base, tiempo_estimado) VALUES ('Actualización Software', 'Flash y actualización de firmware', 75.00, 1);
INSERT INTO tipos_servicio (tipo_nombre, tipo_descripcion, precio_base, tiempo_estimado) VALUES ('Reparación de Cámara', 'Cambio de cámara principal o frontal', 120.00, 2);
INSERT INTO tipos_servicio (tipo_nombre, tipo_descripcion, precio_base, tiempo_estimado) VALUES ('Reparación de Audio', 'Cambio de altavoz o micrófono', 90.00, 1);
INSERT INTO tipos_servicio (tipo_nombre, tipo_descripcion, precio_base, tiempo_estimado) VALUES  ('Limpieza por Líquidos', 'Limpieza por daño con líquidos', 100.00, 2);-- Tabla de tipos de servicio/reparación

CREATE TABLE historial_actividades (
    historial_id SERIAL PRIMARY KEY,
    historial_usuario_id INTEGER NOT NULL,
    historial_modulo VARCHAR(50) NOT NULL,
    historial_accion VARCHAR(50) NOT NULL,
    historial_tabla_afectada VARCHAR(50),
    historial_registro_id INTEGER,
    historial_descripcion LVARCHAR(500),
    historial_datos_anteriores LVARCHAR(1000),
    historial_datos_nuevos LVARCHAR(1000),
    historial_ip VARCHAR(45),
    historial_fecha DATETIME YEAR TO MINUTE DEFAULT CURRENT YEAR TO MINUTE,
    historial_situacion SMALLINT DEFAULT 1,
    FOREIGN KEY (historial_usuario_id) REFERENCES usuario_login2025(usu_id)
);

CREATE TABLE modulos_sistema (
    modulo_id SERIAL PRIMARY KEY,
    modulo_nombre VARCHAR(50) NOT NULL,
    modulo_descripcion VARCHAR(200),
    modulo_activo SMALLINT DEFAULT 1
);

INSERT INTO modulos_sistema (modulo_nombre, modulo_descripcion) VALUES ('CLIENTES', 'Gestión de clientes del sistema'),
INSERT INTO modulos_sistema (modulo_nombre, modulo_descripcion) VALUES ('MARCAS', 'Administración de marcas y modelos'),
INSERT INTO modulos_sistema (modulo_nombre, modulo_descripcion) VALUES ('INVENTARIO', 'Control de inventario de dispositivos'),
INSERT INTO modulos_sistema (modulo_nombre, modulo_descripcion) VALUES ('VENTAS', 'Proceso de ventas y facturación'),
INSERT INTO modulos_sistema (modulo_nombre, modulo_descripcion) VALUES ('REPARACIONES', 'Gestión de reparaciones de dispositivos'),
INSERT INTO modulos_sistema (modulo_nombre, modulo_descripcion) VALUES ('USUARIOS', 'Administración de usuarios del sistema'),
INSERT INTO modulos_sistema (modulo_nombre, modulo_descripcion) VALUES ('LOGIN', 'Accesos al sistema'),
INSERT INTO modulos_sistema (modulo_nombre, modulo_descripcion) VALUES ('REPORTES', 'Generación de reportes'),
INSERT INTO modulos_sistema (modulo_nombre, modulo_descripcion) VALUES ('CONFIGURACION', 'Configuración del sistema');