<?php
// Servește fișierele statice existente
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $file = __DIR__ . $path;

    if (is_file($file)) {
        return false;
    }
}

// Redirecționează totul către aplicație (ex: src/index.php)
require __DIR__ . '/src/login.php';
