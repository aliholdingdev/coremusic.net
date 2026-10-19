---
node_id: K009-security/encryption
node_type: subdomain
status: draft
parent_id: K009-security
children:
  - encryption-index
  - encryption-anayasa
  - encryption-surucu
  - encryption-ornekler
  - encryption-kapsam
  - encryption-dogrulama
  - encryption-bilesenler
  - encryption-web-arastirma
depends_on:
  - K009-security/secrets
  - K009-security/security-master
evidence:
  - "v2 §2.9 satır 323 (ad) — kod: JwtService, SecurityHelper, CsrfMiddleware (imza)"
  - "shared/src/Security/JwtService.php · shared/src/Security/SecurityHelper.php (56 satır)"
validation:
  - "2026-10-13 — frontmatter 9 alan + group, `parent:` yok, index 40–100 satır"
  - "2026-10-13 — URL tablosu HTTP 200 (FIPS 197 / RFC 5116 / 5869 / 8439 / 9106), envanter kesişimi 0"
open_questions:
  - "AES-256-GCM uygulamasının repo karşılığı okunmadı ⚠️ VERIFICATION REQUIRED"
  - "Anahtar rotasyon periyodu belgelenmemiş ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K009 · çocuk · encryption (Şifreleme ve Kriptografik Koruma)

> **Zorunlu:** AES-256-GCM / ChaCha20-Poly1305 · Argon2id · MD5/SHA-1/DES **yasak** (`.ai/CLAUDE.md` §Security).

## §1 ROL (ELI10)

| Katman | Algoritma | Ne korur |
|---|---|---|
| Parola saklama | Argon2id + pepper (HMAC-SHA-256) | Veritabanı sızıntısında kaba kuvvet |
| Simetrik veri | AES-256-GCM (FIPS 197) | Gizlilik + bütünlük, nonce zorunlu |
| Anahtar türetme | HKDF (RFC 5869) | Tek gizliden çok amaçlı anahtar |
| Jeton imzası | RS256/ES256 (RFC 7515–7518) | Jeton sahteciliği |
| İstek imzası | HMAC-SHA-256 (hash_hmac) | CSRF/istek bütünlüğü |
| İletim | TLS 1.3 (RFC 8446 envanterde) | Kanal güvenliği |

## §2 KAPSAM ÖZETİ

| Kalem | Durum | Kanıt tipi |
|---|---|---|
| Parola hash'leme (Argon2id + pepper) | var | kod (Password.php) |
| Jeton imzalama (JwtService) | var | kod |
| Doğrulama-süresi-sabitleme (hash_equals) | var | kod |
| AES-256-GCM veri şifreleme | kanıt yok | ⚠️ VERIFICATION REQUIRED |
| Anahtar rotasyonu / lifecycle | yok | ⚠️ VERIFICATION REQUIRED |
| TLS yalnız (HSTS) | kısmi | .htaccess başlıkları |
| TLS sürüm/paket kısıtı | OS/ sunucu | `K000-isletim-sistemi` kapsam dışı |

## §3 DOSYA HARİTASI

| Dosya | group | Hedef | İçerik |
|---|---|---|---|
| `encryption/index.md` | mimari | 40–100 | bu künye |
| `encryption/encryption-anayasa.md` | mimari | ≥500 | algoritma yasak/izin listesi |
| `encryption/encryption-surucu.md` | mimari | ≥500 | anahtar yaşam döngüsü, şifreleme akışı |
| `encryption/encryption-ornekler.md` | kod | ≥500 | gerçek PHP alıntıları (Dosya/Satır) |
| `encryption/encryption-kapsam.md` | mimari | ≥500 | içeri-dışı, sınır matrisi |
| `encryption/encryption-dogrulama.md` | kanit | ≥500 | kontrol + iddia/URL tablosu |
| `encryption/encryption-bilesenler.md` | mimari | ≥500 | kademe-3 bileşen planı |
| `encryption/encryption-web-arastirma.md` | kanit | ≥500 | FIPS / NIST / RFC kaydı |

## §4 SINIR — KAPSAM DIŞI

| Konu | Sahibi | Bu çocukta |
|---|---|---|
| Kimlik deposu / credentials kasası | `K0100-identitiy` | Yalnız sınır referansı |
| OS kripto sağlayıcısı, disk şifreleme, sertifika deposu | `K000-isletim-sistemi/security` | Yalnız sınır referansı |
| Anahtar sırrının saklandığı fiziksel/gizli depo | `secrets` (K009) | K009 içinde ayrı çocuk |

## §5 BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| `secrets` | öncesi | anahtar/gizli kaynağı |
| `session-security` | sonrası | oturum jetonu imzalama |
| `authentication` | sonrası | parola hash doğrulama |
| `K009-security` (parent) | yukarı | domen anayasasına tabi |

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| AES blok şifresi resmi standardı FIPS 197 | https://csrc.nist.gov/pubs/fips/197/final | 2026-10-13 | HTTP 200 |
| AEAD arayüzü ve nonce kuralları RFC 5116 | https://www.rfc-editor.org/rfc/rfc5116 | 2026-10-13 | HTTP 200 |
| Argon2 password hashing RFC 9106 | https://www.rfc-editor.org/rfc/rfc9106 | 2026-10-13 | HTTP 200 |
| ChaCha20-Poly1305 AEAD RFC 8439 | https://www.rfc-editor.org/rfc/rfc8439 | 2026-10-13 | HTTP 200 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
