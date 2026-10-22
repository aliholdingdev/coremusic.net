<?php declare(strict_types=1);

/**
 * CoreMusic API v2 — Bootstrap (minimal, ayrı giriş).
 *
 * v1 index.php / config/routes.php / include/** DOSYALARINA 0 SATIR DOKUNUR.
 * Bu dosya yalnızca:
 *   1) v2 namespace autoload'unu ÖNCE kaydeder (composer'dan önce çalışır),
 *   2) v1 autoload bridge'i'ni (shared vendor + CoreMusic\*) salt-okunur yükler,
 *   3) v1 config/constants.php'yi (APP_*, DB_*, SESSION_*) salt-okunur include eder.
 */

// 1) v2 namespace — composer'dan ÖNCE kaydedilir ki CoreMusic\Api\V2\* kendi
//    dizininden çözülsün (composer CoreMusic\Api\ → include/ dener, bulamaz,
//    sonra bu autoloader çalışır).
spl_autoload_register(static function (string $class): void {
    $prefix = 'CoreMusic\\Api\\V2\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $file = __DIR__ . '/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

// 2) v1 autoload bridge (salt-okunur) — shared vendor + CoreMusic\Auth\ + v1 Api\*.
require_once dirname(__DIR__) . '/autoload.php';

// 3) v1 constants (salt-okunur) — APP_ENV_MODE/DEBUG/DB_*/SESSION_* tanımları + .env fallback.
require_once dirname(__DIR__) . '/config/constants.php';
