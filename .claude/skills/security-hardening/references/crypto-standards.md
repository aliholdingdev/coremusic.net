# Kripto & Kimlik Standartları — AES-256-GCM · Argon2id · Hibrit Auth · RBAC · Session

> Kaynak: `.ai/CLAUDE.md` §12 (Encryption: AES-256-GCM, Argon2id — NIST SP 800-38D),
> §6/#5/#8/#9 (session/auth/Permission), §17/§21 · ADR-010 (hibrit auth + Bearer sınırı),
> ADR-011 (session kuralları), ADR-022 (PII şifreleme + argon2id).

## 1. Şifreleme Standartları

| Öğe | Standart | Kural |
|-----|----------|-------|
| Veri şifreleme (PII, token) | **AES-256-GCM** (authenticated encryption — NIST SP 800-38D) | Nonce/IV **benzersiz** (12 bayt), tag 16 bayt; paket `iv‖tag‖ciphertext` (OAuthManager deseni, ADR-022 §2.2c) |
| PII alanları (ad/telefon/adres/ödeme) | AES-256-GCM app-level | Aranan hassas alan (email) → normalize + **blind index** (HMAC) — sıralama/kısmi eşleşme yapılmaz (ADR-022) |
| Şifre hashing | **Argon2id** (`password_hash(..., PASSWORD_ARGON2ID)`) + pepper (`APP_PEPPER`) | bcrypt yasak/reddedilir (2025-26 OWASP tercihi — ADR-022 §1.3); doğrulama `password_verify` |
| Anahtar saklama | `.env` → DI `Config` servisi (ADR-015) | Kodda hardcode secret / `getenv()` ihlali yasak (§21); REDACTED — anahtar hiçbir dokümana yazılmaz |

## 2. Hibrit Auth (JWT + Session)

```text
Birincil: HTTPOnly session cookie (PHP oturum = SSOT) → Anlık logout/iptal mümkün
API    : Bearer JWT (kısa ömürlü access ≤15 dk + rotasyonlu refresh — ADR-011 hibrit)
```

- **Auth middleware sırası:** önce session/cookie denenir, sonra `Authorization: Bearer` (ADR-010 §1.1).
- **Bearer muafiyet sınırı:** yalnızca auth mekanizması **tekil Bearer** olan uçlar CSRF'e
  muafdır (tarayıcı `Authorization` header'ını asla otomatik göndermez); hibrit/cookie
  fallback'li her uçta **`csrf_token` zorunludur** (ADR-010 §2.2b).
- ⚠️ VERIFICATION REQUIRED: `validateJwtToken` stub'unun gerçek imza doğrulamasına
  bağlanıp bağlanmadığı kodda teyit edilmeden Bearer muafiyeti geçerli sayılmaz (ADR-010
  §1.1 — stub `null` döner).

## 3. RBAC Roller Permission Middleware'de (#9)

| Rol | Not |
|-----|-----|
| `regular` | Temel kullanıcı |
| `premium` | Ücretli katman |
| `studio` | Stüdyo yetkileri |
| `car` | Araç içi |
| `admin` | Yönetim — en geniş ama en az yetki prensibiyle |
| `system` | Sistem içi |

Rol/değişiklik = **privilege change** → oturum + CSRF token yenileme gerektirir (ASVS 7.2.4
— ADR-011 §2.2c kural 3, ADR-010 §2.2c rotasyon 2).

## 4. Session Kuralları (ADR-011)

| Kural | Değer |
|-------|-------|
| Timeout (idle) | **3600 sn** (60 dk boştalık — CLAUDE §6 tablosu); ADR-011 hedef düzeltmesi: idle 30 dk / absolute 8 saat (absolute asla idle'dan kısa olmaz) |
| Cookie | **HTTPOnly** + `SameSite=Lax` + HTTPS'te `Secure` + `domain=.coremusic.net` (subdomain-geniş — ADR-004) |
| Kimlik | CSPRNG ≥128 bit; login'de `session_regenerate_id(true)` (fixation) |
| Rotasyon | Zamanlanmış rotasyon (30 dk) + privilege change'de yenileme |
| Depolama | Sunucu tarafı oturum = SSOT (stateless JWT tek başına oturum olamaz — logout/iptal imkânsız) |
| Bypass | `?_bypass=1` yalnız test ortamı; prod'da devre dışı |
| Yasak | `localStorage`/`sessionStorage` auth için (§21) → session-based HTTPOnly cookie |

⚠️ VERIFICATION REQUIRED: idle/absolute/rotation nihai değerleri (`SessionConfig.php`) kodda
teyit edilmeden ADR-011 hedef değerleri "uygulandı" diye yazılmaz.