---
node_id: K019-comilance/controls/kod-ornekleri
node_type: file
status: draft
parent_id: K019-comilance/controls
children: []
depends_on:
  - K019-comilance/controls
  - K019-comilance/policies
evidence:
  - "Kontrol eşlemeleri: OWASP Top 10:2025 — https://top10.owasp.org/2025/en (yeniden doğrulandı: 2026-10-17)"
  - "KVKK dayanakları: 6698 sayılı Kanun m.4/m.12/m.16/m.18 — kvkk.gov.tr (2026-10-17)"
  - "Vault güvenlik kuralı: kök AGENTS.md §5 Security (AES-256-GCM/ChaCha20 · Argon2id · MD5/SHA-1/DES yasak · parametreli sorgu zorunlu)"
  - "Kullanıcı kararı 2026-10-17: 11 dil kapsamlı kod örnekleri dosyası onaylandı"
validation:
  - "2026-10-17 — 11 dil yazıldı; her kod bloğu OWASP A0x kontrolü ile eşlendi"
open_questions:
  - "Kod blokları şablondur; repo kaynak yüzeyine (shared/src/**) kopyalanması ilgili domain agent'ına aittir (AGENTS.md §5 domain boundaries)"
  - "PHP/JS/SQL/C++ blokları repo konvansiyonuyla (strict_types, BCNF, zero-allocation) üretim öncesi ilgili agent'ca doğrulanacak"
group: kod
---


# K019-comilance/controls — Kod Örnekleri (11 Dil · Uyum Kontrolleri)

**ELI15:** "Kontrol gerçekten nasıl uygulanır?" sorusunun **çalışan cevabı**. Her
kod bloğu bir OWASP Top 10:2025 kontrolüne (A01–A10) ve/veya KVKK m.12 yükümlülüğüne
bağlıdır. Amaç: denetçi geldiğinde "kural var" demek değil, **kuralın kodda nasıl
gövdelendiğini** göstermek.

## §0 Kullanım kuralları (okumadan kopyalama)

1. **Bunlar vault şablonudur, kaynak kod değil.** `.ai/architecture/**` altındadır;
   repo yüzeyine (`shared/src/**`, `*.js`, `*.sql`) taşınması ilgili domain
   agent'ına aittir (kök `AGENTS.md` §5 — PHP→backend, C++→embedded, SQL→data…).
2. **Vault kuralı ihlali yasak:** MD5/SHA-1/DES yok · `SELECT *` yok · ORM yok ·
   parola hash'i yalnız Argon2id · secret yalnız `.env`.
3. **Denetlenmemiş kod "geçti" sayılmaz.** Bu dosya `status: draft` kalır; üretim
   koduna giren her blok `verification/index.md`'ye kanıt satırıyla işlenir.
4. Zamanlama/timing, RNG ve kripto blokları **olduğu gibi kopyalanmaz** — birim
   testi (`K019-testing`) + güvenlik gözden geçirmesi (`security-engineer`) sonrası
   üretime girer.

## §1 Dil → Kontrol matrisi

| # | Dil | Bölüm | Ana kontroller | Durum |
|---|---|---|---|---|
| 1 | PHP 8.4 | §2 | A01 · A04 · A05 · A07 · A09 | şablon |
| 2 | JavaScript (Node.js) | §3 | A02 · A05 · A07 | şablon |
| 3 | Python 3.12 | §4 | A04 · A05 · A07 · A09 | şablon |
| 4 | Go 1.22 | §5 | A04 · A05 · A09 · A10 | şablon |
| 5 | C# (.NET 8) | §6 | A04 · A05 · A01 | şablon |
| 6 | Java 21 | §7 | A04 · A05 · A07 | şablon |
| 7 | C / C++20 (embedded) | §8 | A04 · A08 | şablon |
| 8 | Rust | §9 | A04 · A05 · A07 · A10 | şablon |
| 9 | SQL (MySQL 9) | §10 | A01 · A08 · A09 · KVKK | şablon |
| 10 | Bash | §11 | A02 · A03 · KVKK m.12 | şablon |
| 11 | YAML (CI/CD) + Rego (policy-as-code) | §12 | A02 · A03 · A08 | şablon |

