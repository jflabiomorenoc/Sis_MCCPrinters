CREATE TABLE mccp_perfil (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    estado CHAR(1) DEFAULT '1',
    predefinido TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO mccp_perfil(nombre, estado, predefinido, created_at, updated_at) VALUES ('CLIENTE', 1, 1, now(), now());
INSERT INTO mccp_perfil(nombre, estado, predefinido, created_at, updated_at) VALUES ('TÉCNICO', 1, 1, now(), now());

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
INSERT INTO mccp_modulo (nombre,estado,created_at,updated_at) VALUES ('Equipos',1,now(),now());
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

INSERT INTO mccp_perfil_modulo (perfil_id, modulo_id, puede_ver, puede_crear, puede_editar, puede_eliminar, created_at) VALUES ('1', '1', '1', '0', '0', '0', now());
INSERT INTO mccp_perfil_modulo (perfil_id, modulo_id, puede_ver, puede_crear, puede_editar, puede_eliminar, created_at) VALUES ('1', '2', '1', '0', '0', '0', now());
INSERT INTO mccp_perfil_modulo (perfil_id, modulo_id, puede_ver, puede_crear, puede_editar, puede_eliminar, created_at) VALUES ('1', '3', '1', '0', '0', '0', now());
INSERT INTO mccp_perfil_modulo (perfil_id, modulo_id, puede_ver, puede_crear, puede_editar, puede_eliminar, created_at) VALUES ('1', '4', '0', '0', '0', '0', now());
INSERT INTO mccp_perfil_modulo (perfil_id, modulo_id, puede_ver, puede_crear, puede_editar, puede_eliminar, created_at) VALUES ('1', '5', '0', '0', '0', '0', now());
INSERT INTO mccp_perfil_modulo (perfil_id, modulo_id, puede_ver, puede_crear, puede_editar, puede_eliminar, created_at) VALUES ('1', '6', '0', '0', '0', '0', now());
INSERT INTO mccp_perfil_modulo (perfil_id, modulo_id, puede_ver, puede_crear, puede_editar, puede_eliminar, created_at) VALUES ('1', '7', '0', '0', '0', '0', now());
INSERT INTO mccp_perfil_modulo (perfil_id, modulo_id, puede_ver, puede_crear, puede_editar, puede_eliminar, created_at) VALUES ('1', '8', '0', '0', '0', '0', now());

INSERT INTO mccp_perfil_modulo (perfil_id, modulo_id, puede_ver, puede_crear, puede_editar, puede_eliminar, created_at) VALUES ('2', '1', '1', '0', '0', '0', now());
INSERT INTO mccp_perfil_modulo (perfil_id, modulo_id, puede_ver, puede_crear, puede_editar, puede_eliminar, created_at) VALUES ('2', '2', '1', '0', '0', '0', now());
INSERT INTO mccp_perfil_modulo (perfil_id, modulo_id, puede_ver, puede_crear, puede_editar, puede_eliminar, created_at) VALUES ('2', '3', '1', '1', '1', '1', now());
INSERT INTO mccp_perfil_modulo (perfil_id, modulo_id, puede_ver, puede_crear, puede_editar, puede_eliminar, created_at) VALUES ('2', '4', '0', '0', '0', '0', now());
INSERT INTO mccp_perfil_modulo (perfil_id, modulo_id, puede_ver, puede_crear, puede_editar, puede_eliminar, created_at) VALUES ('2', '5', '0', '0', '0', '0', now());
INSERT INTO mccp_perfil_modulo (perfil_id, modulo_id, puede_ver, puede_crear, puede_editar, puede_eliminar, created_at) VALUES ('2', '6', '0', '0', '0', '0', now());
INSERT INTO mccp_perfil_modulo (perfil_id, modulo_id, puede_ver, puede_crear, puede_editar, puede_eliminar, created_at) VALUES ('2', '7', '0', '0', '0', '0', now());
INSERT INTO mccp_perfil_modulo (perfil_id, modulo_id, puede_ver, puede_crear, puede_editar, puede_eliminar, created_at) VALUES ('2', '8', '0', '0', '0', '0', now());

CREATE TABLE mccp_cliente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_ruc CHAR(1) DEFAULT '1' NOT NULL,
    ruc VARCHAR(11) NOT NULL UNIQUE,
    razon_social VARCHAR(100),
    nombre_cliente VARCHAR(100),
    apellido_paterno VARCHAR(50),
    apellido_materno VARCHAR(50),
    estado CHAR(1) DEFAULT '1',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE mccp_usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol_usuario CHAR(1) DEFAULT '1',
    cliente_id INT,
    foto_perfil VARCHAR(255),
    numero_contacto VARCHAR(20),
    email VARCHAR(100),
    estado CHAR(1) DEFAULT '1',
    ultimo_acceso TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES mccp_cliente(id)
);

