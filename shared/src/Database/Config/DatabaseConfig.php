<?php declare(strict_types=1);

namespace CoreMusic\Database\Config;

final class DatabaseConfig
{
    public function __construct(
        public readonly string $host = 'localhost',
        public readonly string $dbName = 'coremusic',
        public readonly string $user = 'root',
        public readonly string $password = '',
        public readonly int $port = 3306,
        public readonly string $charset = 'utf8mb4',
    ) {}
}
