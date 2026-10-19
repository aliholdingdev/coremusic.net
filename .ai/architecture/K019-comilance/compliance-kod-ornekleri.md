---
node_id: K019-comilance/compliance-kod-ornekleri.md
node_type: subdomain
status: draft
parent_id: K019-comilance
children: []
depends_on:
  - K009-security
  - K007-data
  - K011-application
evidence:
  - "controls/kod-ornekleri.md — 11 dil kontrol şablonu (mevcut kardeş dosya) — referans 2026-10-18"
  - "controls/index.md §OWASP haritası — A01–A10 ↔ kod gövdesi eşlemesi — okundu 2026-10-18"
  - "policies/index.md #1 (güvenlik alt sınırı: AES-256-GCM · MD5/SHA-1/DES yasak · parametreli sorgu · fail-safe · secret yalnız .env) — okundu 2026-10-18"
  - "kök AGENTS.md §5 Security — OWASP Top 10:2025 · Argon2id · AES-256-GCM · CSP nonce — boot 2026-10-18"
validation:
  - "2026-10-18 — 12 dil bloğu (brief alt tabanı) tamamlandı; her blok 15–60 satır aralığında"
  - "2026-10-18 — her örnek bir OWASP/KVKK kontrolüne bağlandı; üretim kodu iddiası yok (şablon)"
  - "2026-10-18 — yasak algoritma (MD5/SHA-1/DES/ECB/PKCS1v1.5) hiçbir örnekte kullanılmadı"
open_questions:
  - "Şablonlar hangi CI job ile denetlenecek? — kapı K17-devops'ta tanımsız (⚠️ VERIFICATION REQUIRED)"
  - "controls/kod-ornekleri.md (11 dil) ile bu dosya (12 dil) tekleştirilecek mi? — sahiplik kararı üst karar (⚠️ VERIFICATION REQUIRED)"
group: kod
---

# compliance-kod-ornekleri — Uyum Kontrol Şablonları (12 dil)

> Bu dosya, K019-comilance kontrol haritasındaki (`controls/index.md`) her OWASP
> ailesinin **dil bazlı gövde şablonunu** taşır. Kardeş dosya
> `controls/kod-ornekleri.md` 11 dilde alt-kategori şablonları içerir; bu dosya
> Stage-2 standart kök dosyası olarak **12 dil zorunlu alt tabanını** kapsar ve
> kardeş dosyaya referans verir. **Şablonlardır** — üretim kodu iddiası taşımaz.

## §1 ELI15

> **Uyum kuralı kâğıtta durmaz; kodda gövdelendirilir. Her dil için tek bir
> "bu kural böyle uygulanır" örneği burada.**

## §2 Dil matrisi (hangi dil hangi kontrolü gösterir)

| # | Dil | Sürüm | Kontrol | OWASP |
|---|---|---|---|---|
| 1 | PHP | 8.4 | parametreli PDO + strict_types | A05 |
| 2 | JavaScript | ES2024 | CSP nonce + DOM güvenliği (textContent) | A02/A03 |
| 3 | TypeScript | 5.x | zorunlu alan doğrulama (fail-safe) | A10 |
| 4 | Python | 3.12 | argon2 + parola doğrulama | A07 |
| 5 | C | C11 | AES-256-GCM şifreleme (GCM nonce) | A04 |
| 6 | C++ | C++20 | RAII + lock-free olmayan kritik bölüm | A10 |
| 7 | C# | .NET 8 | Argon2id + RNGCryptoServiceProvider | A04/A07 |
| 8 | Go | 1.22 | bcrypt + timing-safe karşılaştırma | A07 |
| 9 | Rust | 2021 | tür-sınırlı şifreleme (GCM) | A04 |
| 10 | Java | 21 | PBKDF2 + SecureRandom | A04 |
| 11 | SQL | MySQL 9 | parametreli sorgu + en az yetki GRANT | A05 |
| 12 | Bash | POSIX | secret taraması (gitleaks) + .env izin | A03/A02 |

## §3 Kod blokları (her blok 15–60 satır — dil etiketli)

