<?php

declare(strict_types=1);

namespace Media;

use CoreMusic\Security\UuidV7;

/**
 * MySqlMediaAccessRepository — `media_access` · `media_catalog.asset` · `media_audit`.
 *
 * ADR-002: yalnız PDO prepared statement · `SELECT *` YOK · ORM YOK ·
 * kolon adı asla interpolation (dinamik tanımlayıcı YOK — tüm sorgular sabit).
 *
 * Şema bağlantısı `ConnectionFactory` üzerinden alınır (tek bağlantı kaynağı):
 *   - grant/audit → anahtar `media` (`DB_MEDIA_NAME` → `coremusic_media`,
 *     dump: `.ai/.sql/mysql/coremusic_media.sql`)
 *   - varlık     → anahtar `mediacatalog` (`DB_MEDIACATALOG_NAME` →
 *     `media_catalog`, dump: `.ai/.sql/mysql/media_catalog.sql`);
 *     `DB_MEDIACATALOG_NAME` yoksa anahtar `media`'ye FALLBACK eder.
 *
 * ⚠️ VERIFICATION REQUIRED (raporlandı): `media_catalog` dump'ında `file_key`
 * kolonu YOK — `file_key` = `asset.id` (ULID, ADR-092 kimliği) eşlemesi
 * tasarımdan geliyor; ADR-112'de teyit edilmelidir.
 */
final class MySqlMediaAccessRepository implements MediaAccessRepositoryInterface
{
    /** @param \Closure(string): \PDO $pdoFactory ConnectionFactory::get() ile aynı imza */
    public function __construct(
        private readonly \Closure $pdoFactory,
        private readonly string $mediaDbKey = MediaEnv::MEDIA_DB,
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * `UNHEX(:user_id)` — binary(16) UUIDv7 (tire yok, 32 hex zaten doğrulanmış).
     */
    public function hasReadGrant(string $userIdHex, string $fileKey): bool
    {
        $pdo = ($this->pdoFactory)($this->mediaDbKey);
        $stmt = $pdo->prepare(
            'SELECT id FROM media_access
             WHERE user_id = UNHEX(:user_id)
               AND file_key = :file_key
               AND access_type IN (\'read\', \'admin\')
               AND is_deleted = 0
               AND (expires_at IS NULL OR expires_at > NOW())
             LIMIT 1'
        );
        $stmt->execute(['user_id' => $userIdHex, 'file_key' => $fileKey]);

        return $stmt->fetch(\PDO::FETCH_ASSOC) !== false;
    }

    /**
     * {@inheritdoc}
     *
     * `media_catalog` bağlantısı kurulamazsa `media` anahtarına fallback eder;
     * o şemada `asset` yoksa PDOException yükselir → guard 403'e çevirir
     * (fail-closed: metadata okunamıyorsa erişim verilmez).
     */
    public function findAsset(string $fileKey): ?array
    {
        $pdo = $this->catalogPdo();
        $stmt = $pdo->prepare(
            'SELECT id, dosya_yolu, durum, dosya, format, boyut_bayt, sha256
             FROM asset
             WHERE id = :file_key AND is_deleted = 0
             LIMIT 1'
        );
        $stmt->execute(['file_key' => $fileKey]);
        $satir = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($satir === false) {
            return null;
        }

        return [
            'id' => (string) $satir['id'],
            'dosya_yolu' => (string) $satir['dosya_yolu'],
            'durum' => (string) $satir['durum'],
            'dosya' => (string) $satir['dosya'],
            'format' => (string) $satir['format'],
            'boyut_bayt' => (int) $satir['boyut_bayt'],
            'sha256' => (string) $satir['sha256'],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * Kolonlar dump SSOT (`coremusic_media.sql:181-201`): `created_at` /
     * `updated_at` / `is_deleted` DEFAULT'lu → bilerek yazılmaz.
     * `action` enum'u içinde `download` kullanılır (HTTP GET/HEAD okuması).
     */
    public function recordAudit(
        string $userIdHex,
        string $fileKey,
        string $relativePath,
        int $fileSize,
        int $status,
    ): void {
        $pdo = ($this->pdoFactory)($this->mediaDbKey);
        $stmt = $pdo->prepare(
            'INSERT INTO media_audit
                (id, user_id, action, file_key, file_path, file_size, ip_address, user_agent, metadata)
             VALUES
                (UNHEX(:id), UNHEX(:user_id), :action, :file_key, :file_path, :file_size,
                 :ip_address, :user_agent, :metadata)'
        );
        $stmt->execute([
            'id' => UuidV7::generateHex(),
            'user_id' => $userIdHex,
            'action' => 'download',
            'file_key' => $fileKey,
            'file_path' => $relativePath,
            'file_size' => $fileSize,
            'ip_address' => self::istemciIp(),
            'user_agent' => self::istemciAjan(),
            'metadata' => self::metadataJson($status),
        ]);
    }

    /* ------------------------------------------------------------------ *
     * Bağlantı
     * ------------------------------------------------------------------ */

    /**
     * `media_catalog` bağlantısı; `DB_MEDIACATALOG_NAME` yoksa `media` fallback.
     */
    private function catalogPdo(): \PDO
    {
        $anahtar = MediaEnv::catalogDbKey();
        if ($anahtar === $this->mediaDbKey) {
            return ($this->pdoFactory)($anahtar);
        }

        try {
            return ($this->pdoFactory)($anahtar);
        } catch (\Throwable) {
            // Fallback (tasarım maddesi 3): katalog anahtarı yoksa `media` üzerinden dene.
            return ($this->pdoFactory)($this->mediaDbKey);
        }
    }

    /* ------------------------------------------------------------------ *
     * Denetim alanları
     * ------------------------------------------------------------------ */

    /**
     * İstemci IP'si — yalnız `REMOTE_ADDR` (sahte `X-Forwarded-For` güvenilmez;
     * proxy zinciri yapılandırması Faz 5 kapsamı).
     */
    private static function istemciIp(): string
    {
        $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');

        return filter_var($ip, FILTER_VALIDATE_IP) === false ? '' : $ip;
    }

    /**
     * `user_agent` — `text` kolonu; istemci başına tek satır bölmek için kırpılır.
     */
    private static function istemciAjan(): string
    {
        $ajan = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');

        return mb_substr($ajan, 0, 512);
    }

    /**
     * `metadata` JSON — durum + yol çözme sonucu; secret/cookie ASLA yazılmaz.
     */
    private static function metadataJson(int $status): string
    {
        return (string) json_encode(
            ['status' => $status, 'protokol' => 'http-range'],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }
}