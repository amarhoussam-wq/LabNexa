<?php
header('Content-Type: application/json');
echo json_encode([
    'php_version' => PHP_VERSION,
    'extensions' => get_loaded_extensions(),
    'gd_info' => extension_loaded('gd') ? gd_info() : 'Not installed',
    'sapi' => PHP_SAPI,
    'env' => $_ENV['APP_ENV'] ?? 'not-set',
    // Removed config() call as this script runs standalone
]);