INSERT INTO `mccp_usuario` (`id`, `nombres`, `apellidos`, `usuario`, `password`, `rol_usuario`, `foto_perfil`, `numero_contacto`, `email`, `estado`, `ultimo_acceso`, `created_at`, `updated_at`) VALUES (NULL, 'Administrador', 'Pruebas', 'admin.prueba', '$2y$10$lKv6v5m0JzlglYEWLZc5nORmbPi/QmrOre89F1G488pCvEkwr5cv.', '1', 'default.png', '999999999', 'admin.prueba@gmail.com', '1', NULL, current_timestamp(), current_timestamp());

CREATE TABLE mccp_usuario_perfil (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    perfil_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
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
    nombre_proveedor VARCHAR(100),
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

CREATE TABLE mccp_direccion_cliente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    distrito VARCHAR(100),
    provincia VARCHAR(100),
    departamento VARCHAR(100),
    referencia VARCHAR(255),
    es_principal TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES mccp_cliente(id) ON DELETE CASCADE
);

CREATE TABLE mccp_contacto_direccion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    direccion_id INT NOT NULL,
    nombre_contacto VARCHAR(100) NOT NULL,
    cargo_contacto VARCHAR(100),
    email_contacto VARCHAR(100),
    telefono_contacto VARCHAR(20),
    fecha_cumple DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (direccion_id) REFERENCES mccp_direccion_cliente(id) ON DELETE CASCADE
);

CREATE TABLE mccp_equipo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(100) NOT NULL,
    modelo VARCHAR(100) NOT NULL,
    numero_serie VARCHAR(100) NOT NULL UNIQUE,
    tipo_equipo ENUM('bn', 'color') DEFAULT 'bn',
    condicion ENUM('nuevo', 'seminuevo', 'usado') DEFAULT 'nuevo',
    estado ENUM('activo', 'inactivo', 'mantenimiento', 'asignado') DEFAULT 'activo',
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
    fecha_inicio DATE NOT NULL,
    fecha_culminacion DATE,
    estado ENUM('pendiente', 'vigente', 'finalizado', 'cancelado') DEFAULT 'pendiente',
    tecnico_id INT,     
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES mccp_cliente(id),
    FOREIGN KEY (tecnico_id) REFERENCES mccp_usuario(id)
);

CREATE TABLE mccp_contrato_equipo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contrato_id INT NOT NULL,
    direccion_id INT NOT NULL,
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
    FOREIGN KEY (direccion_id) REFERENCES mccp_direccion_cliente(id),
    FOREIGN KEY (equipo_id) REFERENCES mccp_equipo(id) ON DELETE RESTRICT,
    UNIQUE KEY (contrato_id, equipo_id)
);

CREATE TABLE mccp_incidencia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_ticket VARCHAR(20) NOT NULL UNIQUE,
    fecha_incidencia DATETIME NOT NULL,
    tipo_incidencia ENUM('correctivo', 'preventivo') NOT NULL, 
    descripcion_problema TEXT NOT NULL,
    cliente_id INT NOT NULL,
    contrato_id INT,
    equipo_id INT,
    tecnico_id INT,                                   
    fecha_atencion DATETIME,
    contador_final_bn INT,
    contador_final_color INT,
    tiempo_atencion DECIMAL(10,2),
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
    tipo_archivo VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (comentario_id) REFERENCES mccp_incidencia_comentario(id) ON DELETE CASCADE
);

--TRIGGERS DE BASE DE DATOS

/*
    EDITAR USUARIO
*/

DELIMITER $$
CREATE TRIGGER mccp_usuario_upd 
BEFORE UPDATE ON mccp_usuario 
FOR EACH ROW 
BEGIN    
    -- Si cambia de rol 2 a rol 1, asegurar que cliente_id sea NULL
    IF OLD.rol_usuario = 2 AND NEW.rol_usuario = 1 THEN
        SET NEW.cliente_id = NULL;
    END IF;
END$$
DELIMITER ;

/*
    ELIMINAR USUARIO
*/

DELIMITER $$
CREATE TRIGGER mccp_usuario_perfil_del 
AFTER DELETE ON mccp_usuario_perfil 
FOR EACH ROW 
BEGIN    
    IF OLD.perfil_id = 1 THEN
        UPDATE mccp_usuario 
        SET cliente_id = NULL 
        WHERE id = OLD.usuario_id
          AND cliente_id IS NOT NULL;
    END IF;
END$$
DELIMITER ;

