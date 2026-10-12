<?php
/**
 * Migración: rol empleado en usuarios
 * Ejecutar: php database/run_migration_empleados.php
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

$sql = "ALTER TABLE usuarios MODIFY rol ENUM('admin', 'empleado', 'operador') NOT NULL DEFAULT 'empleado'";

if ($mysqli->query($sql)) {
    echo "OK: columna rol actualizada (admin, empleado, operador).\n";

    // Migrar operador legacy a empleado si existiera
    $mysqli->query("UPDATE usuarios SET rol = 'empleado' WHERE rol = 'operador'");
    echo "OK: registros operador migrados a empleado si aplicaba.\n";
} else {
    echo "Error: " . $mysqli->error . PHP_EOL;
    exit(1);
}

$mysqli->close();
echo "Migración completada.\n";
