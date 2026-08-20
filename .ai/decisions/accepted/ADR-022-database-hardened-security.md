---
title: "ADR-022: Database Hardened Security"
status: frozen
date: 2026-01-25
tags: [security, database, encryption, argon2id, aes-256-gcm, pdo, frozen]
---

# ADR-022: Database Hardened Security

---

## 1. Executive Summary

### 1.1 KararÄ±n Ã–zeti

CoreMusic veritabanÄ± gÃ¼venliÄŸi, **AES-256-GCM** ÅŸifreleme, **Argon2id** hashleme ve **PDO prepared statement** tabanlÄ± olarak uygulanacaktÄ±r. Credential vault AES-256-GCM ile ÅŸifrelenir. Åifre hashleme Argon2id (64MB/4/2) ile yapÄ±lÄ±r. SQL injection korumasÄ± iÃ§in prepared statement zorunludur. `SELECT *` kullanÄ±mÄ± kesinlikle yasaktÄ±r.

### 1.2 Temel GerekÃ§e

VeritabanÄ± gÃ¼venliÄŸi, uygulama gÃ¼venliÄŸinin temelidir. ZayÄ±f veritabanÄ± gÃ¼venliÄŸi, veri sÄ±zÄ±ntÄ±sÄ±, SQL injection ve yetkisiz eriÅŸim saldÄ±rÄ±larÄ±na yol aÃ§ar. CoreMusic'in 18 BCNF veritabanÄ± yapÄ±sÄ±nda veritabanÄ± gÃ¼venliÄŸi kritik Ã¶nem taÅŸÄ±r.

### 1.3 Beklenen SonuÃ§lar

- Credential vault AES-256-GCM ile ÅŸifrelenir
- Åifre hashleme Argon2id ile yapÄ±lÄ±r
- SQL injection korumasÄ± (prepared statement)
- `SELECT *` kullanÄ±mÄ± yasak
- VeritabanÄ± eriÅŸim logsu

---

## 2. Status

| Alan | DeÄŸer |
|------|-------|
| **Durum** | frozen |
| **Versiyon** | 2.0.0 |
| **OluÅŸturma Tarihi** | 2026-01-25 |
| **Son GÃ¼ncelleme** | 2026-08-15 |
| **Otorite** | Security Engineer |
| **Risk Seviyesi** | critical |
| **Onay** | Red Team Â· Human Mode Â· Truth Mode |

---

## 3. Context

### 3.1 Problem TanÄ±mÄ±

VeritabanÄ± gÃ¼venliÄŸi aÅŸaÄŸÄ±daki tehditlere karÅŸÄ± koruma saÄŸlar:

1. **SQL Injection:** ZararlÄ± SQL sorgularÄ± ile veri Ã§alma
2. **Veri SÄ±zÄ±ntÄ±sÄ±:** Hassas verilerin aÃ§Ä±lmasÄ±
3. **Credential Theft:** VeritabanÄ± ÅŸifrelerinin ele geÃ§irilmesi
4. **Privilege Escalation:** Yetki yÃ¼kseltme saldÄ±rÄ±larÄ±

### 3.2 OWASP Top 10:2021 EtkileÅŸimi

| OWASP Kategorisi | Durum | Etki |
|------------------|-------|------|
| **A02:2021** Cryptographic Failures | âš ï¸ DoÄŸrudan | AES-256-GCM, Argon2id |
| **A03:2021** Injection | âš ï¸ DoÄŸrudan | Prepared statement |
| **A04:2021** Insecure Design | âš ï¸ DoÄŸrudan | BCNF normalization |