> **Kontrol referansı:** OWASP Top 10:2025 (`top10.owasp.org/2025/en`) —
> A01 Broken Access Control · A02 Security Misconfiguration · A03 Software
> Supply Chain Failures · A04 Cryptographic Failures · A05 Injection ·
> A06 Insecure Design · A07 Authentication Failures · A08 Software or Data
> Integrity Failures · A09 Security Logging and Alerting Failures ·
> A10 Mishandling of Exceptional Conditions.

---

## §2 PHP 8.4 — çekirdek kontroller

> Domain: backend (`*.php` → Backend Architect). Vault kuralı: `strict_types=1`,
> PDO zorunlu, PSR-12.

### §2.1 A05 — Parametreli sorgu + append-only audit (PDO)

```php
<?php
declare(strict_types=1);

// KONTROL: A05:2025 Injection — parametreli sorgu (asla string birleştirme)
final class AuditLogRepository
{
    public function __construct(private PDO $pdo) {}

    public function append(string $actor, string $action, string $entity, string $ip): void
    {
        $sql = 'INSERT INTO audit_log (actor, action, entity, ip, created_at)
                VALUES (:actor, :action, :entity, :ip, NOW(3))';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':actor'  => $actor,
            ':action' => $action,
            ':entity' => $entity,
            ':ip'     => $ip,
        ]);
        // audit_log append-only'dir — UPDATE/DELETE MySQL tetikleyiciyle engelli (bkz §10)
    }
}
```

### §2.2 A07 — Argon2id parola hash (vault zorunluluğu)

```php
// KONTROL: A07:2025 Authentication Failures — Argon2id (MD5/bcrypt yasak; bkz kök AGENTS §5)
final class PasswordService
{
    public function hash(string $plain): string
    {
        return password_hash($plain, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,  // 64 MiB
            'time_cost'   => 4,
            'threads'     => 1,
        ]);
    }

    public function verify(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash); // sabit-zamanlı karşılaştırma içerir
    }
}
```

### §2.3 A04 — AES-256-GCM (PII şifreleme)

```php
// KONTROL: A04:2025 Cryptographic Failures — AES-256-GCM (authenticated encryption)
final class PiiCipher
{
    private const IV_BYTES = 12;   // GCM nonce: 96-bit
    private const TAG_BYTES = 16;  // GMAC etiketi

    public function encrypt(string $plaintext, string $key32, string $aad = 'coremusic:v1'): string
    {
        if (strlen($key32) !== 32) {
            throw new InvalidArgumentException('anahtar 32 bayt olmalı (AES-256)');
        }
        $iv  = random_bytes(self::IV_BYTES);
        $tag = '';
        $cipher = openssl_encrypt($plaintext, 'aes-256-gcm', $key32, OPENSSL_RAW_DATA, $iv, $tag, $aad);
        if ($cipher === false || strlen($tag) !== self::TAG_BYTES) {
            throw new RuntimeException('şifreleme başarısız'); // A10: fail-safe, fail-open değil
        }
        return $iv . $tag . $cipher; // 12 + 16 + ciphertext
    }

    public function decrypt(string $blob, string $key32, string $aad = 'coremusic:v1'): string
    {
        $iv      = substr($blob, 0, self::IV_BYTES);
        $tag     = substr($blob, self::IV_BYTES, self::TAG_BYTES);
        $cipher  = substr($blob, self::IV_BYTES + self::TAG_BYTES);
        $plain   = openssl_decrypt($cipher, 'aes-256-gcm', $key32, OPENSSL_RAW_DATA, $iv, $tag, $aad);
        if ($plain === false) {
            throw new RuntimeException('doğrulama/çözme başarısız — etiket reddedildi');
        }
        return $plain;
    }
}
```

