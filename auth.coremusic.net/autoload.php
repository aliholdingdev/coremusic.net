<?php declare(strict_types=1);

/**
 * CoreMusic Auth — Autoload Bridge
 *
 * 1. Kendi vendor/autoload.php'yu yükle
 * 2. Yoksa shared'den yükle + auth sınıflarını manuel kaydet
 */

$authVendor   = __DIR__ . '/vendor/autoload.php';
$sharedVendor = __DIR__ . '/../shared/vendor/autoload.php';

if (file_exists($authVendor)) {
    require_once $authVendor;
    return;
}

if (file_exists($sharedVendor)) {
    require_once $sharedVendor;

    // Auth-specific sınıfları manuel kaydet (composer install henüz çalışmadıysa)
    spl_autoload_register(function (string $class): void {
        $prefixes = [
            'CoreMusic\\Auth\\Handler\\'     => __DIR__ . '/include/Handler/',
            'CoreMusic\\Auth\\Container\\'   => __DIR__ . '/include/Container/',
            'CoreMusic\\Auth\\Controller\\'  => __DIR__ . '/include/Controller/',
            'CoreMusic\\Auth\\Service\\'     => __DIR__ . '/include/Service/',
            'CoreMusic\\Auth\\Repository\\'  => __DIR__ . '/include/Repository/',
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
echo 'Composer dependencies missing. Run "composer install" in both shared/ and auth.coremusic.net/.';
exit(1);
