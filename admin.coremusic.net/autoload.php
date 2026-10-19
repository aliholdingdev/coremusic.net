<?php declare(strict_types=1);

/**
 * CoreMusic Admin — Autoload Bridge
 *
 * 1. Kendi vendor/autoload.php'yu yükle
 * 2. Yoksa shared'den yükle + admin sınıflarını manuel kaydet
 */

$adminVendor  = __DIR__ . '/vendor/autoload.php';
$sharedVendor = __DIR__ . '/../shared/vendor/autoload.php';

if (file_exists($adminVendor)) {
    require_once $adminVendor;
    return;
}

if (file_exists($sharedVendor)) {
    require_once $sharedVendor;

    // Admin-specific sınıfları manuel kaydet (composer install henüz çalışmadıysa)
    spl_autoload_register(function (string $class): void {
        $prefixes = [
            'CoreMusic\\Admin\\Auth\\'        => __DIR__ . '/include/Auth/',
            'CoreMusic\\Admin\\Container\\'   => __DIR__ . '/include/Container/',
            'CoreMusic\\Admin\\Controller\\'  => __DIR__ . '/include/Controller/',
            'CoreMusic\\Admin\\Middleware\\'  => __DIR__ . '/include/Middleware/',
            'CoreMusic\\Admin\\Repository\\'  => __DIR__ . '/include/Repository/',
            'CoreMusic\\Admin\\Service\\'     => __DIR__ . '/include/Service/',
        ];
        foreach ($prefixes as $prefix => $baseDir) {
            if (str_starts_with($class, $prefix)) {
                $relativeClass = substr($class, strlen($prefix));
                $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
        }
    });
    return;
}

http_response_code(503);
echo 'Composer dependencies missing. Run "composer install" in both shared/ and admin.coremusic.net/.';
exit(1);
