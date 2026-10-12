<?php
/**
 * Diagnóstico rápido — subir a public_html y abrir /diagnostico.php
 * BORRAR este archivo cuando el sitio funcione.
 */
header('Content-Type: text/plain; charset=utf-8');

echo "=== ContratoYa diagnóstico ===\n\n";
echo 'PHP: ' . PHP_VERSION . "\n";
echo 'mysqli: ' . (extension_loaded('mysqli') ? 'OK' : 'FALTA') . "\n";
echo 'mbstring: ' . (extension_loaded('mbstring') ? 'OK' : 'FALTA') . "\n";
echo 'intl: ' . (extension_loaded('intl') ? 'OK' : 'FALTA') . "\n\n";

$root = dirname(__DIR__);
echo "Raíz proyecto: $root\n";
echo '.env existe: ' . (is_file($root . '/.env') ? 'SI' : 'NO') . "\n";
echo 'app/: ' . (is_dir($root . '/app') ? 'SI' : 'NO') . "\n";
echo 'writable/: ' . (is_dir($root . '/writable') ? 'SI' : 'NO') . "\n\n";

$writable = $root . '/writable';
$session = $writable . '/session';
echo 'writable escribible: ' . (is_writable($writable) ? 'SI' : 'NO') . "\n";
echo 'session escribible: ' . (is_writable($session) ? 'SI' : 'NO') . "\n\n";

// Leer .env sin mostrar contraseña
$env = [];
if (is_file($root . '/.env')) {
    foreach (file($root . '/.env', FILE_IGNORE_NEW_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;
        [$k, $v] = array_map('trim', explode('=', $line, 2));
        $env[$k] = $v;
    }
}

$host = $env['database.default.hostname'] ?? 'localhost';
$db   = $env['database.default.database'] ?? '';
$user = $env['database.default.username'] ?? '';
$pass = $env['database.default.password'] ?? '';

echo "BD configurada: $db\n";
echo "Usuario BD: $user\n\n";

if ($db && $user) {
    $mysqli = @new mysqli($host, $user, $pass, $db);
    if ($mysqli->connect_error) {
        echo 'MySQL: ERROR - ' . $mysqli->connect_error . "\n";
    } else {
        echo "MySQL: CONECTADO\n";
        $r = $mysqli->query('SHOW TABLES');
        echo 'Tablas: ' . ($r ? $r->num_rows : 0) . "\n";
        $mysqli->close();
    }
} else {
    echo "MySQL: no hay datos en .env\n";
}

echo "\n=== Fin (borra este archivo después) ===\n";
