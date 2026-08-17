<?php declare(strict_types=1);

$json = [
    'status'  => 'ok',
    'service' => 'home.coremusic.net',
    'version' => APP_VERSION ?? '2.0.0',
    'time'    => date('c'),
];

http_response_code(200);
header('Content-Type: application/json; charset=utf-8');
echo json_encode($json, JSON_UNESCAPED_UNICODE);
