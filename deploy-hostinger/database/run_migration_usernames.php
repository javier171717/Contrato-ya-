<?php
$mysqli = new mysqli('localhost', 'root', '', 'compraventa');
if ($mysqli->connect_error) die('Error DB: ' . $mysqli->connect_error);
$mysqli->set_charset('utf8mb4');

$r = $mysqli->query("SHOW COLUMNS FROM platform_admins LIKE 'username'");
if ($r && $r->num_rows === 0) {
    $mysqli->query("ALTER TABLE platform_admins ADD COLUMN username VARCHAR(80) NULL UNIQUE AFTER nombre");
    echo "Added platform_admins.username\n";
    $mysqli->query("UPDATE platform_admins SET username = 'platform' WHERE email = 'platform@compraventa.com' AND (username IS NULL OR username = '')");
}

$r = $mysqli->query("SHOW INDEX FROM usuarios WHERE Key_name = 'uk_usuarios_username'");
if ($r && $r->num_rows === 0) {
    $mysqli->query("UPDATE usuarios SET username = SUBSTRING_INDEX(email, '@', 1) WHERE username IS NULL OR username = ''");
    $mysqli->query("ALTER TABLE usuarios ADD UNIQUE KEY uk_usuarios_username (username)");
    echo "Added usuarios.username unique\n";
}

echo "Done.\n";
