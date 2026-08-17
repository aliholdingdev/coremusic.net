<?php declare(strict_types=1);

$composerPath = __DIR__ . '/../shared/vendor/autoload.php';
if (file_exists($composerPath)) {
    require_once $composerPath;
} else {
    http_response_code(503);
    echo 'Composer dependencies missing. Run "composer install".';
    exit(1);
}
