CREATE TABLE mccp_perfil (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    estado CHAR(1) DEFAULT '1',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO mccp_perfil(nombre, estado, created_at, updated_at) VALUES ('Técnico', 1, now(), now());

CREATE TABLE mccp_modulo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    estado CHAR(1) DEFAULT '1',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO mccp_modulo (nombre,estado,created_at,updated_at) VALUES ('Dashboard',1,now(),now());
INSERT INTO mccp_modulo (nombre,estado,created_at,updated_at) VALUES ('Contratos',1,now(),now());
INSERT INTO mccp_modulo (nombre,estado,created_at,updated_at) VALUES ('Tickets',1,now(),now());
INSERT INTO mccp_modulo (nombre,estado,created_at,updated_at) VALUES ('Clientes',1,now(),now());
INSERT INTO mccp_modulo (nombre,estado,created_at,updated_at) VALUES ('Proveedores',1,now(),now());
INSERT INTO mccp_modulo (nombre,estado,created_at,updated_at) VALUES ('Usuarios',1,now(),now());
INSERT INTO mccp_modulo (nombre,estado,created_at,updated_at) VALUES ('Perfiles',1,now(),now());
INSERT INTO mccp_modulo (nombre,estado,created_at,updated_at) VALUES ('Reportes',1,now(),now());

CREATE TABLE mccp_perfil_modulo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    perfil_id INT NOT NULL,
    modulo_id INT NOT NULL,
    puede_ver BOOLEAN DEFAULT TRUE,
    puede_crear BOOLEAN DEFAULT FALSE,
    puede_editar BOOLEAN DEFAULT FALSE,
    puede_eliminar BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (perfil_id) REFERENCES mccp_perfil(id) ON DELETE CASCADE,
    FOREIGN KEY (modulo_id) REFERENCES mccp_modulo(id) ON DELETE CASCADE,
    UNIQUE KEY unique_rol_modulo (perfil_id, modulo_id)
);

INSERT INTO `mccp_perfil_modulo` (`perfil_id`, `modulo_id`, `puede_ver`, `puede_crear`, `puede_editar`, `puede_eliminar`, `created_at`) VALUES ('1', '4', '1', '0', '1', '0', now());

CREATE TABLE mccp_usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol_usuario CHAR(1) DEFAULT '1',
    foto_perfil VARCHAR(255),
    numero_contacto VARCHAR(20),
    email VARCHAR(100),
    estado CHAR(1) DEFAULT '1',
    ultimo_acceso TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO `mccp_usuario` (`id`, `nombres`, `apellidos`, `usuario`, `password`, `rol_usuario`, `foto_perfil`, `numero_contacto`, `email`, `estado`, `ultimo_acceso`, `created_at`, `updated_at`) VALUES (NULL, 'Jean Flabio', 'Moreno Curay', 'flabio.moreno', '$2y$10$lKv6v5m0JzlglYEWLZc5nORmbPi/QmrOre89F1G488pCvEkwr5cv.', '1', 'default.png', '934880237', 'jflabiomorenoc@gmail.com', '1', NULL, current_timestamp(), current_timestamp());

CREATE TABLE mccp_usuario_perfil (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    perfil_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- Evita que el mismo usuario tenga el mismo perfil más de una vez
    UNIQUE KEY unique_usuario_perfil (usuario_id, perfil_id),
    -- Claves foráneas
    FOREIGN KEY (usuario_id) REFERENCES mccp_usuario(id) ON DELETE CASCADE,
    FOREIGN KEY (perfil_id) REFERENCES mccp_perfil(id) ON DELETE CASCADE
);


CREATE TABLE mccp_proveedor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_ruc CHAR(1) DEFAULT '1' NOT NULL,
    ruc VARCHAR(11) NOT NULL UNIQUE,
    razon_social VARCHAR(100),
    nombre_cliente VARCHAR(100),
    apellido_paterno VARCHAR(50),
    apellido_materno VARCHAR(50),
    direccion VARCHAR(255),
    telefono VARCHAR(20),
    email VARCHAR(100),
    contacto VARCHAR(100),
    estado CHAR(1) DEFAULT '1',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE mccp_cliente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_ruc CHAR(1) DEFAULT '1' NOT NULL,
    ruc VARCHAR(11) NOT NULL UNIQUE,
    razon_social VARCHAR(100),
    nombre_cliente VARCHAR(100),
    apellido_paterno VARCHAR(50),
    apellido_materno VARCHAR(50),
    contacto_principal VARCHAR(100),
    cargo_contacto VARCHAR(100),
    email_contacto VARCHAR(100),
    telefono_contacto VARCHAR(20),
    contacto_1 VARCHAR(100),
    telefono_contacto_1 VARCHAR(20),
    contacto_2 VARCHAR(100),
    telefono_contacto_2 VARCHAR(20),
    estado CHAR(1) DEFAULT '1',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE mccp_direccion_cliente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    distrito VARCHAR(100),
    provincia VARCHAR(100),
    departamento VARCHAR(100),
    es_principal BOOLEAN DEFAULT FALSE,
    referencia VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES mccp_cliente(id) ON DELETE CASCADE
);

