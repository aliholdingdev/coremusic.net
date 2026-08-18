<?php declare(strict_types=1);

/**
 * CoreMusic Home — Autoload Bridge
 *
 * 1. Kendi vendor/autoload.php'yu yükle (shared dependency dahil)
 * 2. Yoksa shared'den yükle + home sınıflarını manuel kaydet
 */

$homeVendor  = __DIR__ . '/vendor/autoload.php';
$sharedVendor = __DIR__ . '/../shared/vendor/autoload.php';

if (file_exists($homeVendor)) {
    require_once $homeVendor;
    return;
}

if (file_exists($sharedVendor)) {
    require_once $sharedVendor;

    // Home-specific sınıfları manuel kaydet
    spl_autoload_register(function (string $class): void {
        $prefixes = [
            'CoreMusic\\Home\\Session\\'   => __DIR__ . '/include/Session/',
            'CoreMusic\\Home\\Auth\\'      => __DIR__ . '/include/Auth/',
            'CoreMusic\\Home\\Container\\' => __DIR__ . '/include/Container/',
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
echo 'Composer dependencies missing. Run "composer install" in both shared/ and home.coremusic.net/.';
exit(1);
