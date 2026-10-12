<?php
/**
 * Migración: abonos, saldo capital e interés mensual
 * Ejecutar: php database/run_migration_abonos.php
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

function columnaExiste(mysqli $mysqli, string $tabla, string $columna): bool
{
    $tabla = $mysqli->real_escape_string($tabla);
    $columna = $mysqli->real_escape_string($columna);
    $res = $mysqli->query("SHOW COLUMNS FROM `$tabla` LIKE '$columna'");
    return $res && $res->num_rows > 0;
}

function tablaExiste(mysqli $mysqli, string $tabla): bool
{
    $tabla = $mysqli->real_escape_string($tabla);
    $res = $mysqli->query("SHOW TABLES LIKE '$tabla'");
    return $res && $res->num_rows > 0;
}

if (!columnaExiste($mysqli, 'negocios', 'tasa_interes_mensual')) {
    $mysqli->query("ALTER TABLE negocios ADD COLUMN tasa_interes_mensual DECIMAL(5,2) NOT NULL DEFAULT 10.00 AFTER dias_plazo_default");
    echo "OK: tasa_interes_mensual en negocios\n";
} else {
    echo "SKIP: tasa_interes_mensual ya existe\n";
}

if (!columnaExiste($mysqli, 'contratos', 'saldo_capital')) {
    $mysqli->query("ALTER TABLE contratos ADD COLUMN saldo_capital DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER monto");
    echo "OK: saldo_capital en contratos\n";
} else {
    echo "SKIP: saldo_capital ya existe\n";
}

if (!columnaExiste($mysqli, 'contratos', 'fecha_ultimo_movimiento')) {
    $mysqli->query("ALTER TABLE contratos ADD COLUMN fecha_ultimo_movimiento DATE NULL AFTER fecha_terminacion");
    echo "OK: fecha_ultimo_movimiento en contratos\n";
} else {
    echo "SKIP: fecha_ultimo_movimiento ya existe\n";
}

$mysqli->query("UPDATE contratos SET saldo_capital = monto WHERE saldo_capital = 0");
$mysqli->query("UPDATE contratos SET fecha_ultimo_movimiento = fecha_inicio WHERE fecha_ultimo_movimiento IS NULL");
echo "OK: datos existentes actualizados\n";

if (!tablaExiste($mysqli, 'abonos')) {
    $mysqli->query("CREATE TABLE abonos (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        negocio_id INT UNSIGNED NOT NULL,
        contrato_id INT UNSIGNED NOT NULL,
        usuario_id INT UNSIGNED NOT NULL,
        monto DECIMAL(12,2) NOT NULL,
        interes_cobrado DECIMAL(12,2) NOT NULL DEFAULT 0,
        capital_abonado DECIMAL(12,2) NOT NULL DEFAULT 0,
        interes_capitalizado DECIMAL(12,2) NOT NULL DEFAULT 0,
        saldo_capital_anterior DECIMAL(12,2) NOT NULL,
        saldo_capital_nuevo DECIMAL(12,2) NOT NULL,
        total_adeudado_antes DECIMAL(12,2) NOT NULL,
        saldo_total_nuevo DECIMAL(12,2) NOT NULL,
        dias_periodo INT UNSIGNED NOT NULL DEFAULT 0,
        tasa_mensual DECIMAL(5,2) NOT NULL DEFAULT 10.00,
        fecha_periodo_desde DATE NOT NULL,
        fecha_abono DATE NOT NULL,
        fecha_terminacion_anterior DATE NOT NULL,
        fecha_terminacion_nueva DATE NOT NULL,
        notas VARCHAR(255) NULL,
        created_at DATETIME NULL,
        updated_at DATETIME NULL,
        FOREIGN KEY (negocio_id) REFERENCES negocios(id) ON DELETE CASCADE,
        FOREIGN KEY (contrato_id) REFERENCES contratos(id) ON DELETE CASCADE,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
        INDEX idx_abonos_contrato (contrato_id),
        INDEX idx_abonos_fecha (fecha_abono)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "OK: tabla abonos creada\n";
} else {
    echo "SKIP: tabla abonos ya existe\n";
}

$mysqli->close();
echo "Migración de abonos completada.\n";
