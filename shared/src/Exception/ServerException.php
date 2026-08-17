<?php declare(strict_types=1);

namespace CoreMusic\Exception;

final class ServerException extends BaseCoreMusicException
{
    private function __construct(string $message, string $errorCode)
    {
        parent::__construct($message, $errorCode, 500);
    }

    public static function configError(string $key): self
    {
        return new self("Yapılandırma hatası: {$key} tanımlı değil.", 'CONFIG_ERROR');
    }

    public static function databaseError(string $operation, ?\Throwable $previous = null): self
    {
        return new self("Veritabanı hatası: {$operation}", 'DATABASE_ERROR', 500, $previous);
    }
}
