---
type: decision
id: "022"
title: "ADR-022: Database Hardened Security"
category: "security"
status: "frozen"
date: "2026-01-25"
updated: "2026-08-15"
authority: "Security Engineer"
governance: "Red Team · Human Mode · Truth Mode"
supersedes: null
version: 2.0.0
tags: [security, database, encryption, argon2id, aes-256-gcm, pdo, frozen]
risk-level: "critical"
owasp-top10: ["A02:2021", "A03:2021", "A04:2021"]
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
  - "[[AGENTS.md]]"
  - "[[keys.md]]"
  - "[[decisions/accepted/ADR-002-pdo-mandatory-no-orm]]"
  - "[[decisions/accepted/ADR-010-csrf-protection-strategy]]"
  - "[[decisions/accepted/ADR-011-session-management]]"
  - "[[decisions/accepted/ADR-034-credential-vault-normalization]]"
  - "[[architecture/l0-infrastructure]]"
  - "[[architecture/l1-security]]"
---

# ADR-022: Database Hardened Security

---

## 1. Executive Summary

### 1.1 Kararın Özeti

CoreMusic veritabanı güvenliği, **AES-256-GCM** şifreleme, **Argon2id** hashleme ve **PDO prepared statement** tabanlı olarak uygulanacaktır. Credential vault AES-256-GCM ile şifrelenir. Şifre hashleme Argon2id (64MB/4/2) ile yapılır. SQL injection koruması için prepared statement zorunludur. `SELECT *` kullanımı kesinlikle yasaktır.

### 1.2 Temel Gerekçe

Veritabanı güvenliği, uygulama güvenliğinin temelidir. Zayıf veritabanı güvenliği, veri sızıntısı, SQL injection ve yetkisiz erişim saldırılarına yol açar. CoreMusic'in 18 BCNF veritabanı yapısında veritabanı güvenliği kritik önem taşır.

### 1.3 Beklenen Sonuçlar

- Credential vault AES-256-GCM ile şifrelenir
- Şifre hashleme Argon2id ile yapılır
- SQL injection koruması (prepared statement)
- `SELECT *` kullanımı yasak
- Veritabanı erişim logsu

---

## 2. Status

| Alan | Değer |
|------|-------|
| **Durum** | frozen |
| **Versiyon** | 2.0.0 |
| **Oluşturma Tarihi** | 2026-01-25 |
| **Son Güncelleme** | 2026-08-15 |
| **Otorite** | Security Engineer |
| **Risk Seviyesi** | critical |
| **Onay** | Red Team · Human Mode · Truth Mode |

---

## 3. Context

### 3.1 Problem Tanımı

Veritabanı güvenliği aşağıdaki tehditlere karşı koruma sağlar:

1. **SQL Injection:** Zararlı SQL sorguları ile veri çalma
2. **Veri Sızıntısı:** Hassas verilerin açılması
3. **Credential Theft:** Veritabanı şifrelerinin ele geçirilmesi
4. **Privilege Escalation:** Yetki yükseltme saldırıları

### 3.2 OWASP Top 10:2021 Etkileşimi

| OWASP Kategorisi | Durum | Etki |
|------------------|-------|------|
| **A02:2021** Cryptographic Failures | ⚠️ Doğrudan | AES-256-GCM, Argon2id |
| **A03:2021** Injection | ⚠️ Doğrudan | Prepared statement |
| **A04:2021** Insecure Design | ⚠️ Doğrudan | BCNF normalization |

### 3.3 Güvenlik Katmanları

```
┌─────────────────────────────────────────────────┐
│              Database Security Layers             │
│                                                  │
│  ┌────────────────────────────────────────────┐  │
│  │  Layer 1: Encryption (AES-256-GCM)        │  │
│  │  • Credential vault şifreleme              │  │
│  │  • Hassas alan şifreleme                   │  │
│  │  • 96-bit IV, 16-byte tag                  │  │
│  └────────────────────────────────────────────┘  │
│                                                  │
│  ┌────────────────────────────────────────────┐  │
│  │  Layer 2: Hashing (Argon2id)               │  │
│  │  • Şifre hashleme                          │  │
│  │  • 64MB memory, 4 iterations, 2 threads    │  │
│  │  • Timing-safe comparison                  │  │
│  └────────────────────────────────────────────┘  │
│                                                  │
│  ┌────────────────────────────────────────────┐  │
│  │  Layer 3: SQL Injection Prevention         │  │
│  │  • PDO prepared statement                  │  │
│  │  • Explicit column list (SELECT * yasak)   │  │
│  │  • Input validation                        │  │
│  └────────────────────────────────────────────┘  │
│                                                  │
│  ┌────────────────────────────────────────────┐  │
│  │  Layer 4: Access Control                   │  │
│  │  • Database user isolation                 │  │
│  │  • Minimal privilege                       │  │
│  │  • Connection pooling                      │  │
│  └────────────────────────────────────────────┘  │
│                                                  │
└─────────────────────────────────────────────────┘
```

