<?php
// Ejecutar: php database/run_migration_superadmin.php
$mysqli = new mysqli('localhost', 'root', '', 'compraventa');
if ($mysqli->connect_error) {
    die('Error DB: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

$queries = [
    "CREATE TABLE IF NOT EXISTS platform_admins (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(150) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        activo TINYINT(1) NOT NULL DEFAULT 1,
        created_at DATETIME NULL,
        updated_at DATETIME NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
];

$alters = [
    "estado_plan ENUM('prueba','activo','suspendido_mora') NOT NULL DEFAULT 'prueba'",
    "plan_codigo VARCHAR(50) NULL",
    "fin_prueba DATETIME NULL",
    "proximo_cobro DATETIME NULL",
    "suspendido_admin TINYINT(1) NOT NULL DEFAULT 0",
];

foreach ($queries as $q) {
    if (!$mysqli->query($q)) echo "ERR: {$mysqli->error}\n";
    else echo "OK\n";
}

foreach ($alters as $col) {
    $name = explode(' ', $col)[0];
    $r = $mysqli->query("SHOW COLUMNS FROM negocios LIKE '$name'");
    if ($r && $r->num_rows === 0) {
        $sql = "ALTER TABLE negocios ADD COLUMN $col";
        if ($mysqli->query($sql)) echo "Added negocios.$name\n";
        else echo "ERR negocios.$name: {$mysqli->error}\n";
    } else {
        echo "Skip negocios.$name\n";
    }
}

$r = $mysqli->query("SHOW COLUMNS FROM usuarios LIKE 'username'");
if ($r && $r->num_rows === 0) {
    $mysqli->query("ALTER TABLE usuarios ADD COLUMN username VARCHAR(80) NULL AFTER nombre");
    echo "Added usuarios.username\n";
}

$hash = '$2y$10$h6qUm0z0XtK4yTQdwCEgauP7jcwmyRgbMdhQ32018pP1LmtQDphFa';
$mysqli->query("INSERT IGNORE INTO platform_admins (nombre, email, password, activo, created_at, updated_at) VALUES ('Administrador plataforma', 'platform@compraventa.com', '$hash', 1, NOW(), NOW())");
$mysqli->query("UPDATE negocios SET estado_plan = 'activo', suspendido_admin = 0 WHERE slug = 'valery'");
echo "Migration done.\n";