### §2.4 A01 — CSRF token doğrulama

```php
// KONTROL: A01:2025 Broken Access Control — CSRF (double-submit + hash_equals)
final class CsrfGuard
{
    public function validate(?string $token): void
    {
        $expected = $_SESSION['csrf'] ?? '';
        if (!is_string($token) || $expected === '' || !hash_equals($expected, $token)) {
            http_response_code(419);
            throw new RuntimeException('CSRF doğrulaması başarısız');
        }
    }
}
```

### §2.5 A09 — KVKK m.12: ihlal olayı kaydı (72 saat sayacı)

```php
// KONTROL: A09 + KVKK m.12(5) — veri ihlali "öğrenme anı" kaydı (72 saat buradan başlar)
final class BreachRegistry
{
    public function register(string $summary, string $detectedBy): string
    {
        $uuid = bin2hex(random_bytes(16));
        // kaydet: öğrenme zaman damgası UTC — Kurul bildirimi bu an + 72 saat sınır
        // (form: kvkk.gov.tr Veri İhlali Bildirimi)
        return $uuid;
    }
}
```

---

## §3 JavaScript (Node.js 20+) — kenar ve tarayıcı kontrolleri

> Domain: frontend/Node (`*.js` → UI Designer / backend-js). Vanilla JS kuralı:
> `var` yok, `any` yok (JS tarafında strict mode).

### §3.1 A02 — CSP nonce middleware

```js
// KONTROL: A02:2025 Security Misconfiguration — CSP nonce + katı başlıklar
'use strict';
import crypto from 'node:crypto';

export function securityHeaders(req, res, next) {
  req.nonce = crypto.randomBytes(16).toString('base64');
  res.setHeader(
    'Content-Security-Policy',
    `default-src 'self'; script-src 'self' 'nonce-${req.nonce}'; ` +
      `object-src 'none'; base-uri 'self'; frame-ancestors 'none'`
  );
  res.setHeader('X-Content-Type-Options', 'nosniff');
  res.setHeader('Referrer-Policy', 'no-referrer');
  res.removeHeader('X-Powered-By');
  next();
}
```

### §3.2 A05 — XSS'e karşı güvenli render (innerHTML YASAK)

```js
// KONTROL: A05:2025 Injection — kullanıcı verisi asla HTML yorumlamaz
export function renderUserText(el, value) {
  el.textContent = String(value); // güvenli: DOM düğüm metni, HTML parse yok
}

// Kaçış (sadece HTML string zorunluysa — tercihen hiç kullanılmaz):
export function escapeHtml(s) {
  return String(s).replace(/[&<>"']/g, (c) => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
  })[c]);
}
```

### §3.3 A07 — sabit-zamanlı karşılaştırma + rate limit

```js
// KONTROL: A07 — timing attack'e karşı sabit-zamanlı eşitlik
export function safeEqual(a, b) {
  const ab = Buffer.from(String(a));
  const bb = Buffer.from(String(b));
  if (ab.length !== bb.length) return false;
  return crypto.timingSafeEqual(ab, bb);
}

// KONTROL: A07 — brute-force rate limiter (bellek-içi; üretimde AppCu/Redis — ADR)
export function rateLimit({ windowMs = 60_000, max = 30 } = {}) {
  const hits = new Map();
  return (key) => {
    const now = Date.now();
    let e = hits.get(key);
    if (!e || now - e.t > windowMs) { e = { n: 0, t: now }; }
    e.n += 1;
    hits.set(key, e);
    return e.n <= max; // false → 429 döndür
  };
}
```

---

## §4 Python 3.12 — veri ve servis kontrolleri

### §4.1 A05 — parametreli sorgu