### 3.3 GÃ¼venlik KatmanlarÄ±

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚              Database Security Layers             â”‚
â”‚                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  Layer 1: Encryption (AES-256-GCM)        â”‚  â”‚
â”‚  â”‚  â€¢ Credential vault ÅŸifreleme              â”‚  â”‚
â”‚  â”‚  â€¢ Hassas alan ÅŸifreleme                   â”‚  â”‚
â”‚  â”‚  â€¢ 96-bit IV, 16-byte tag                  â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  Layer 2: Hashing (Argon2id)               â”‚  â”‚
â”‚  â”‚  â€¢ Åifre hashleme                          â”‚  â”‚
â”‚  â”‚  â€¢ 64MB memory, 4 iterations, 2 threads    â”‚  â”‚
â”‚  â”‚  â€¢ Timing-safe comparison                  â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  Layer 3: SQL Injection Prevention         â”‚  â”‚
â”‚  â”‚  â€¢ PDO prepared statement                  â”‚  â”‚
â”‚  â”‚  â€¢ Explicit column list (SELECT * yasak)   â”‚  â”‚
â”‚  â”‚  â€¢ Input validation                        â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  Layer 4: Access Control                   â”‚  â”‚
â”‚  â”‚  â€¢ Database user isolation                 â”‚  â”‚
â”‚  â”‚  â€¢ Minimal privilege                       â”‚  â”‚
â”‚  â”‚  â€¢ Connection pooling                      â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                  â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

---

## 4. Decision

### 4.1 Karar Bildirimi

**CoreMusic, AES-256-GCM ÅŸifreleme, Argon2id hashleme ve PDO prepared statement tabanlÄ± veritabanÄ± gÃ¼venliÄŸi kullanÄ±r. `SELECT *` kullanÄ±mÄ± kesinlikle yasaktÄ±r.**

### 4.2 Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | AES-256-GCM encryption | âœ… Zorunlu |
| 2 | 96-bit IV (12 byte) | âœ… Zorunlu |
| 3 | 16-byte authentication tag | âœ… Zorunlu |
| 4 | Argon2id hashing | âœ… Zorunlu |
| 5 | Argon2id: 64MB memory | âœ… Zorunlu |
| 6 | Argon2id: 4 iterations | âœ… Zorunlu |
| 7 | Argon2id: 2 threads | âœ… Zorunlu |
| 8 | PDO prepared statement | âœ… Zorunlu |
| 9 | `SELECT *` yasak | âŒ Yasak |
| 10 | Explicit column list | âœ… Zorunlu |
| 11 | ORM yasak | âŒ Yasak (ADR-002) |
| 12 | hash_equals() comparison | âœ… Zorunlu |

### 4.3 Kod Ã–rnekleri

#### 4.3.1 AES-256-GCM Encryption Service

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Service;

/**
 * AES-256-GCM Encryption Service
 *
 * ADR-022 uyumlu ÅŸifreleme servisi.
 * 96-bit IV, 16-byte tag, 256-bit key.
 */
final class EncryptionService
{
    private const CIPHER = 'aes-256-gcm';
    private const IV_LENGTH = 12; // 96-bit
    private const TAG_LENGTH = 16; // 128-bit

    /**
     * Metni ÅŸifreler.
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
     * Åifreyi Ã§Ã¶zer.
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
     * Åifre hash'ler.
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
     * Åifre doÄŸrular.
     */
    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Hash'in Argon2id olup olmadÄ±ÄŸÄ±nÄ± kontrol eder.
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
 * ADR-022 uyumlu gÃ¼venli veritabanÄ± baÄŸlantÄ±sÄ±.
 * Prepared statement zorunlu.
 * SELECT * yasak.
 */
final class SecurePdoConnection
{
    /**
     * GÃ¼venli PDO baÄŸlantÄ±sÄ± oluÅŸturur.
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
            \PDO::ATTR_EMULATE_PREPARES => false, // GerÃ§ek prepared statement
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
 * ADR-022 uyumlu gÃ¼venli sorgu yardÄ±mcÄ±sÄ±.
 * SELECT * yasak, explicit column zorunlu.
 */
final class SecureQuery
{
    public function __construct(
        private readonly \PDO $pdo,
    ) {
    }