/*
    INSERTAR CONTRATO
*/

DELIMITER $$
CREATE TRIGGER mccp_contrato_alquiler_ins
BEFORE INSERT ON mccp_contrato_alquiler
FOR EACH ROW
BEGIN
    DECLARE ultimo_numero INT DEFAULT 0;
    DECLARE anio_actual VARCHAR(4);
    DECLARE mes_actual VARCHAR(2);
    DECLARE prefijo VARCHAR(10);
    DECLARE nuevo_numero VARCHAR(20);
    
    -- Obtener año y mes actual
    SET anio_actual = YEAR(CURDATE());
    SET mes_actual = LPAD(MONTH(CURDATE()), 2, '0');
    
    -- Crear prefijo: C + AÑO + MES
    SET prefijo = CONCAT('C', anio_actual, mes_actual, '-');
    
    -- Obtener el último número del mes actual (SIN COLLATE)
    SELECT COALESCE(MAX(CAST(SUBSTRING(numero_contrato, -4) AS UNSIGNED)), 0)
    INTO ultimo_numero
    FROM mccp_contrato_alquiler
    WHERE numero_contrato LIKE CONCAT(prefijo, '%');
    
    -- Incrementar el número
    SET ultimo_numero = ultimo_numero + 1;
    
    -- Generar el nuevo número de contrato
    SET nuevo_numero = CONCAT(prefijo, LPAD(ultimo_numero, 4, '0'));
    
    -- Asignar el número generado
    SET NEW.numero_contrato = nuevo_numero;
END$$
DELIMITER ;

/*
    EDITAR CONTRATO ALQUILER
*/

DELIMITER $$

CREATE TRIGGER mccp_contrato_alquiler_upd
BEFORE UPDATE ON mccp_contrato_alquiler
FOR EACH ROW
BEGIN
    -- Si el estado cambió a 'finalizado' o 'cancelado'
    IF NEW.estado IN ('finalizado', 'cancelado') AND OLD.estado != NEW.estado THEN
        -- Actualizar todos los equipos del contrato al mismo estado
        UPDATE mccp_contrato_equipo
        SET estado = NEW.estado
        WHERE contrato_id = NEW.id;
    END IF;
END$$

DELIMITER ;

/*
    INSERTAR EQUIPO EN CONTRATO
*/

DELIMITER $$

CREATE TRIGGER mccp_contrato_equipo_ins
AFTER INSERT ON mccp_contrato_equipo
FOR EACH ROW
BEGIN
    CALL mccp_contrato_equipo_asignar(NEW.contrato_id, NEW.equipo_id);
END$$

DELIMITER ;

/*
    UPDATE EQUIPO EN CONTRATO
*/

DELIMITER $$

CREATE TRIGGER mccp_contrato_equipo_upd
AFTER UPDATE ON mccp_contrato_equipo
FOR EACH ROW
BEGIN
    -- Si el equipo cambió
    IF OLD.equipo_id != NEW.equipo_id THEN
        -- Cambiar el equipo anterior a 'activo'
        UPDATE mccp_equipo 
        SET estado = 'activo' 
        WHERE id = OLD.equipo_id;
        
        -- Cambiar el nuevo equipo a 'asignado'
        UPDATE mccp_equipo 
        SET estado = 'asignado' 
        WHERE id = NEW.equipo_id;
    END IF;
    
    -- Si el estado del contrato_equipo cambió a 'finalizado' o 'cancelado'
    IF NEW.estado IN ('finalizado', 'cancelado') AND OLD.estado != NEW.estado THEN
        -- Cambiar el equipo a 'activo'
        UPDATE mccp_equipo
        SET estado = 'activo'
        WHERE id = NEW.equipo_id;
    END IF;
END$$

DELIMITER ;

/*
    DELETE EQUIPO EN CONTRATO
*/

DELIMITER $$

CREATE TRIGGER mccp_contrato_equipo_del
BEFORE DELETE ON mccp_contrato_equipo
FOR EACH ROW
BEGIN
    DECLARE equipos_restantes INT;
    
    -- Cambiar equipo eliminado a 'activo'
    UPDATE mccp_equipo 
    SET estado = 'activo' 
    WHERE id = OLD.equipo_id;
    
    -- Contar cuántos equipos quedarán después de eliminar este
    -- Restamos 1 porque aún incluye el registro que se va a eliminar
    SELECT COUNT(*) - 1 INTO equipos_restantes
    FROM mccp_contrato_equipo
    WHERE contrato_id = OLD.contrato_id;
    
    -- Si después de eliminar no quedan equipos, cambiar contrato a 'pendiente'
    IF equipos_restantes = 0 THEN
        UPDATE mccp_contrato_alquiler
        SET estado = 'pendiente'
        WHERE id = OLD.contrato_id;
    END IF;