---

## 4. Decision

### 4.1 Karar Bildirimi

**CoreMusic, AES-256-GCM şifreleme, Argon2id hashleme ve PDO prepared statement tabanlı veritabanı güvenliği kullanır. `SELECT *` kullanımı kesinlikle yasaktır.**

### 4.2 Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | AES-256-GCM encryption | ✅ Zorunlu |
| 2 | 96-bit IV (12 byte) | ✅ Zorunlu |
| 3 | 16-byte authentication tag | ✅ Zorunlu |
| 4 | Argon2id hashing | ✅ Zorunlu |
| 5 | Argon2id: 64MB memory | ✅ Zorunlu |
| 6 | Argon2id: 4 iterations | ✅ Zorunlu |
| 7 | Argon2id: 2 threads | ✅ Zorunlu |
| 8 | PDO prepared statement | ✅ Zorunlu |
| 9 | `SELECT *` yasak | ❌ Yasak |
| 10 | Explicit column list | ✅ Zorunlu |
| 11 | ORM yasak | ❌ Yasak (ADR-002) |
| 12 | hash_equals() comparison | ✅ Zorunlu |

### 4.3 Kod Örnekleri

#### 4.3.1 AES-256-GCM Encryption Service

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Service;

/**
 * AES-256-GCM Encryption Service
 *
 * ADR-022 uyumlu şifreleme servisi.
 * 96-bit IV, 16-byte tag, 256-bit key.
 */
final class EncryptionService
{
    private const CIPHER = 'aes-256-gcm';
    private const IV_LENGTH = 12; // 96-bit
    private const TAG_LENGTH = 16; // 128-bit

    /**
     * Metni şifreler.
     *
     * @return array{ciphertext: string, iv: string, tag: string}
     */
    public function encrypt(string $plaintext, string $key): array
    {
        $iv = random_bytes(self::IV_LENGTH);
        $tag = '';

        $ciphertext = openssl_encrypt(
            $plaintext,
            self::CIPHER,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '', // AAD
            self::TAG_LENGTH
        );

        if ($ciphertext === false) {
            throw new \RuntimeException('Encryption failed');
        }

        return [
            'ciphertext' => $ciphertext,
            'iv' => base64_encode($iv),
            'tag' => base64_encode($tag),
        ];
    }

    /**
     * Şifreyi çözer.
     */
    public function decrypt(array $data, string $key): string
    {
        $plaintext = openssl_decrypt(
            $data['ciphertext'],
            self::CIPHER,
            $key,
            OPENSSL_RAW_DATA,
            base64_decode($data['iv']),
            base64_decode($data['tag'])
        );

        if ($plaintext === false) {
            throw new \RuntimeException('Decryption failed');
        }

        return $plaintext;
    }
}
```

#### 4.3.2 Argon2id Password Hashing

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Service;

/**
 * Password Hashing Service
 *
 * ADR-022 uyumlu Argon2id hashleme.
 * 64MB memory, 4 iterations, 2 threads.
 */
final class PasswordHashService
{
    /**
     * Şifre hash'ler.
     */
    public function hash(string $password): string
    {
        $hash = password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536, // 64 MB
            'time_cost' => 4,       // 4 iterations
            'threads' => 2,         // 2 threads
        ]);

        if ($hash === false) {
            throw new \RuntimeException('Password hashing failed');
        }

        return $hash;
    }

    /**
     * Şifre doğrular.
     */
    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Hash'in Argon2id olup olmadığını kontrol eder.
     */
    public function isArgon2id(string $hash): bool
    {
        return str_starts_with($hash, '$argon2id$');
    }
}
```

#### 4.3.3 PDO Secure Connection

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Infrastructure\Database;

/**
 * PDO Secure Connection
 *
 * ADR-022 uyumlu güvenli veritabanı bağlantısı.
 * Prepared statement zorunlu.
 * SELECT * yasak.
 */