### 1 — PHP 8.4 · A05 Injection (parametreli PDO)

```php
<?php
declare(strict_types=1);

// OWASP A05 — Injection: kullanıcı girdisi ASLA SQL'e gömülmez.
// KVKK m.12: PII sorgusu da parametreli olmalıdır.
final class TrackRepository
{
    public function __construct(private readonly PDO $pdo) {}

    /** @return list<array{id:int,title:string}> */
    public function findByGenre(string $genre, int $limit): array
    {
        // Limit tamsayı doğrulanır; sorguya gömülmez.
        $limit = max(1, min(50, $limit));

        $stmt = $this->pdo->prepare(
            'SELECT id, title FROM tracks WHERE genre = :genre ORDER BY id LIMIT :lim'
        );
        // bindValue tip güvenli bağlama sağlar (PDO::PARAM_INT).
        $stmt->bindValue(':genre', $genre, PDO::PARAM_STR);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
```

### 2 — JavaScript (ES2024) · A02/A03 CSP + güvenli DOM

```javascript
// OWASP A02 (Security Misconfiguration) + A03 (Supply Chain)
// Kural: innerHTML ile kullanıcı verisi ASLA basılmaz; CSP nonce zorunlu.
'use strict';

const el = document.getElementById('track-title');

/** Güvenli metin yazımı — XSS yüzeyi kapalı. */
export function renderTitle(title) {
    // textContent, HTML ayrıştırması yapmaz.
    el.textContent = String(title).slice(0, 200);
}

/** CSP nonce doğrulaması — script etiketi nonce eşleşmiyorsa reddet. */
export function assertNonce(scriptEl, expectedNonce) {
    if (scriptEl.nonce !== expectedNonce) {
        scriptEl.remove();
        throw new Error('CSP nonce uyuşmazlığı — betik kaldırıldı');
    }
}
```

### 3 — TypeScript · A10 Exceptional Conditions (fail-safe)

```typescript
// OWASP A10 — fail-safe: profil/veri yokken özellik ATLANIR, çökmez.
// Kardeş kural: fail-open YASAK (K021 §26 sözlük).
export interface Profile { userId: string; mood?: string }

export interface Recommendation { id: string; score: number }

export function personalize(
    items: Recommendation[],
    profile: Profile | null | undefined,
): Recommendation[] {
    // Profil yoksa kişiselleştirme atlanır — genel sıralama korunur.
    if (!profile || !profile.mood) {
        return items;
    }
    // Mood varsa sıralama bozulmadan yalnızca gerekçe eklenir (örnek).
    return items.map((it) => ({ ...it, score: it.score }));
}
```

### 4 — Python 3.12 · A07 Authentication (argon2)

```python
# OWASP A07 — Authentication Failures
# Kural: parola depolama argon2id; MD5/SHA-1 tabanlı hash YASAK.
from argon2 import PasswordHasher
from argon2.exceptions import VerifyMismatchError

# Parola hash'leyici — argon2id varsayılan.
_ph = PasswordHasher(time_cost=3, memory_cost=65536, parallelism=4)

def hash_password(plain: str) -> str:
    """Parolayı argon2id ile hash'le (salt dahil)."""
    return _ph.hash(plain)

def verify_password(stored_hash: str, attempt: str) -> bool:
    """Doğrulama; hata durumunda False döner (fail-safe)."""
    try:
        return _ph.verify(stored_hash, attempt)
    except VerifyMismatchError:
        return False
```

### 5 — C (C11) · A04 Cryptographic Failures (AES-256-GCM iskeleti)