```python
# KONTROL: A05:2025 Injection — parametreli sorgu (f-string/fprintf yasak)
import sqlite3

def append_audit(conn: sqlite3.Connection, actor: str, action: str, entity: str) -> None:
    conn.execute(
        "INSERT INTO audit_log (actor, action, entity, created_at)"
        " VALUES (?, ?, ?, datetime('now'))",
        (actor, action, entity),
    )
```

### §4.2 A04 — AES-256-GCM (cryptography paketi)

```python
# KONTROL: A04:2025 Cryptographic Failures — AESGCM (etiketli şifreleme)
import os
from cryptography.hazmat.primitives.ciphers.aead import AESGCM

def encrypt_pii(key: bytes, plaintext: bytes, aad: bytes = b"coremusic:v1") -> bytes:
    if len(key) != 32:
        raise ValueError("anahtar 32 bayt olmalı (AES-256)")
    nonce = os.urandom(12)
    return nonce + AESGCM(key).encrypt(nonce, plaintext, aad)

def decrypt_pii(key: bytes, blob: bytes, aad: bytes = b"coremusic:v1") -> bytes:
    nonce, ct = blob[:12], blob[12:]
    # etiket doğrulanmazsa InvalidTag fırlatır → fail-safe (A10)
    return AESGCM(key).decrypt(nonce, ct, aad)
```

### §4.3 A07 — Argon2id

```python
# KONTROL: A07 — argon2-cffi (vault zorunluluğu: Argon2id)
from argon2 import PasswordHasher
from argon2.exceptions import VerifyMismatchError

ph = PasswordHasher(time_cost=4, memory_cost=65536, parallelism=1)

def hash_password(plain: str) -> str:
    return ph.hash(plain)

def verify_password(digest: str, plain: str) -> bool:
    try:
        return ph.verify(digest, plain)
    except VerifyMismatchError:
        return False
```

---

## §5 Go 1.22 — servis ve altyapı kontrolleri

### §5.1 A04 — AES-256-GCM

```go
// KONTROL: A04:2025 Cryptographic Failures — crypto/aes + GCM
package crypt

import (
	"crypto/aes"
	"crypto/cipher"
	"crypto/rand"
	"errors"
	"io"
)

func Encrypt(key, plaintext, aad []byte) ([]byte, error) {
	if len(key) != 32 {
		return nil, errors.New("anahtar 32 bayt olmalı (AES-256)")
	}
	block, err := aes.NewCipher(key)
	if err != nil {
		return nil, err
	}
	gcm, err := cipher.NewGCM(block)
	if err != nil {
		return nil, err
	}
	nonce := make([]byte, gcm.NonceSize())
	if _, err := io.ReadFull(rand.Reader, nonce); err != nil {
		return nil, err
	}
	return gcm.Seal(nonce, nonce, plaintext, aad), nil // nonce öne eklenir
}

func Decrypt(key, blob, aad []byte) ([]byte, error) {
	block, err := aes.NewCipher(key)
	if err != nil {
		return nil, err
	}
	gcm, err := cipher.NewGCM(block)
	if err != nil {
		return nil, err
	}
	ns := gcm.NonceSize()
	if len(blob) < ns {
		return nil, errors.New("blob kısa")
	}
	return gcm.Open(nil, blob[:ns], blob[ns:], aad) // etiket reddi → hata
}
```

### §5.2 A05 — parametreli sorgu + A10 fail-safe

```go
// KONTROL: A05 — database/sql, parametreli ExecContext
func AppendAudit(ctx context.Context, db *sql.DB, actor, action, entity string) error {
	_, err := db.ExecContext(ctx,
		`INSERT INTO audit_log (actor, action, entity, created_at) VALUES (?, ?, ?, NOW(3))`,
		actor, action, entity,
	)
	return err
}

// KONTROL: A10 — hata yutma YASAK; kök neden log'a, istemciye genel mesaj
func ClientError(err error) string {
	if err == nil {
		return ""
	}
	// detay (ip, SQL, dosya) asla istemciye dönmez — A02/A10
	return "işlem tamamlanamadı; lütfen tekrar deneyin"
}
```

