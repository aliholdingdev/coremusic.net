<?php declare(strict_types=1);

namespace CoreMusic\Repository;

use CoreMusic\Contracts\Database\IDatabaseManager;
use CoreMusic\Contracts\Database\IDatabaseRegistry;
use CoreMusic\Security\UuidV7;

/**
 * ApiKey Repository — `coremusic_auth.api_keys` (SSOT, ADR-020 §2.2-1)
 *
 * Şema (canlı, migration tamam):
 *   id binary(16) PK | user_id binary(16) NOT NULL | key_hash UNIQUE |
 *   key_prefix varchar(8) | key_name | scopes JSON |
 *   key_type ENUM('user','service','server') | rate_limit int DEFAULT 1000 |
 *   is_active | last_used_at | expires_at | created_at | updated_at |
 *   is_deleted | deleted_at
 *   INDEX: idx_apikeys_type, idx_apikeys_active, idx_apikeys_hash,
 *          idx_apikeys_prefix, idx_apikeys_user
 *
 * Doğrulama akışı (ADR-020 §2.2-1):
 *   prefix index taraması → hash_equals(key_hash, sha256(raw)) →
 *   is_active=1 AND is_deleted=0 AND (expires_at IS NULL OR expires_at > now) →
 *   IP allowlist (varsa) → scope → last_used_at güncelle
 *
 * Güvenlik: ham anahtar ASLA DB'ye yazılmaz (yalnız SHA-256 hash + prefix),
 * loglara/yanıtlara yalnız `key_prefix` düşer.
 *
 * ADR-002: PDO prepared statement; SELECT * yasak; ORM yasak.
 */
final class ApiKeyRepository
{
    public const DB_KEY = 'auth';

    /** Ham anahtar deseni: cm_live_<8-char prefix>_<random> (ADR-020 §2.1). */
    public const RAW_KEY_PATTERN = '/^(cm_live|cm_test)_([A-Za-z0-9]{8})_([A-Za-z0-9\-_]{16,})$/';

    public const KEY_TYPES = ['user', 'service', 'server'];

    private const SELECT_COLUMNS =
        'id, user_id, key_hash, key_prefix, key_name, scopes, key_type, rate_limit, is_active, last_used_at, expires_at';

    public function __construct(
        private readonly IDatabaseRegistry $registry,
        private readonly string $dbKey = self::DB_KEY,
    ) {}

    private function db(): IDatabaseManager
    {
        return $this->registry->get($this->dbKey);
    }