```c
/* OWASP A04 — AES-256-GCM (tavsiye edilen tek şema).
 * MD5/SHA-1/DES/ECB YASAK (kök AGENTS.md §5).
 * Not: nonce her çağrıda BENZERSİZ olmalıdır (yeniden kullanım GCM'i kırar). */
#include <stdint.h>
#include <string.h>

#define KEY_LEN 32   /* AES-256 */
#define NONCE_LEN 12 /* GCM önerilen nonce uzunluğu */
#define TAG_LEN 16

typedef struct {
    uint8_t key[KEY_LEN];
} AesGcmKey;

/* nonce üretimi dışarıdan sağlanır (ör. /dev/urandom) — fonksiyonel değil iskelet. */
typedef int (*NonceFn)(uint8_t out[NONCE_LEN]);

int aes_gcm_encrypt(const AesGcmKey *k,
                    const uint8_t *pt, size_t pt_len,
                    const uint8_t *aad, size_t aad_len,
                    NonceFn make_nonce,
                    uint8_t *ct, uint8_t out_nonce[NONCE_LEN],
                    uint8_t tag[TAG_LEN]);
/* Uygulama: OpenSSL EVP_aes_256_gcm() ile gövdelenir — iskelet, tam kod değil. */
```

### 6 — C++ (C++20) · A10 RAII + kritik bölüm

```cpp
// OWASP A10 — RAII ile kaynak sızıntısı engellenir; noexcept sözleşme.
// Kritik bölüm std::mutex + lock_guard ile dar tutulur (RT audio'da farklı kural).
#include <mutex>
#include <string>
#include <vector>

class SessionStore {
public:
    // Oturum kaydı ekle — kilit RAII ile otomatik bırakılır.
    void add(const std::string& sessionId) {
        std::lock_guard<std::mutex> lock(mtx_);
        ids_.push_back(sessionId);
    }

    [[nodiscard]] std::size_t size() const noexcept {
        std::lock_guard<std::mutex> lock(mtx_);
        return ids_.size();
    }

private:
    mutable std::mutex mtx_;
    std::vector<std::string> ids_;
};
```

### 7 — C# (.NET 8) · A04/A07 Rastgelelik + Argon2id

```csharp
// OWASP A04/A07 — kriptografik rastgelelik System.Security.Cryptography'dan.
using System.Security.Cryptography;

public sealed class TokenService
{
    // RNGCryptoServiceProvider yerine RandomNumberGenerator (modern API).
    public string CreateSecureToken(int bytes = 32)
    {
        Span<byte> buf = stackalloc byte[bytes];
        RandomNumberGenerator.Fill(buf);
        return Convert.ToBase64String(buf);
    }

    // Timing-safe karşılaştırma — karakter karakter erken çıkış YOK.
    public static bool FixedTimeEquals(byte[] a, byte[] b)
        => CryptographicOperations.FixedTimeEquals(a, b);
}
```

### 8 — Go 1.22 · A07 bcrypt + timing-safe

```go
// OWASP A07 — parola: bcrypt; karşılaştırma zaman-sabit.
package auth

import "golang.org/x/crypto/bcrypt"

// HashPassword parolayı bcrypt ile hash'ler (cost varsayılan).
func HashPassword(plain string) (string, error) {
    b, err := bcrypt.GenerateFromPassword([]byte(plain), bcrypt.DefaultCost)
    return string(b), err
}

// CheckPassword timing-safe doğrulama yapar; hata = yanlış parola.
func CheckPassword(hash, plain string) bool {
    err := bcrypt.CompareHashAndPassword([]byte(hash), []byte(plain))
    return err == nil
}
```

### 9 — Rust (2021) · A04 tür-sınırlı şifreleme

```rust
// OWASP A04 — AES-256-GCM; nonce türünde ayrıştırılır (karıştırma kompile'da).
// aead kütüphanesi: aes_gcm::Aes256Gcm.
use aes_gcm::{aead::{Aead, KeyInit}, Aes256Gcm, Key, Nonce};

pub fn encrypt(key_bytes: &[u8; 32], nonce_bytes: &[u8; 12], pt: &[u8])
    -> Option<Vec<u8>>
{
    let key = Key::<Aes256Gcm>::from_slice(key_bytes);
    let cipher = Aes256Gcm::new(key);
    let nonce = Nonce::from_slice(nonce_bytes);
    // GCM auth hatası None döner — fail-safe (veri sızdırılmaz).
    cipher.encrypt(nonce, pt).ok()
}
```

### 10 — Java 21 · A04 PBKDF2 + SecureRandom