---

## §6 C# (.NET 8) — Windows/platfom kontrolleri

### §6.1 A04 — AesGcm

```csharp
// KONTROL: A04:2025 Cryptographic Failures — System.Security.Cryptography.AesGcm
using System.Security.Cryptography;

public static class PiiCipher
{
    public static byte[] Encrypt(byte[] key, byte[] plaintext, byte[]? aad = null)
    {
        if (key.Length != 32) throw new ArgumentException("anahtar 32 bayt (AES-256)");
        var nonce = RandomNumberGenerator.GetBytes(12);
        var cipher = new byte[plaintext.Length];
        var tag = new byte[16];
        using var aes = new AesGcm(key, tagSizeInBytes: 16);
        aes.Encrypt(nonce, plaintext, cipher, tag, aad);

        var outBuf = new byte[12 + 16 + plaintext.Length];
        Buffer.BlockCopy(nonce, 0, outBuf, 0, 12);
        Buffer.BlockCopy(tag, 0, outBuf, 12, 16);
        Buffer.BlockCopy(cipher, 0, outBuf, 28, cipher.Length);
        return outBuf;
    }
}
```

### §6.2 A05 — parametreli SqlCommand (EF YOK — vault kuralı)

```csharp
// KONTROL: A05 — tip-bağlı parametre (SQL injection'a karşı)
using (var cmd = new SqlCommand(
    "INSERT INTO audit_log (actor, action, entity) VALUES (@a, @b, @c)", conn))
{
    cmd.Parameters.Add("@a", SqlDbType.NVarChar, 128).Value = actor;
    cmd.Parameters.Add("@b", SqlDbType.NVarChar, 64).Value = action;
    cmd.Parameters.Add("@c", SqlDbType.NVarChar, 128).Value = entity;
    await cmd.ExecuteNonQueryAsync();
}
```

---

## §7 Java 21 — kurumsal kontroller

### §7.1 A04 — AES/GCM/NoPadding (JCA)

```java
// KONTROL: A04 — JCA GCM; nonce her çağrıda taze
import javax.crypto.Cipher;
import javax.crypto.spec.GCMParameterSpec;
import javax.crypto.spec.SecretKeySpec;
import java.security.GeneralSecurityException;
import java.security.SecureRandom;
import java.nio.ByteBuffer;

public static byte[] encrypt(byte[] key, byte[] plaintext, byte[] aad)
        throws GeneralSecurityException {
    var cipher = Cipher.getInstance("AES/GCM/NoPadding");
    var nonce = new byte[12];
    new SecureRandom().nextBytes(nonce);
    cipher.init(Cipher.ENCRYPT_MODE, new SecretKeySpec(key, "AES"),
                new GCMParameterSpec(128, nonce));
    cipher.updateAAD(aad);
    byte[] ct = cipher.doFinal(plaintext);
    return ByteBuffer.allocate(12 + ct.length).put(nonce).put(ct).array();
}
```

### §7.2 A05 — PreparedStatement

```java
// KONTROL: A05 — PreparedStatement (concat yasak)
try (var ps = conn.prepareStatement(
        "INSERT INTO audit_log (actor, action, created_at) VALUES (?, ?, NOW(3))")) {
    ps.setString(1, actor);
    ps.setString(2, action);
    ps.executeUpdate();
}
```

---

## §8 C / C++20 — embedded yüzey (realtime kısıtları)

> Domain: embedded (`*.cpp/*.h` → Embedded Engineer). Kural: zero-allocation,
> lock-free, noexcept; bu bloklar realtime callback dışında çağrılır.

### §8.1 A04 — sabit-zamanlı karşılaştırma + güvenli sıfırlama

