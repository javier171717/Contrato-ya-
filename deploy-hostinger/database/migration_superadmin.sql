-- Migración: Super Admin de plataforma
USE compraventa;

-- Administradores de la plataforma (super admin)
CREATE TABLE IF NOT EXISTS platform_admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Campos de plan y cobros en negocios
ALTER TABLE negocios
    ADD COLUMN IF NOT EXISTS estado_plan ENUM('prueba', 'activo', 'suspendido_mora') NOT NULL DEFAULT 'prueba' AFTER activo,
    ADD COLUMN IF NOT EXISTS plan_codigo VARCHAR(50) NULL AFTER estado_plan,
    ADD COLUMN IF NOT EXISTS fin_prueba DATETIME NULL AFTER plan_codigo,
    ADD COLUMN IF NOT EXISTS proximo_cobro DATETIME NULL AFTER fin_prueba,
    ADD COLUMN IF NOT EXISTS suspendido_admin TINYINT(1) NOT NULL DEFAULT 0 AFTER proximo_cobro;

-- Username opcional para usuarios de negocio
ALTER TABLE usuarios
    ADD COLUMN IF NOT EXISTS username VARCHAR(80) NULL AFTER nombre;

-- Super admin por defecto (contraseña: superadmin123)
INSERT IGNORE INTO platform_admins (nombre, email, password, activo, created_at, updated_at)
VALUES (
    'Administrador plataforma',
    'platform@compraventa.com',
    '$2y$10$h6qUm0z0XtK4yTQdwCEgauP7jcwmyRgbMdhQ32018pP1LmtQDphFa',
    1,
    NOW(),
    NOW()
);

-- Negocio Valery existente: plan activo
UPDATE negocios SET estado_plan = 'activo', suspendido_admin = 0 WHERE slug = 'valery';