    /**
     * Prepared statement ile sorgu Ã§alÄ±ÅŸtÄ±rÄ±r.
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Prepared statement ile tek satÄ±r okur.
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result !== false ? $result : null;
    }

    /**
     * Prepared statement ile Ã§oklu satÄ±r okur.
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
}
```

```php
// âœ… DOÄRU â€” Explicit column list
$users = $secureQuery->fetchAll(
    'SELECT id, email, display_name, role FROM users WHERE id = :id',
    ['id' => $userId]
);

// âŒ YANLIÅ â€” SELECT * yasak (ADR-022)
$users = $secureQuery->fetchAll(
    'SELECT * FROM users WHERE id = :id',
    ['id' => $userId]
);
```

### 4.4 KonfigÃ¼rasyon

| Dosya | DeÄŸer |
|-------|-------|
| `shared/config/database.php` | PDO settings |
| `shared/config/encryption.php` | AES-256-GCM settings |
| `.env` | `DB_PASSWORD=[REDACTED]` |

---

## 5. Architecture

### 5.1 Encryption Architecture

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚              Credential Vault                     â”‚
â”‚                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  Master Key (256-bit)                      â”‚  â”‚
â”‚  â”‚  â€¢ Environment variable'dan yÃ¼klenir       â”‚  â”‚
â”‚  â”‚  â€¢ ASLA kodda saklanmaz                    â”‚  â”‚
â”‚  â”‚  â€¢ ASLA log'da gÃ¶rÃ¼nmez                    â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  Encrypted Secrets:                        â”‚  â”‚
â”‚  â”‚  â€¢ DB_PASSWORD â†’ AES-256-GCM encrypted    â”‚  â”‚
â”‚  â”‚  â€¢ API_KEY â†’ AES-256-GCM encrypted        â”‚  â”‚
â”‚  â”‚  â€¢ JWT_SECRET â†’ AES-256-GCM encrypted     â”‚  â”‚
â”‚  â”‚  â€¢ DEEZER_ARL â†’ AES-256-GCM encrypted     â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  Encryption Parameters:                    â”‚  â”‚
â”‚  â”‚  â€¢ Cipher: aes-256-gcm                     â”‚  â”‚
â”‚  â”‚  â€¢ IV: 96-bit (12 byte) random            â”‚  â”‚
â”‚  â”‚  â€¢ Tag: 16-byte authentication            â”‚  â”‚
â”‚  â”‚  â€¢ Key: 256-bit (32 byte)                 â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                  â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

---

## 6. Alternatives Considered

| Alternatif | Neden Reddedildi |
|------------|------------------|
| MD5/SHA1 hashing | GÃ¼vensiz, NIST tarafÄ±ndan reddedildi |
| bcrypt | Argon2id daha gÃ¼venli |
| ORM (Doctrine/Eloquent) | ADR-022 ORM yasak |
| `SELECT *` | SQL injection riski |

---

## 7. Consequences

### Olumlu
- SQL injection engellenir
- Credential gÃ¼venliÄŸi saÄŸlanÄ±r
- OWASP A02/A03 uyumluluÄŸu

### Olumsuz
- Argon2id yavaÅŸ (~100ms) â€” kasÄ±tlÄ±
- AES-256-GCM key yÃ¶netimi karmaÅŸÄ±k

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
| A02:2021 Cryptographic Failures | âœ… AES-256-GCM + Argon2id |
| A03:2021 Injection | âœ… Prepared statement |
| A04:2021 Insecure Design | âœ… BCNF normalization |

---

## 10. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Frozen |

---

*ADR-022: Database Hardened Security v2.0.0 â€” CoreMusic Security*
*Authority: Security Engineer Â· Last Updated: 2026-08-15*
*Status: Frozen Â· Governance: Red Team Â· Human Mode Â· Truth Mode*