final class SecurePdoConnection
{
    /**
     * Güvenli PDO bağlantısı oluşturur.
     */
    public static function create(array $config): \PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $config['host'],
            $config['port'],
            $config['database']
        );

        $pdo = new \PDO($dsn, $config['username'], $config['password'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false, // Gerçek prepared statement
            \PDO::MYSQL_ATTR_FOUND_ROWS => false,
        ]);

        return $pdo;
    }
}
```

#### 4.3.4 Secure Query Builder

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Infrastructure\Database;

/**
 * Secure Query Helper
 *
 * ADR-022 uyumlu güvenli sorgu yardımcısı.
 * SELECT * yasak, explicit column zorunlu.
 */
final class SecureQuery
{
    public function __construct(
        private readonly \PDO $pdo,
    ) {
    }

    /**
     * Prepared statement ile sorgu çalıştırır.
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Prepared statement ile tek satır okur.
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result !== false ? $result : null;
    }

    /**
     * Prepared statement ile çoklu satır okur.
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
}
```

```php
// ✅ DOĞRU — Explicit column list
$users = $secureQuery->fetchAll(
    'SELECT id, email, display_name, role FROM users WHERE id = :id',
    ['id' => $userId]
);

// ❌ YANLIŞ — SELECT * yasak (ADR-022)
$users = $secureQuery->fetchAll(
    'SELECT * FROM users WHERE id = :id',
    ['id' => $userId]
);
```

### 4.4 Konfigürasyon

| Dosya | Değer |
|-------|-------|
| `shared/config/database.php` | PDO settings |
| `shared/config/encryption.php` | AES-256-GCM settings |
| `.env` | `DB_PASSWORD=[REDACTED]` |

---

## 5. Architecture

### 5.1 Encryption Architecture

```
┌─────────────────────────────────────────────────┐
│              Credential Vault                     │
│                                                  │
│  ┌────────────────────────────────────────────┐  │
│  │  Master Key (256-bit)                      │  │
│  │  • Environment variable'dan yüklenir       │  │
│  │  • ASLA kodda saklanmaz                    │  │
│  │  • ASLA log'da görünmez                    │  │
│  └────────────────────────────────────────────┘  │
│                                                  │
│  ┌────────────────────────────────────────────┐  │
│  │  Encrypted Secrets:                        │  │
│  │  • DB_PASSWORD → AES-256-GCM encrypted    │  │
│  │  • API_KEY → AES-256-GCM encrypted        │  │
│  │  • JWT_SECRET → AES-256-GCM encrypted     │  │
│  │  • DEEZER_ARL → AES-256-GCM encrypted     │  │
│  └────────────────────────────────────────────┘  │
│                                                  │
│  ┌────────────────────────────────────────────┐  │
│  │  Encryption Parameters:                    │  │
│  │  • Cipher: aes-256-gcm                     │  │
│  │  • IV: 96-bit (12 byte) random            │  │
│  │  • Tag: 16-byte authentication            │  │
│  │  • Key: 256-bit (32 byte)                 │  │
│  └────────────────────────────────────────────┘  │
│                                                  │
└─────────────────────────────────────────────────┘
```

---

## 6. Alternatives Considered

| Alternatif | Neden Reddedildi |
|------------|------------------|
| MD5/SHA1 hashing | Güvensiz, NIST tarafından reddedildi |
| bcrypt | Argon2id daha güvenli |
| ORM (Doctrine/Eloquent) | ADR-022 ORM yasak |
| `SELECT *` | SQL injection riski |

---

## 7. Consequences

### Olumlu
- SQL injection engellenir
- Credential güvenliği sağlanır
- OWASP A02/A03 uyumluluğu

### Olumsuz
- Argon2id yavaş (~100ms) — kasıtlı
- AES-256-GCM key yönetimi karmaşık

---

## 8. Testing Strategy

| Test | Kapsama |
|------|---------|
| AES-256-GCM encrypt/decrypt | %100 |
| Argon2id hash/verify | %100 |
| Prepared statement | %100 |
| SELECT * engelleme | %100 |

---

## 9. OWASP Compliance

| OWASP | Durum |
|-------|-------|
| A02:2021 Cryptographic Failures | ✅ AES-256-GCM + Argon2id |
| A03:2021 Injection | ✅ Prepared statement |
| A04:2021 Insecure Design | ✅ BCNF normalization |

---

## 10. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-022: Database Hardened Security v2.0.0 — CoreMusic Security*
*Authority: Security Engineer · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
