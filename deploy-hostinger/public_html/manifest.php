<?php
declare(strict_types=1);

header('Content-Type: application/manifest+json; charset=utf-8');
header('Cache-Control: no-cache');

$scheme = 'http';
if (
    (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
    || (! empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
) {
    $scheme = 'https';
}

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
$basePath = ($scriptDir === '/' || $scriptDir === '') ? '/' : rtrim($scriptDir, '/') . '/';

echo json_encode([
    'name' => 'ContratoYa',
    'short_name' => 'ContratoYa',
    'description' => 'Sistema de contratos con pacto de retroventa para compraventas',
    'start_url' => $basePath,
    'scope' => $basePath,
    'display' => 'standalone',
    'orientation' => 'portrait',
    'background_color' => '#1a5f2a',
    'theme_color' => '#1a5f2a',
    'lang' => 'es-CO',
    'icons' => [
        [
            'src' => $basePath . 'icons/icon-192.png',
            'sizes' => '192x192',
            'type' => 'image/png',
            'purpose' => 'any maskable',
        ],
        [
            'src' => $basePath . 'icons/icon-512.png',
            'sizes' => '512x512',
            'type' => 'image/png',
            'purpose' => 'any maskable',
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
