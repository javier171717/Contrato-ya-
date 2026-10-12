<?php
/**
 * Repara carpetas writable — abrir una vez: /fix-writable.php
 * BORRAR después de usar.
 */
header('Content-Type: text/plain; charset=utf-8');

$root = dirname(__DIR__);
$dirs = ['cache', 'logs', 'session', 'uploads'];

echo "=== Reparar writable ===\n\n";
echo "Raíz: $root\n\n";

foreach ($dirs as $dir) {
    $path = $root . '/writable/' . $dir;
    if (! is_dir($path)) {
        $ok = @mkdir($path, 0777, true);
        echo "$dir: creada " . ($ok ? 'OK' : 'FALLO') . "\n";
    } else {
        echo "$dir: ya existe\n";
    }
    @chmod($path, 0777);

    $test = $path . '/test_' . time() . '.txt';
    $write = @file_put_contents($test, 'ok');
    if ($write !== false) {
        @unlink($test);
        echo "  -> escribir: OK\n";
    } else {
        echo "  -> escribir: FALLO (permisos)\n";
    }
}

echo "\nListo. Prueba https://contratoya.online/ y borra este archivo.\n";