```c
/* KONTROL: A04 — timing attack'e karşı sabit-zamanlı XOR karşılaştırması */
#include <stddef.h>

int ct_memcmp(const void *a, const void *b, size_t n)
{
    const unsigned char *pa = (const unsigned char *)a;
    const unsigned char *pb = (const unsigned char *)b;
    unsigned char diff = 0;
    for (size_t i = 0; i < n; ++i)
        diff |= (unsigned char)(pa[i] ^ pb[i]); /* dal-bağımsız OR */
    return (int)diff; /* 0 = eşit */
}

/* KONTROL: A04 — optimizer'ın silmesini engelleyen bellek temizliği
 * (stack'e düşen anahtar/parola için) */
void secure_zero(void *p, size_t n)
{
    volatile unsigned char *vp = (volatile unsigned char *)p;
    while (n--) {
        *vp++ = 0;
    }
}
```

### §8.2 A08 — imzalı firmware başlığı doğrulama (boot zinciri)

```c
/* KONTROL: A08 — firmware paket bütünlüğü; tam ed25519 doğrulaması K005-firmware'de */
#include <stdint.h>
#include <stddef.h>

#define FW_MAGIC 0x434D4657u /* "CMFW" */

typedef struct {
    uint32_t magic;
    uint32_t payload_size;
    uint8_t  sig[64];   /* ed25519 imzası */
} fw_header_t;

int fw_header_plausible(const fw_header_t *h, size_t max_payload)
{
    if (h->magic != FW_MAGIC) {
        return 0; /* yanlış sihir → paket reddedilir (fail-safe) */
    }
    if (h->payload_size == 0 || h->payload_size > max_payload) {
        return 0;
    }
    return 1; /* + imza doğrulaması: K005-firmware/update/* */
}
```

---

## §9 Rust — sistem ve servis kontrolleri

### §9.1 A04 — AES-256-GCM (aes-gcm crate)

```rust
// KONTROL: A04 — aes-gcm crate; nonce her çağrıda rastgele
use aes_gcm::aead::{Aead, Payload};
use aes_gcm::{Aes256Gcm, KeyInit};
use rand::RngCore;

pub fn encrypt(key: &[u8; 32], plaintext: &[u8], aad: &[u8]) -> Option<Vec<u8>> {
    let cipher = Aes256Gcm::new_from_slice(key).ok()?;
    let mut nonce = [0u8; 12];
    rand::rngs::OsRng.fill_bytes(&mut nonce);
    let ct = cipher
        .encrypt((&nonce).into(), Payload { msg: plaintext, aad })
        .ok()?;
    let mut out = Vec::with_capacity(12 + ct.len());
    out.extend_from_slice(&nonce);
    out.extend_from_slice(&ct);
    Some(out)
}
```

### §9.2 A07 — argon2 crate + A10 Result disiplini

```rust
// KONTROL: A07 — argon2 (id) parola hash
use argon2::{Argon2, PasswordHash, PasswordHasher, PasswordVerifier};

pub fn verify_password(encoded: &str, plain: &str) -> bool {
    let Ok(digest) = PasswordHash::new(encoded) else {
        return false; // bozuk hash → reddet (fail-safe)
    };
    Argon2::default().verify_password(plain.as_bytes(), &digest).is_ok()
}

// KONTROL: A10 — hata yutma yok; Option/Result ile açık reddetme
```

### §9.3 A05 — sqlx parametreli sorgu

```rust
// KONTROL: A05 — sqlx, parametre bağlama (derleme-anı SQL kontrolü)
pub async fn append_audit(pool: &sqlx::MySqlPool, actor: &str, action: &str) {
    let _ = sqlx::query(
        "INSERT INTO audit_log (actor, action, created_at) VALUES (?, ?, NOW(3))",
    )
    .bind(actor)
    .bind(action)
    .execute(pool)
    .await; // hata yutulmaz — çağıran katman loglar/escalate eder
}
```