CREATE TABLE mccp_equipo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(100) NOT NULL,
    modelo VARCHAR(100) NOT NULL,
    numero_serie VARCHAR(100) NOT NULL UNIQUE,
    tipo_equipo ENUM('bn', 'color') DEFAULT 'bn',
    condicion ENUM('nuevo', 'seminuevo', 'usado') DEFAULT 'nuevo',
    estado ENUM('activo', 'inactivo', 'mantenimiento', 'alquiler') DEFAULT 'activo',
    proveedor_id INT,
    fecha_compra DATE,
    costo_dolares DECIMAL(10,2),
    costo_soles DECIMAL(10,2),
    contador_inicial_bn INT DEFAULT 0,
    contador_inicial_color INT DEFAULT 0,
    contador_actual_bn INT DEFAULT 0,
    contador_actual_color INT DEFAULT 0,
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (proveedor_id) REFERENCES mccp_proveedor(id)
);

CREATE TABLE mccp_contrato_alquiler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_contrato VARCHAR(20) NOT NULL UNIQUE,
    cliente_id INT NOT NULL,
    direccion_id INT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_culminacion DATE NOT NULL,
    estado ENUM('vigente', 'finalizado', 'cancelado') DEFAULT 'vigente',
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES mccp_cliente(id),
    FOREIGN KEY (direccion_id) REFERENCES mccp_direccion_cliente(id)
);

CREATE TABLE mccp_contrato_equipo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contrato_id INT NOT NULL,
    equipo_id INT NOT NULL,
    ip_equipo VARCHAR(15),
    area_ubicacion VARCHAR(100),
    contador_inicial_bn INT DEFAULT 0,
    contador_inicial_color INT DEFAULT 0,
    contador_final_bn INT,
    contador_final_color INT,
    estado ENUM('vigente', 'finalizado', 'cancelado') DEFAULT 'vigente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (contrato_id) REFERENCES mccp_contrato_alquiler(id) ON DELETE CASCADE,
    FOREIGN KEY (equipo_id) REFERENCES mccp_equipo(id) ON DELETE RESTRICT,
    UNIQUE KEY (contrato_id, equipo_id)
);

CREATE TABLE mccp_incidencia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_ticket VARCHAR(20) NOT NULL UNIQUE,
    fecha_incidencia DATETIME NOT NULL,
    descripcion_problema TEXT NOT NULL,
    cliente_id INT NOT NULL,
    contrato_id INT,
    equipo_id INT,
    tecnico_id INT,                                   
    fecha_atencion DATETIME,
    contador_final_bn INT,
    contador_final_color INT,
    tiempo_atencion INT,
    canal_reporte ENUM('whatsapp','llamada','email','presencial') DEFAULT 'whatsapp',
    prioridad ENUM('baja','media','alta','critica') DEFAULT 'media',
    estado ENUM('pendiente','en_proceso','resuelto','cerrado','cancelado') DEFAULT 'pendiente',
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES mccp_cliente(id),
    FOREIGN KEY (contrato_id) REFERENCES mccp_contrato_alquiler(id),
    FOREIGN KEY (equipo_id) REFERENCES mccp_equipo(id),
    FOREIGN KEY (tecnico_id) REFERENCES mccp_usuario(id)
);

CREATE TABLE mccp_incidencia_comentario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    incidencia_id INT NOT NULL,
    usuario_id INT NOT NULL,          
    comentario TEXT NOT NULL,
    tipo ENUM('nota','accion','cierre') DEFAULT 'accion',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (incidencia_id) REFERENCES mccp_incidencia(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES mccp_usuario(id) ON DELETE CASCADE
);

CREATE TABLE mccp_incidencia_foto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comentario_id INT NOT NULL,
    nombre_archivo VARCHAR(255) NOT NULL,
    ruta_archivo VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (comentario_id) REFERENCES mccp_incidencia_comentario(id) ON DELETE CASCADE
);