    /**
     * Yeni API key üret. Ham anahtar YALNIZCA bu çağrıda döner (bir kez);
     * DB'ye yalnız hash + prefix yazılır.
     *
     * @param array{
     *     user_id: string,
     *     key_name?: string,
     *     scopes?: list<string>,
     *     key_type?: string,
     *     rate_limit?: int,
     *     expires_at?: ?string,
     *     allowed_ips?: list<string>,
     *     environment?: 'live'|'test'
     * } $params user_id = 32 karakter hex UUID
     *
     * @return array{
     *     raw_key: string, key_prefix: string, id: string, user_id: string,
     *     key_name: string, key_type: string, scopes: list<string>,
     *     allowed_ips: list<string>, rate_limit: int, expires_at: ?string
     * }
     */
    public function create(array $params): array
    {
        $userIdHex = strtolower((string) $params['user_id']);
        if (!UuidV7::isValidHex($userIdHex)) {
            throw new \InvalidArgumentException('user_id must be a 32-char hex UUID');
        }

        $keyType = (string) ($params['key_type'] ?? 'user');
        if (!in_array($keyType, self::KEY_TYPES, true)) {
            throw new \InvalidArgumentException('key_type must be one of: ' . implode(', ', self::KEY_TYPES));
        }

        $environment = ($params['environment'] ?? 'live') === 'test' ? 'test' : 'live';
        $scopes      = array_values(array_unique(array_map('strval', (array) ($params['scopes'] ?? []))));
        $allowedIps  = array_values(array_unique(array_map('strval', (array) ($params['allowed_ips'] ?? []))));
        $expiresAt   = isset($params['expires_at']) && $params['expires_at'] !== null && $params['expires_at'] !== ''
            ? (string) $params['expires_at']
            : null;
        $rateLimit   = (int) ($params['rate_limit'] ?? 1000);
        $keyName     = (string) ($params['key_name'] ?? 'unnamed');

        $prefix = $this->randomPrefix();
        $secret = $this->randomSecret();
        $rawKey = 'cm_' . $environment . '_' . $prefix . '_' . $secret;

        $idBinary = UuidV7::generateBinary();
        $now      = date('Y-m-d H:i:s');

        $this->db()->write(
            'INSERT INTO api_keys (id, user_id, key_hash, key_prefix, key_name, scopes, key_type, rate_limit, is_active, expires_at, created_at, updated_at) '
            . 'VALUES (:id, :user_id, :key_hash, :key_prefix, :key_name, :scopes, :key_type, :rate_limit, 1, :expires_at, :now1, :now2)',
            [
                'id'         => $idBinary,
                'user_id'    => UuidV7::toBinary($userIdHex),
                'key_hash'   => hash('sha256', $rawKey),
                'key_prefix' => $prefix,
                'key_name'   => $keyName,
                'scopes'     => $this->encodeScopes($scopes, $allowedIps),
                'key_type'   => $keyType,
                'rate_limit' => $rateLimit,
                'expires_at' => $expiresAt,
                'now1'       => $now,
                'now2'       => $now,
            ]
        );

        return [
            'raw_key'     => $rawKey,
            'key_prefix'  => $prefix,
            'id'          => self::toHexSafe($idBinary),
            'user_id'     => $userIdHex,
            'key_name'    => $keyName,
            'key_type'    => $keyType,
            'scopes'      => $scopes,
            'allowed_ips' => $allowedIps,
            'rate_limit'  => $rateLimit,
            'expires_at'  => $expiresAt,
        ];
    }

    /**
     * Ham anahtarı doğrula. Başarılıysa bağlam döner, aksi halde null.
     *
     * @param string|null $requiredScope verilen scope key'de zorunlu olmalı
     * @return array<string, mixed>|null
     */
    public function verify(string $rawKey, ?string $clientIp = null, ?string $requiredScope = null): ?array
    {
        $result = $this->inspect($rawKey, $clientIp, $requiredScope);

        return $result['ok'] ? $result['context'] : null;
    }

    /**
     * Doğrulama nedeniyle birlikte incele (log/audit için; ham anahtar
     * asla dömez/depolanmaz, yalnız `key_prefix`).
     *
     * @return array{ok: bool, reason: string, context: array<string, mixed>|null}
     */
    public function inspect(string $rawKey, ?string $clientIp = null, ?string $requiredScope = null): array
    {
        $prefix = self::prefixFromRaw($rawKey);
        if ($prefix === null) {
            return ['ok' => false, 'reason' => 'malformed_key', 'context' => null];
        }

        $rows = $this->db()->execute(
            'SELECT ' . self::SELECT_COLUMNS . ' FROM api_keys WHERE key_prefix = :prefix AND is_deleted = 0',
            ['prefix' => $prefix]
        );

        $rawHash = hash('sha256', $rawKey);
        $row     = null;

        // Prefix index taraması → sabit-zamanlı hash karşılaştırması.
        foreach ($rows as $candidate) {
            $stored = (string) ($candidate['key_hash'] ?? '');
            if ($stored !== '' && hash_equals($stored, $rawHash)) {
                $row = $candidate;
                break;
            }
        }

        if ($row === null) {
            return ['ok' => false, 'reason' => $rows === [] ? 'unknown_prefix' : 'hash_mismatch', 'context' => null];
        }

        if ((int) ($row['is_active'] ?? 0) !== 1) {
            return ['ok' => false, 'reason' => 'inactive', 'context' => null];
        }

        if ((int) ($row['is_deleted'] ?? 0) !== 0) {
            return ['ok' => false, 'reason' => 'deleted', 'context' => null];
        }

        $now = date('Y-m-d H:i:s');
        $expiresAt = $row['expires_at'] ?? null;
        if ($expiresAt !== null && $expiresAt !== '' && (string) $expiresAt <= $now) {
            return ['ok' => false, 'reason' => 'expired', 'context' => null];
        }

        $decoded = $this->decodeScopes($row['scopes'] ?? null);
        $ips     = $decoded['allowed_ips'];
        $scopes  = $decoded['scopes'];

        if ($ips !== [] && ($clientIp === null || !in_array($clientIp, $ips, true))) {
            return ['ok' => false, 'reason' => 'ip_not_allowed', 'context' => null];
        }

        if ($requiredScope !== null && !in_array($requiredScope, $scopes, true)) {
            return ['ok' => false, 'reason' => 'scope_denied', 'context' => null];
        }

        $context = [
            'api_key_id'  => self::toHexSafe((string) ($row['id'] ?? '')),
            'user_id'     => self::toHexSafe((string) ($row['user_id'] ?? '')),
            'key_prefix'  => (string) ($row['key_prefix'] ?? $prefix),
            'key_name'    => (string) ($row['key_name'] ?? ''),
            'key_type'    => (string) ($row['key_type'] ?? 'user'),
            'scopes'      => $scopes,
            'rate_limit'  => (int) ($row['rate_limit'] ?? 0),
            'expires_at'  => $expiresAt,
            'last_used_at' => $row['last_used_at'] ?? null,
        ];

        // Son adım: last_used_at güncelle (key_hash UNIQUE → tek satır).
        $this->db()->write(
            'UPDATE api_keys SET last_used_at = :now, updated_at = :now2 WHERE key_hash = :key_hash AND is_deleted = 0',
            ['now' => $now, 'now2' => $now, 'key_hash' => $rawHash]
        );

        return ['ok' => true, 'reason' => 'ok', 'context' => $context];
    }