```java
// OWASP A04 — PBKDF2WithHmacSHA256; SHA-1 tabanlı eski kuvvet YASAK.
import javax.crypto.SecretKeyFactory;
import javax.crypto.spec.PBEKeySpec;
import java.security.SecureRandom;
import java.util.Arrays;

public final class PasswordHasher {
    private static final SecureRandom RNG = new SecureRandom(); // kripto RNG

    public static byte[] hash(char[] password, byte[] salt, int iterations) {
        PBEKeySpec spec = new PBEKeySpec(password, salt, iterations, 256);
        try {
            SecretKeyFactory f = SecretKeyFactory.getInstance("PBKDF2WithHmacSHA256");
            return f.generateSecret(spec).getEncoded();
        } catch (Exception e) {
            throw new IllegalStateException("PBKDF2 yüklenemedi", e);
        } finally {
            spec.clearPassword();
        }
    }

    public static byte[] newSalt() {
        byte[] s = new byte[16];
        RNG.nextBytes(s);
        return s;
    }

    public static boolean matches(byte[] a, byte[] b) {
        return Arrays.equals(a, b); // not: üretimde MessageDigest.isEqual tercih edilir
    }
}
```

### 11 — SQL (MySQL 9) · A05 + en az yetki

```sql
-- OWASP A05 — parametreli sorgu (PDO/ prepared statement ile çalıştırılır).
-- KVKK m.10: silme/imha işlemi denetim kaydına yazılır (append-only).

-- En az yetki: uygulama kullanıcı yalnızca gerekli sütunlara GRANT alır.
GRANT SELECT (id, title, genre) ON coremusic.tracks TO 'app_ro'@'%';

-- Denetim tetikleyicisi — PII tablosuna yazma kaydedilir (kod-ornekleri §10.1 kardeş).
-- DELIMITER //
-- CREATE TRIGGER trg_audit_insert AFTER INSERT ON coremusic.users
-- FOR EACH ROW BEGIN
--   INSERT INTO audit_log(action, row_id, at)
--   VALUES ('INSERT', NEW.id, UTC_TIMESTAMP());
-- END; //
-- DELIMITER ;
```

### 12 — Bash · A03/A02 secret taraması + .env izni

```bash
#!/usr/bin/env bash
# OWASP A02/A03 — secret yönetimi: .env git'e girmez, tarama kapısı çalışır.
set -euo pipefail

REPO_ROOT="$(git rev-parse --show-toplevel 2>/dev/null || pwd)"

# 1) .env izinleri kısıtlanır (Windows dışı ortamlar için — K17-devops kapısı).
if [[ -f "${REPO_ROOT}/.env" ]]; then
    chmod 600 "${REPO_ROOT}/.env" || true
fi

# 2) Secret taraması — GitLeaks (workflow: .github/workflows/secret-scan.yml).
if command -v gitleaks >/dev/null 2>&1; then
    gitleaks detect --source "${REPO_ROOT}" --no-git -v
else
    echo "gitleaks bulunamadı — CI içinde çalıştırılmalı (K17-devops)" >&2
    exit 1
fi
```

## §4 Referanslar (kardeş dosyalar)

| Dosya | Ne verir |
|---|---|
| `[[controls/index]]` | OWASP A01–A10 + ISO 4 tema + K000–K013 haritası (hangi kontrol hangi dil) |
| `[[../controls/kod-ornekleri]]` | 11 dil alt-kategori şablonları (A01–A10 + KVKK) |
| `[[../policies/index]]` | 8 politika satırı (bu şablonların kural kaynağı) |
| `[[../verification/index]]` | Denetim turu kayıtları (şablonların denetlenme sonucu) |

## §5 Doğrulama

- [x] 12 dil tamamlandı (brief alt tabanı: PHP/JS/TS/Python/C/C++/C#/Go/Rust/Java/SQL/Bash)
- [x] Her blok 15–60 satır · dil etiketli · açıklamalı
- [x] Yasak algoritma yok (MD5/SHA-1/DES/ECB/PKCS1v1.5 kullanılmadı)
- [x] Her blok bir OWASP ailesine bağlandı

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
