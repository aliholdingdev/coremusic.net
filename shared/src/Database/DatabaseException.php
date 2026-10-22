<?php declare(strict_types=1);

namespace CoreMusic\Database;

use CoreMusic\Exception\BaseCoreMusicException;

/**
 * DatabaseException — PDO bağlantı katmanından yükselen kontrollü domain exception.
 *
 * Kural (Adım 03): PDOException doğrudan yukarı sıçramaz; sarmalanır.
 *  - Exception mesajı ASLA DSN / host / parola içermez: DSN yeri [REDACTED].
 *  - Orijinal PDOException $previous olarak korunur (stack correlation).
 *  - Log yazımı FileHandler üzerinden yapılır; FileHandler satırları zaten
 *    Redactor'dan geçirir (password/token/secret → [REDACTED]).
 *
 * ADR-002 (raw PDO, ORM yasak) · ADR-022 (hardened security).
 */
final class DatabaseException extends BaseCoreMusicException
{
    private const REDACTED = '[REDACTED]';

    private function __construct(string $message, string $errorCode, ?\Throwable $previous = null)
    {
        parent::__construct($message, $errorCode, 500, $previous);
    }

    /**
     * PDO bağlantı kurulumu başarısız (sunucu yok / auth reddi / bilinmeyen DB).
     * Mesajda DSN maskeli; ayrıntı $previous PDOException içinde taşınır.
     */
    public static function connectionFailed(string $dbKey, ?\Throwable $previous = null): self
    {
        return new self(
            sprintf("Veritabanı bağlantısı başarısız: '%s' (DSN: %s)", $dbKey, self::REDACTED),
            'DB_CONNECTION_FAILED',
            $previous
        );
    }

    /**
     * Ortam yapılandırması eksik — fail-safe: bağlantı AÇILMAZ, boş string ile devam edilmez.
     * Örn: DB_AUTH_NAME tanımsızken 'auth' bağlantısı istenmiş.
     */
    public static function configMissing(string $dbKey, string $envKey): self
    {
        return new self(
            sprintf("Veritabanı yapılandırması eksik: '%s' için %s tanımlı değil.", $dbKey, $envKey),
            'DB_CONFIG_MISSING'
        );
    }

    /**
     * Registry'den beklenmeyen manager tipi döndü (iç tutarlılık ihlali).
     * DatabaseRegistry::registerMySql yalnız DatabaseManager kaydeder — bu hata
     * yalnız bozuk/kopya registry implementasyonunda tetiklenir.
     */
    public static function unexpectedManager(string $dbKey, string $actualType): self
    {
        return new self(
            sprintf(
                "Beklenmeyen veritabanı manager tipi: '%s' (beklenen: DatabaseManager, gelen: %s)",
                $dbKey,
                $actualType
            ),
            'DB_REGISTRY_TYPE_MISMATCH'
        );
    }
}