END$$

DELIMITER ;


/*
    INSERTAR INCIDENCIA
*/

DELIMITER $$
CREATE TRIGGER mccp_incidencia_ins
BEFORE INSERT ON mccp_incidencia
FOR EACH ROW
BEGIN
    DECLARE ultimo_numero INT DEFAULT 0;
    DECLARE anio_actual VARCHAR(4);
    DECLARE mes_actual VARCHAR(2);
    DECLARE prefijo VARCHAR(10);
    DECLARE nuevo_numero VARCHAR(20);
    
    -- Obtener año y mes actual
    SET anio_actual = YEAR(CURDATE());
    SET mes_actual = LPAD(MONTH(CURDATE()), 2, '0');
    
    -- Crear prefijo: I + AÑO + MES (I de Incidencia)
    SET prefijo = CONCAT('I', anio_actual, mes_actual, '-');
    
    -- Obtener el último número del mes actual
    SELECT COALESCE(MAX(CAST(SUBSTRING(numero_ticket, -4) AS UNSIGNED)), 0)
    INTO ultimo_numero
    FROM mccp_incidencia
    WHERE numero_ticket LIKE CONCAT(prefijo, '%');
    
    -- Incrementar el número
    SET ultimo_numero = ultimo_numero + 1;
    
    -- Generar el nuevo número de incidencia
    SET nuevo_numero = CONCAT(prefijo, LPAD(ultimo_numero, 4, '0'));
    
    -- Asignar el número generado
    SET NEW.numero_ticket = nuevo_numero;
END$$
DELIMITER ;



-- PROCEDURE

/*
    INSERTAR PERFIL EN USUARIO
*/

DELIMITER $$

CREATE PROCEDURE mccp_asignar_perfil_usuario(
    IN p_usuario_id INT,
    IN p_perfil_id INT
)
BEGIN
    -- Verificar si el perfil ya existe para evitar duplicados
    IF NOT EXISTS (
        SELECT 1 FROM mccp_usuario_perfil 
        WHERE usuario_id = p_usuario_id AND perfil_id = p_perfil_id
    ) THEN
        
        IF p_perfil_id = 1 THEN
            -- Perfil CLIENTE: eliminar TODOS los perfiles
            DELETE FROM mccp_usuario_perfil WHERE usuario_id = p_usuario_id;
        ELSE
            -- Otro perfil: eliminar solo CLIENTE si existe
            DELETE FROM mccp_usuario_perfil WHERE usuario_id = p_usuario_id AND perfil_id = 1;
        END IF;
        
        -- Insertar el nuevo perfil
        INSERT INTO mccp_usuario_perfil (usuario_id, perfil_id, created_at) 
        VALUES (p_usuario_id, p_perfil_id, NOW());
    END IF;
END$$

DELIMITER ;

/*
    ACTUALIZAR ESTADO DE CONTRATO Y ESTADO DE EQUIPO
*/
DELIMITER $$

CREATE PROCEDURE mccp_contrato_equipo_asignar(IN p_contrato_id INT, IN p_equipo_id INT)
BEGIN
    DECLARE fecha_inicio DATE;
    DECLARE estado_actual VARCHAR(20);
    DECLARE total_equipos INT;
    DECLARE contrato_existe INT;
    
    -- Actualizar equipo a 'asignado'
    UPDATE mccp_equipo 
    SET estado = 'asignado' 
    WHERE id = p_equipo_id;
    
    -- Contar equipos del contrato
    SELECT COUNT(*) INTO total_equipos
    FROM mccp_contrato_equipo
    WHERE contrato_id = p_contrato_id;
    
    -- Si es el primer equipo
    IF total_equipos = 1 THEN
        -- Verificar que el contrato existe
        SELECT COUNT(*) INTO contrato_existe
        FROM mccp_contrato_alquiler
        WHERE id = p_contrato_id;
        
        IF contrato_existe = 1 THEN
            -- Obtener datos del contrato de forma segura
            SELECT COALESCE(fecha_inicio, CURDATE()), COALESCE(estado, 'pendiente')
            INTO fecha_inicio, estado_actual
            FROM mccp_contrato_alquiler
            WHERE id = p_contrato_id;
            
            -- Si está pendiente y la fecha ya pasó, actualizar a vigente
            IF estado_actual = 'pendiente' AND fecha_inicio <= CURDATE() THEN
                UPDATE mccp_contrato_alquiler
                SET estado = 'vigente'
                WHERE id = p_contrato_id;
            END IF;
        END IF;
    END IF;
END$$

DELIMITER ;