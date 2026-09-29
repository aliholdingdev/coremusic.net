<?php declare(strict_types=1);

/**
 * CoreMusic API — Autoload Bridge
 *
 * 1. Kendi vendor/autoload.php'yu yükle (varsa)
 * 2. shared vendor/autoload.php'yi de yükle (path repo bağımlılıkları + CoreMusic\*)
 * 3. Hiçbiri yoksa + API sınıfları için manuel PSR-4 fallback
 */

$apiVendor    = __DIR__ . '/vendor/autoload.php';
$sharedVendor = __DIR__ . '/../shared/vendor/autoload.php';

$loaded = false;

// 1) Kendi vendor'ı (composer install/dump-autoload çalıştıysa)
if (file_exists($apiVendor)) {
    require_once $apiVendor;
    $loaded = true;
}

// 2) Shared vendor (CoreMusic\Config, CoreMusic\Log, CoreMusic\Api\* ... için ZORUNLU)
if (file_exists($sharedVendor)) {
    require_once $sharedVendor;
    $loaded = true;
} elseif (!$loaded) {
    http_response_code(503);
    echo 'Composer dependencies missing. Run "composer install" in both shared/ and api.coremusic.net/.';
    exit(1);
}

// 3) API-specific sınıflar: kendi vendor'ı yoksa manuel fallback
if (!file_exists($apiVendor)) {
    spl_autoload_register(function (string $class): void {
        $prefixes = [
            'CoreMusic\\Api\\Domain\\'      => __DIR__ . '/include/Domain/',
            'CoreMusic\\Api\\Middleware\\'  => __DIR__ . '/include/Middleware/',
            'CoreMusic\\Api\\Handler\\'     => __DIR__ . '/include/Handler/',
            'CoreMusic\\Api\\Container\\'   => __DIR__ . '/include/Container/',
            'CoreMusic\\Api\\Controller\\'  => __DIR__ . '/include/Controller/',
            'CoreMusic\\Api\\Service\\'     => __DIR__ . '/include/Service/',
            'CoreMusic\\Api\\Repository\\'  => __DIR__ . '/include/Repository/',
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
}