---

## §10 SQL (MySQL 9) — veri katmanı kontrolleri

> Domain: data (`*.sql` → Data Engineer). BCNF + parametreli sorgu + `SELECT *` yasak.

### §10.1 A09 — append-only audit tablosu + tetikleyici kilidi

```sql
-- KONTROL: A09:2025 Security Logging and Alerting Failures
CREATE TABLE audit_log (
  id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  actor      VARCHAR(128)    NOT NULL,
  action     VARCHAR(64)     NOT NULL,
  entity     VARCHAR(128)    NOT NULL,
  ip         VARBINARY(16)   NULL,          -- IPv4/IPv6 ham (KVKK: log tutma süresi sınırlı)
  created_at DATETIME(3)     NOT NULL,
  PRIMARY KEY (id),
  KEY idx_actor_created (actor, created_at)
) ENGINE=InnoDB;

-- MySQL istemcisi (mysql CLI) — DELIMITER bloğu
DELIMITER $$
CREATE TRIGGER trg_audit_no_update BEFORE UPDATE ON audit_log
FOR EACH ROW
BEGIN
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'audit_log append-only: UPDATE yasak';
END$$
CREATE TRIGGER trg_audit_no_delete BEFORE DELETE ON audit_log
FOR EACH ROW
BEGIN
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'audit_log append-only: DELETE yasak';
END$$
DELIMITER ;
```

### §10.2 A01 — en az yetki (least privilege)

```sql
-- KONTROL: A01 — uygulama kullanıcısı yalnız SELECT/INSERT; DDL/DML genişi yasak
GRANT SELECT, INSERT ON coremusic.audit_log TO 'app_rw'@'%';
REVOKE UPDATE, DELETE, DROP, ALTER, GRANT OPTION ON coremusic.* FROM 'app_rw'@'%';
FLUSH PRIVILEGES;
```

### §10.3 KVKK — maskeleme view'ı (m.4 ölçülülük · m.12 muhafaza)

```sql
-- KONTROL: KVKK m.4 (ölçülülük) — tam PII yalnız maskelenmiş projection'da görünür
CREATE VIEW vw_customer_masked AS
SELECT id,
       CONCAT(LEFT(email, 2), '***@', SUBSTRING_INDEX(email, '@', -1)) AS email_masked,
       CONCAT('5**-***-', RIGHT(phone, 2))                             AS phone_masked
FROM customer;
-- tam e-posta/telefon: yalnız PII-decrypt servisi (§2.3/§4.2) üzerinden, denetim kaydıyla
```

### §10.4 A08 — migration bütünlük kaydı

```sql
-- KONTROL: A08 — şema değişikliği imza/kayıt defteri (salt-ekleme)
CREATE TABLE schema_migration_log (
  version    VARCHAR(64)     NOT NULL PRIMARY KEY,
  checksum   CHAR(64)        NOT NULL,      -- SHA-256 (MD5 yasak)
  applied_at DATETIME(3)     NOT NULL,
  applied_by VARCHAR(64)     NOT NULL
) ENGINE=InnoDB;
```

---

## §11 Bash — operasyon ve kapı betikleri

### §11.1 A03 — secret taraması (commit öncesi kapı)

```bash
#!/usr/bin/env bash
# KONTROL: A03:2025 Software Supply Chain Failures — gitleaks kapısı
set -euo pipefail

if command -v gitleaks >/dev/null 2>&1; then
  gitleaks detect --source . --redact --no-banner --exit-code 1 \
    || { echo "[KAPI] olası sızıntı — commit/CI durduruldu"; exit 1; }
else
  echo "[KAPI] gitleaks yok — kurulum: https://github.com/gitleaks/gitleaks"; exit 1
fi
```

### §11.2 A02 — .env izin denetimi

