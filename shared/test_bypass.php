<?php declare(strict_types=1);
require __DIR__ . '/vendor/autoload.php';

use CoreMusic\Config\ConfigManager;
use CoreMusic\Security\SecurityHelper;
use CoreMusic\Middleware\BypassAuthMiddleware;

ini_set('session.use_cookies', '0');
session_start();
$_SESSION = [];

$config = new ConfigManager(['app' => ['env' => 'development', 'test_mode' => 'true']]);
echo 'Step 1: isTestBypassActive = ' . var_export(SecurityHelper::isTestBypassActive($config), true) . PHP_EOL;

try {
    $middleware = new BypassAuthMiddleware($config);
    echo 'Step 2: Middleware created' . PHP_EOL;

    $request = ['method' => 'GET', 'uri' => '/home'];
    $result = $middleware->handle($request, function (array $req) {
        echo 'Step 4: Core called, _auth = ' . var_export($req['_auth'] ?? 'NOT SET', true) . PHP_EOL;
        return ['status' => 200];
    });
    echo 'Step 3: Middleware returned' . PHP_EOL;
    echo 'Step 5: Request _auth = ' . var_export($request['_auth'] ?? 'NOT SET', true) . PHP_EOL;
} catch (\Throwable $e) {
    echo 'EXCEPTION: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL;
}
