<?php declare(strict_types=1);

/**
 * CoreMusic — Dev Host Router
 *
 * Tek bir `php -S 127.0.0.1:80` sunucusu için Host-bazlı docroot seçimi.
 * HTTP_HOST'a göre alt alan docroot'u seçilir; var olan dosya serve edilir,
 * yoksa ilgili alt alanın index.php'sine chdir + require edilir.
 * Bilinmeyen host → 404 JSON.
 *
 * Kullanım:
 *   C:\Php858\php.exe -S 127.0.0.1:80 dev/router.php
 */

$host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
if (str_contains($host, ':')) {
    $host = explode(':', $host, 2)[0];
}

$repoRoot = dirname(__DIR__);

$docroots = [
    'api.coremusic.net'    => $repoRoot . '/api.coremusic.net',
    'auth.coremusic.net'   => $repoRoot . '/auth.coremusic.net',
    'assets.coremusic.net' => $repoRoot . '/assets.coremusic.net',
    'home.coremusic.net'   => $repoRoot . '/home.coremusic.net',
];

$failJson = static function (string $code, string $message, int $status): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    echo json_encode(
        ['error' => ['code' => $code, 'message' => $message]],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
};

$docroot = $docroots[$host] ?? null;
if ($docroot === null) {
    $failJson('NOT_FOUND', 'Bilinmeyen host: ' . $host, 404);
}
if (!is_dir($docroot)) {
    $failJson('NOT_FOUND', 'Docroot bulunamadı: ' . $host, 404);
}

$path = (string)parse_url((string)($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
if ($path === '') {
    $path = '/';
}

// Yasaklı yollar: config/include/vendor/tests + .env
if (
    preg_match('#^/(config|include|tests|vendor)(/|$)#', $path) === 1
    || preg_match('#(^|/)\.env#i', $path) === 1
) {
    $failJson('FORBIDDEN', 'Erişim reddedildi.', 403);
}

// Var olan statik dosya → doğrudan serve
$file = $docroot . $path;
if ($path !== '/' && is_file($file)) {
    $isPhp = str_ends_with(strtolower($file), '.php');
    if (!$isPhp) {
        $mime = function_exists('mime_content_type') ? (mime_content_type($file) ?: '') : '';
        if ($mime === '') {
            $ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $mime = match ($ext) {
                'css'  => 'text/css',
                'js'   => 'application/javascript',
                'json' => 'application/json',
                'html' => 'text/html',
                'svg'  => 'image/svg+xml',
                'png'  => 'image/png',
                'jpg', 'jpeg' => 'image/jpeg',
                'gif'  => 'image/gif',
                'ico'  => 'image/x-icon',
                'woff' => 'font/woff',
                'woff2'=> 'font/woff2',
                default => 'application/octet-stream',
            };
        }
        header('Content-Type: ' . $mime);
        header('X-Content-Type-Options: nosniff');
        readfile($file);
        exit;
    }
    // index.php dışındaki PHP dosyalarına doğrudan erişim yok
    if (basename($file) !== 'index.php') {
        $failJson('NOT_FOUND', 'Uç bulunamadı.', 404);
    }
}

// Front controller'a yönlendir
$frontController = $docroot . '/index.php';
if (!is_file($frontController)) {
    $failJson('NOT_FOUND', 'Front controller yok: ' . $host, 404);
}

chdir($docroot);
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $frontController;
require $frontController;