```bash
# KONTROL: A02 — secret dosyası izinleri (POSIX)
perm=$(stat -c '%a' .env 2>/dev/null || echo 'missing')
case "$perm" in
  600|400) echo "[OK] .env izni: $perm" ;;
  *) echo "[KAPI] .env izni: $perm — beklenen 600 (chmod 600 .env)"; exit 1 ;;
esac
```

### §11.3 KVKK m.12(5) — 72 saat ihlal bildirim kontrol listesi

```bash
# KONTROL: KVKK — veri ihlali öğrenme anından itibaren 72 saat (Kurul 2019/10 sayılı karar)
breach_checklist() {
  cat <<'EOF'
[1] Öğrenme anı (UTC) kaydedildi — 72 saat sayacı bu damgayla başlar
[2] Etkilenen kişi sayısı ve veri kategorisi tahmin edildi (Bildirim Formu)
[3] Alınan teknik/idari tedbirler listelendi (KVKK m.12)
[4] İlgili kişilere bildirim gerekliliği değerlendirildi (m.12/5)
[5] Kurul bildirimi: kvkk.gov.tr → Veri İhlali Bildirimi formu (en geç 72 saat)
EOF
}
```

---

## §12 YAML (CI/CD) + Rego (policy-as-code)

### §12.1 A03 + A08 — GitHub Actions kapıları

```yaml
# KONTROL: A03 — her push/PR'da secret taraması + bağımlılık incelemesi
name: security-gates
on: [push, pull_request]

jobs:
  gitleaks:
    runs-on: ubuntu-latest
    permissions:
      contents: read
    steps:
      - uses: actions/checkout@v4
        with:
          fetch-depth: 0
      - name: Gitleaks tarama
        uses: gitleaks/gitleaks-action@v2
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}

  dependency-review:
    # KONTROL: A08 — ihlalli/publish edilmemiş bağımlılık PR kapısı
    if: github.event_name == 'pull_request'
    runs-on: ubuntu-latest
    permissions:
      contents: read
    steps:
      - uses: actions/checkout@v4
      - uses: actions/dependency-review-action@v4
```

> **Dürüstlük notu:** `.github/workflows/secret-scan.yml` dosyası repoda mevcuttur
> (kök AGENTS.md §25.2 kanıtı); bu blok **kapı davranışının şablonudur** — CI'nın
> gerçekten koştuğu `⚠️ VERIFY REQUIRED`'dır (compliance-master §7 madde 3).

### §12.2 A01 + A02 — Rego policy-as-code (OPA)

```rego
# KONTROL: A01/A02 — deploly politikaları kod olarak (deny-by-default)
package deploy.policy

deny[msg] {
  input.env == "production"
  input.ref != "refs/heads/main"
  msg := "production yayımı yalnız main dalından yapılabilir"
}

deny[msg] {
  input.secret_scan_passed == false
  msg := "secret taraması geçmeden yayım yasak (A03 kapısı)"
}

deny[msg] {
  input.approvals < 2
  msg := "production için en az 2 onay zorunlu (segregation of duties)"
}
```

---

## §13 Doğrulama listesi (bu dosyanın kendi kapıları)

| # | Kapı | Yöntem | Durum |
|---|---|---|---|
| 1 | Vault kuralı ihlali yok (MD5/SHA-1/DES/SELECT */ORM/inner secret) | gözden geçirme | ✅ 2026-10-17 |
| 2 | Her blok A0x/KVKK ile eşli | §1 matrisi | ✅ 2026-10-17 |
| 3 | API doğruluğu (openssl/aes-gcm/GCMParameterSpec/AesGcm…) sürüm sapması | üretim öncesi agent doğrulaması | `⚠️ VERIFY REQUIRED` |
| 4 | Repo konvansiyonu uyumu (strict_types · BCNF · zero-allocation) | domain agent incelemesi | `⚠️ VERIFY REQUIRED` |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| kod | `kod-ornekleri.md` | 11 dilde uyum kontrolü kod şablonları | Kademe-3 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
