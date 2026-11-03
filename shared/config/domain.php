<?php declare(strict_types=1);

return [
    'primary'         => 'coremusic.net',
    'primary_port'    => 81,
    'subdomain_port'  => 81,
    'subdomains' => [
        'auth'   => 'auth.coremusic.net',
        'home'   => 'home.coremusic.net',
        'assets' => 'assets.coremusic.net',
        'music'  => 'music.coremusic.net',
        'admin'  => 'admin.coremusic.net',
        'media'  => 'media.coremusic.net',
        'api'    => 'api.coremusic.net',
    ],
    'subdomain_ports' => [
        'home'   => 81,
        'assets' => 81,
        'auth'   => 81,
        'music'  => 81,
        'admin'  => 81,
        'media'  => 81,
        'api'    => 81,
    ],
];
