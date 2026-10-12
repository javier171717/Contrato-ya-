<?php
/**
 * Migración: gastos operativos
 * Ejecutar: php database/run_migration_finanzas.php
 */

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'compraventa';

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    die('Error de conexión: ' . $mysqli->connect_error . PHP_EOL);
}
$mysqli->set_charset('utf8mb4');

function tablaExiste(mysqli $mysqli, string $tabla): bool
{
    $tabla = $mysqli->real_escape_string($tabla);
    $res = $mysqli->query("SHOW TABLES LIKE '$tabla'");
    return $res && $res->num_rows > 0;
}

if (!tablaExiste($mysqli, 'gastos')) {
    $mysqli->query("CREATE TABLE gastos (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        negocio_id INT UNSIGNED NOT NULL,
        usuario_id INT UNSIGNED NOT NULL,
        fecha DATE NOT NULL,
        concepto VARCHAR(255) NOT NULL,
        monto DECIMAL(12,2) NOT NULL,
        created_at DATETIME NULL,
        updated_at DATETIME NULL,
        FOREIGN KEY (negocio_id) REFERENCES negocios(id) ON DELETE CASCADE,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
        INDEX idx_gastos_negocio_fecha (negocio_id, fecha)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "OK: tabla gastos creada\n";
} else {
    echo "SKIP: tabla gastos ya existe\n";
}

$mysqli->close();
echo "Migración finanzas completada.\n";