    /**
     * Ham anahtardan prefix çıkar (loglama yalnız bu parçayı kullanır).
     */
    public static function prefixFromRaw(string $rawKey): ?string
    {
        if (preg_match(self::RAW_KEY_PATTERN, $rawKey, $matches) !== 1) {
            return null;
        }

        return $matches[2];
    }

    /**
     * scopes JSON encode — array (sadece scope) veya
     * {"scopes": [...], "allowed_ips": [...]} (IP kısıtı varsa).
     *
     * @param list<string> $scopes
     * @param list<string> $allowedIps
     */
    private function encodeScopes(array $scopes, array $allowedIps): string
    {
        if ($allowedIps === []) {
            return (string) json_encode(array_values($scopes), JSON_UNESCAPED_SLASHES);
        }

        return (string) json_encode(
            ['scopes' => array_values($scopes), 'allowed_ips' => array_values($allowedIps)],
            JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * @return array{scopes: list<string>, allowed_ips: list<string>}
     */
    private function decodeScopes(mixed $raw): array
    {
        if ($raw === null || $raw === '') {
            return ['scopes' => [], 'allowed_ips' => []];
        }

        $decoded = is_string($raw) ? json_decode($raw, true) : $raw;

        if (!is_array($decoded)) {
            return ['scopes' => [], 'allowed_ips' => []];
        }

        // Şema A: düz liste ["api.access", "auth.read"]
        if (array_is_list($decoded)) {
            return [
                'scopes'      => array_values(array_map('strval', $decoded)),
                'allowed_ips' => [],
            ];
        }

        // Şema B: nesne {"scopes": [...], "allowed_ips": [...]}
        return [
            'scopes'      => array_values(array_map('strval', (array) ($decoded['scopes'] ?? []))),
            'allowed_ips' => array_values(array_map('strval', (array) ($decoded['allowed_ips'] ?? []))),
        ];
    }

    private function randomPrefix(): string
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $prefix   = '';

        for ($i = 0; $i < 8; $i++) {
            $prefix .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return $prefix;
    }

    private function randomSecret(): string
    {
        // 32 bayt → URL-safe base64 (43 karakter) — yüksek entropi (ADR-020 §1.3).
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    private static function toHexSafe(string $binary): string
    {
        if (UuidV7::isValidBinary($binary)) {
            return UuidV7::toHex($binary);
        }

        // SQLite test ortamı gibi binary yerine hex tutan katmanlarda
        // (veya bozuk okumada) ham değeri olduğu gibi döndür.
        return $binary;
    }
}
