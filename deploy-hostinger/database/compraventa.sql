-- Sistema PWA Multinegocios - Compraventas con Pacto de Retroventa
-- Ejecutar en MySQL/MariaDB (phpMyAdmin o línea de comandos)

CREATE DATABASE IF NOT EXISTS compraventa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE compraventa;

-- Negocios (multi-tenant)
CREATE TABLE IF NOT EXISTS negocios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    nit VARCHAR(30) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    telefono VARCHAR(50) NOT NULL,
    ciudad VARCHAR(100) NOT NULL,
    departamento VARCHAR(100) NOT NULL DEFAULT 'Antioquia',
    logo VARCHAR(255) NULL,
    representante_nombre VARCHAR(150) NULL,
    representante_cc VARCHAR(20) NULL,
    dias_plazo_default INT UNSIGNED NOT NULL DEFAULT 90,
    ultimo_numero INT UNSIGNED NOT NULL DEFAULT 0,
    texto_legal TEXT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuarios del sistema
CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    negocio_id INT UNSIGNED NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'empleado', 'operador') NOT NULL DEFAULT 'empleado',
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    FOREIGN KEY (negocio_id) REFERENCES negocios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Clientes / vendedores
CREATE TABLE IF NOT EXISTS clientes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    negocio_id INT UNSIGNED NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    cedula VARCHAR(20) NOT NULL,
    telefono VARCHAR(50) NULL,
    direccion VARCHAR(255) NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    FOREIGN KEY (negocio_id) REFERENCES negocios(id) ON DELETE CASCADE,
    UNIQUE KEY uk_cliente_negocio_cedula (negocio_id, cedula)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contratos de compraventa con pacto de retroventa
CREATE TABLE IF NOT EXISTS contratos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    negocio_id INT UNSIGNED NOT NULL,
    cliente_id INT UNSIGNED NOT NULL,
    usuario_id INT UNSIGNED NOT NULL,
    numero INT UNSIGNED NOT NULL,
    monto DECIMAL(12,2) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_terminacion DATE NOT NULL,
    descripcion_articulo TEXT NOT NULL,
    estado ENUM('vigente', 'renovado', 'redimido', 'vencido', 'vendido') NOT NULL DEFAULT 'vigente',
    observaciones TEXT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    FOREIGN KEY (negocio_id) REFERENCES negocios(id) ON DELETE CASCADE,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    UNIQUE KEY uk_contrato_negocio_numero (negocio_id, numero)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Texto legal por defecto (Art. 1939 Código Civil Colombiano)
SET @texto_legal = 'Declaro bajo la gravedad del juramento que soy el propietario exclusivo de los bienes objeto de este contrato, que los mismos son de procedencia lícita y que no están sujetos a gravamen, embargo ni limitación alguna de dominio.

El presente contrato se celebra con sujeción al Pacto de Retroventa consagrado en el Artículo 1939 del Código Civil Colombiano, en virtud del cual me reservo el derecho de recuperar los bienes vendidos, reembolsando al comprador la suma de dinero que por ellos se me haya entregado.

El comprador queda exonerado de toda responsabilidad por el deterioro, pérdida, hurto, incendio o falta de funcionamiento de los bienes vendidos, así como por cualquier caso fortuito o fuerza mayor.

El derecho de retroventa es personalísimo e intransferible, no pudiendo cederse a terceros bajo ninguna circunstancia.

En caso de no ejercer la acción de retroventa dentro del plazo pactado, la venta se tendrá por pura y simple, quedando el comprador facultado para disponer libremente de los bienes.';

-- Datos semilla: Compraventa Valery
INSERT INTO negocios (nombre, slug, nit, direccion, telefono, ciudad, departamento, representante_nombre, dias_plazo_default, ultimo_numero, texto_legal, activo, created_at, updated_at)
VALUES (
    'Compraventa Valery',
    'valery',
    '322.932.927-7',
    'Calle 87 No. 97-46 Calle Principal del Barrio el Bosque',
    '321 856 1163',
    'Chigorodó',
    'Antioquia',
    'Compraventa Valery',
    90,
    836,
    @texto_legal,
    1,
    NOW(),
    NOW()
);

-- Usuario administrador (contraseña: admin123)
INSERT INTO usuarios (negocio_id, nombre, email, password, rol, activo, created_at, updated_at)
VALUES (
    1,
    'Administrador',
    'admin@valery.com',
    '$2y$10$h6qUm0z0XtK4yTQdwCEgauP7jcwmyRgbMdhQ32018pP1LmtQDphFa',
    'admin',
    1,
    NOW(),
    NOW()
);
