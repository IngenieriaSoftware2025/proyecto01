create database proyecto01

CREATE TABLE usuario(
usuario_id SERIAL PRIMARY KEY,
usuario_nom1 VARCHAR (50) NOT NULL,
usuario_nom2 VARCHAR (50) NOT NULL,
usuario_ape1 VARCHAR (50) NOT NULL,
usuario_ape2 VARCHAR (50) NOT NULL,
usuario_tel INT NOT NULL, 
usuario_direc VARCHAR (150) NOT NULL,
usuario_dpi VARCHAR (13) NOT NULL,
usuario_correo VARCHAR (100) NOT NULL,
usuario_contra LVARCHAR (1056) NOT NULL,
usuario_token LVARCHAR (1056) NOT NULL,
usuario_fecha_creacion DATE DEFAULT TODAY,
usuario_fecha_contra DATE DEFAULT TODAY,
usuario_fotografia LVARCHAR (2056),
usuario_situacion SMALLINT DEFAULT 1
);

CREATE TABLE aplicacion(
app_id SERIAL PRIMARY KEY,
app_nombre_largo VARCHAR (250) NOT NULL,
app_nombre_medium VARCHAR (150) NOT NULL,
app_nombre_corto VARCHAR (50) NOT NULL,
app_fecha_creacion DATE DEFAULT TODAY,
app_situacion SMALLINT DEFAULT 1
);

CREATE TABLE permiso(
permiso_id SERIAL PRIMARY KEY, 
permiso_app_id INT NOT NULL,
permiso_nombre VARCHAR (150) NOT NULL,
permiso_clave VARCHAR (250) NOT NULL,
permiso_desc VARCHAR (250) NOT NULL,
permiso_fecha DATE DEFAULT TODAY,
permiso_situacion SMALLINT DEFAULT 1,
FOREIGN KEY (permiso_app_id) REFERENCES aplicacion(app_id) 
);

CREATE TABLE asig_permisos(
asignacion_id SERIAL PRIMARY KEY,
asignacion_usuario_id INT NOT NULL,
asignacion_app_id INT NOT NULL,
asignacion_permiso_id INT NOT NULL,
asignacion_fecha DATE DEFAULT TODAY,
asignacion_usuario_asigno INT NOT NULL,
asignacion_motivo VARCHAR (250) NOT NULL,
asignacion_situacion SMALLINT DEFAULT 1,
FOREIGN KEY (asignacion_usuario_id) REFERENCES usuario(usuario_id),
FOREIGN KEY (asignacion_app_id) REFERENCES aplicacion(app_id),
FOREIGN KEY (asignacion_permiso_id) REFERENCES permiso(permiso_id)
);

CREATE TABLE historial_act(
historial_id SERIAL PRIMARY KEY,
historial_usuario_id INT NOT NULL,
historial_fecha DATETIME YEAR TO MINUTE,
historial_ruta INT NOT NULL,
historial_ejecucion LVARCHAR (1056) NOT NULL,
historial_situacion SMALLINT DEFAULT 1,
FOREIGN KEY (historial_usuario_id) REFERENCES usuario(usuario_id),
FOREIGN KEY (historial_ruta) REFERENCES rutas(ruta_id)
);

CREATE TABLE rol(
rol_id SERIAL PRIMARY KEY,
rol_nombre VARCHAR(75),
rol_nombre_ct VARCHAR(25),
rol_app INTEGER,
rol_situacion SMALLINT DEFAULT 1,
FOREIGN KEY (rol_app) REFERENCES aplicacion(app_id)
);

CREATE TABLE rutas(
ruta_id SERIAL PRIMARY KEY,
ruta_app_id INT NOT NULL,
ruta_nombre LVARCHAR (1056) NOT NULL,
ruta_descripcion VARCHAR (250) NOT NULL,
ruta_situacion SMALLINT DEFAULT 1,
FOREIGN KEY (ruta_app_id) REFERENCES aplicacion(app_id)
);

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

-----------NUEVAS TABLAS-----------------
-----------------------------------------
-----------------------------------------

CREATE TABLE ventas (
    id SERIAL PRIMARY KEY,
    cliente_id INTEGER NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    fecha_venta DATE DEFAULT TODAY,
    estado VARCHAR(20) DEFAULT 'COMPLETADA',
    usuario_id INT,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

CREATE TABLE ventas_detalle (
    id SERIAL PRIMARY KEY,
    venta_id INTEGER NOT NULL,
    inventario_id INTEGER NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (venta_id) REFERENCES ventas(id),
    FOREIGN KEY (inventario_id) REFERENCES inventario(id)
);

CREATE TABLE inventario_config (
    id SERIAL PRIMARY KEY,
    inventario_id INTEGER NOT NULL,
    stock_minimo INT DEFAULT 5,
    alerta_enviada SMALLINT DEFAULT 0,
    fecha_creacion DATE DEFAULT TODAY,
    FOREIGN KEY (inventario_id) REFERENCES inventario(id)
);

CREATE TABLE movimientos_inventario (
    id SERIAL PRIMARY KEY,
    inventario_id INTEGER NOT NULL,
    tipo_movimiento VARCHAR(20) NOT NULL,
    cantidad INT NOT NULL,
    motivo VARCHAR(250),
    usuario_id INT,
    fecha_movimiento DATETIME YEAR TO MINUTE,
    FOREIGN KEY (inventario_id) REFERENCES inventario(id)
);