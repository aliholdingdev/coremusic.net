---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K6 Güvenlik Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-24
last_update_note: "3 turlu agent tartışması"
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# K6: Güvenlik Layer

**Katman:** K6 (Güvenlik)
**Kapsam:** Auth, RBAC, CSRF, CSP, RateLimit, Encryption, Vault, Audit
**Sorumlu Agent:** Security Engineer
**Bileşen Sayısı:** 40

---

## 1. Genel Bakış

K6 katmanı, CoreMusic'in güvenlik altyapısını içerir. OWASP Top 10:2025 uyumlu, katmanlı güvenlik mimarisi sunar.

---

## 2. Bileşen Haritası

| # | Bileşen | Amaç | ADR |
|---|---------|------|-----|
| K6-01 | Auth System | JWT + Session hybrid | ADR-043 |
| K6-02 | RBAC | Rol bazlı erişim | — |
| K6-03 | CSRF | Cross-Site Request Forgery | ADR-010 |
| K6-04 | CSP | Content Security Policy | ADR-012 |
| K6-05 | Rate Limit | APCu tabanlı | ADR-013 |
| K6-06 | Encryption | AES-256-GCM | ADR-022 |
| K6-07 | Credential Vault | Güvenli depolama | ADR-034 |
| K6-08 | Audit Trail | Günlük kaydı | — |

---

## 3. Authentication (K6-01)

### 3.1 JWT + Session Hybrid

```
Login → Validate Credentials → Create Session → Generate JWT → Set HTTPOnly Cookie
                                                                      ↓
Session Data: user_id, role, permissions, device_info
JWT Claims: sub, exp, iat, jti, role
```

### 3.2 Token Struct

```json
{
    "header": {
        "alg": "RS256",
        "typ": "JWT",
        "kid": "key-id-2026"
    },
    "payload": {
        "sub": "user-123",
        "role": "premium",
        "permissions": ["play", "download", "create_playlist"],
        "iat": 1726838400,
        "exp": 1726842000,
        "jti": "unique-token-id"
    }
}
```

---

## 4. CSRF (K6-03)

### 4.1 Token Kuralları

| Kural | Değer |
|-------|-------|
| Token key | `csrf_token` (NOT `_csrf_token`) |
| Doğrulama | `hash_equals()` (timing-safe) |
| Storage | HTTPOnly cookie |
| Yenileme | Her 3600s |

---

## 5. CSP (K6-04)

### 5.1 Policy

```
default-src 'self';
script-src 'self' 'nonce-{{NONCE}}' 'strict-dynamic';
style-src 'self' 'nonce-{{NONCE}}';
img-src 'self' data: https:;
font-src 'self';
connect-src 'self' wss://api.coremusic.net;
media-src 'self';
object-src 'none';
frame-ancestors 'none';
base-uri 'self';
form-action 'self';
```

---

## 6. Encryption (K6-06)

### 6.1 Parametreler

| Parametre | Değer |
|-----------|-------|
| Algorithm | AES-256-GCM |
| Key Size | 256-bit (32 byte) |
| IV Size | 96-bit (12 byte) |
| Tag Size | 16 byte |
| Password Hash | Argon2id |
| Argon2 Memory | 64MB |
| Argon2 Time | 4 iterations |
| Argon2 Threads | 2 |

---

## 7. Rate Limiting (K6-05)

### 7.1 Kurallar

| Kural | Değer |
|-------|-------|
| Storage | APCu |
| Window | 60 saniye |
| Max Request | 60 |
| Eşik | 80% → Warning |
| Aşım | 429 Too Many Requests |

---

## 8. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-010 | csrf_token key zorunlu |
| ADR-012 | strict-dynamic, nonce-based CSP |
| ADR-013 | APCu, 60 req/60s |
| ADR-022 | AES-256-GCM, Argon2id |
| ADR-034 | AES-256-GCM credential vault |
| ADR-043 | Auth subdomain konsolidasyonu |

---

## Alt Katman Şeması (K6.a.b.c)

> **Katman kuralı (K6 · OWASP):** Bu bölüm yalnızca 4. seviye yaprak üretir; her yaprak diskteki bir kanıt satırına bağlanır — MD başlık satırı (##/###), README indeks tablosu satırı veya SQL CREATE TABLE satırı. Kural/soyut başlıklar yaprak sayılmaz.
> **Sayım aritmetiği:** 154 anchor (13 içerik dosyası + index.md + README.md + CLAUDE.md) − 18 dışlama = 136 başlık yaprağı + 14 README tablo satırı = **150 yaprak** · 9 ikinci düğüm · 18 üçüncü düğüm (kat).
> **Dışlama kuralı:** 14 adet "Durum: Implementasyon" başlığı (13 içerik dosyası + index.md) ve CLAUDE.md'nin 4 kural başlığı yaprak sayılmaz; 18 dışlamanın tamamı Kanıt Kataloğu § Dışlamalar tablosunda satır satır listelenir.

| Kod | İkinci Düğüm (K6.a) | 3. Seviye (kat) | 4. Seviye (yaprak) |
|---|---|---|---|
| K6.1 | Authentication & Session | 4 | 36 |
| K6.2 | RBAC Authorization | 1 | 9 |
| K6.3 | CSRF + Input Validation | 2 | 17 |
| K6.4 | CSP + Security Headers | 2 | 18 |
| K6.5 | Rate Limiting | 1 | 9 |
| K6.6 | Encryption + Vault | 2 | 18 |
| K6.7 | Audit Logging | 1 | 9 |
| K6.8 | README (bileşen + ADR) | 4 | 28 |
| K6.9 | OWASP Threat Model + index.md | 1 | 6 |
| **TOPLAM** | **9 düğüm** | **18** | **150** |

### K6.1 — Authentication & Session (36 yaprak)

*Dosyalar: authentication-jwt.md · session-management.md · password-hashing.md · oauth2-integration.md — JWT, oturum, Argon2id ve OAuth2/PKCE kanıtları; her dosya 9 yaprak (Durum başlığı hariç).*

| Kod | Ad | Kanıt (dosya · satır) |
|---|---|---|
| **K6.1.1** | **authentication-jwt.md — JWT Kimlik Doğrulama** | authentication-jwt.md · L10–L210 · 9 yaprak |
| K6.1.1.1 | Genel Bakış | authentication-jwt.md · L10 |
| K6.1.1.2 | Teknik Detaylar | authentication-jwt.md · L14 |
| K6.1.1.3 | Token Yapısı | authentication-jwt.md · L16 |
| K6.1.1.4 | Token Yaşam Döngüsü | authentication-jwt.md · L39 |
| K6.1.1.5 | Token Rotation Stratejisi | authentication-jwt.md · L49 |
| K6.1.1.6 | RS256 Asimetrik Şifreleme | authentication-jwt.md · L53 |
| K6.1.1.7 | Konfigürasyon / Kod | authentication-jwt.md · L57 |
| K6.1.1.8 | Güvenlik Kontrolleri | authentication-jwt.md · L190 |
| K6.1.1.9 | Bağımlılıklar | authentication-jwt.md · L203 |
| **K6.1.2** | **session-management.md — Oturum Yönetimi** | session-management.md · L10–L380 · 9 yaprak |
| K6.1.2.1 | Genel Bakış | session-management.md · L10 |
| K6.1.2.2 | Teknik Detaylar | session-management.md · L14 |
| K6.1.2.3 | Oturum Yaşam Döngüsü | session-management.md · L16 |
| K6.1.2.4 | Cookie Ayarları | session-management.md · L28 |
| K6.1.2.5 | Session Fixing Koruması | session-management.md · L39 |
| K6.1.2.6 | Concurrent Session Control | session-management.md · L46 |
| K6.1.2.7 | Konfigürasyon / Kod | session-management.md · L52 |
| K6.1.2.8 | Güvenlik Kontrolleri | session-management.md · L360 |
| K6.1.2.9 | Bağımlılıklar | session-management.md · L373 |
| **K6.1.3** | **password-hashing.md — Argon2id Parola Hashing** | password-hashing.md · L10–L358 · 9 yaprak |
| K6.1.3.1 | Genel Bakış | password-hashing.md · L10 |
| K6.1.3.2 | Teknik Detaylar | password-hashing.md · L14 |
| K6.1.3.3 | Algoritma Karşılaştırması | password-hashing.md · L16 |
| K6.1.3.4 | Argon2id Parametreleri | password-hashing.md · L27 |
| K6.1.3.5 | Şifre Politikası | password-hashing.md · L38 |
| K6.1.3.6 | Salt Stratejisi | password-hashing.md · L46 |
| K6.1.3.7 | Konfigürasyon / Kod | password-hashing.md · L50 |
| K6.1.3.8 | Güvenlik Kontrolleri | password-hashing.md · L338 |
| K6.1.3.9 | Bağımlılıklar | password-hashing.md · L351 |
| **K6.1.4** | **oauth2-integration.md — OAuth 2.0 + PKCE** | oauth2-integration.md · L10–L498 · 9 yaprak |
| K6.1.4.1 | Genel Bakış | oauth2-integration.md · L10 |
| K6.1.4.2 | Teknik Detaylar | oauth2-integration.md · L14 |
| K6.1.4.3 | OAuth 2.0 Flow (Authorization Code + PKCE) | oauth2-integration.md · L16 |
| K6.1.4.4 | PKCE (Proof Key for Code Exchange) | oauth2-integration.md · L40 |
| K6.1.4.5 | Provider Konfigürasyonu | oauth2-integration.md · L47 |
| K6.1.4.6 | Token Exchange | oauth2-integration.md · L55 |
| K6.1.4.7 | Konfigürasyon / Kod | oauth2-integration.md · L63 |
| K6.1.4.8 | Güvenlik Kontrolleri | oauth2-integration.md · L478 |
| K6.1.4.9 | Bağımlılıklar | oauth2-integration.md · L491 |

**K6.1 Ara Toplam:** ikinci 1 · üçüncü 4 · yaprak 36

### K6.2 — RBAC Authorization (9 yaprak)

*Dosya: rbac-authorization.md — rol hiyerarşisi ve izin matrisi kanıtları; 9 yaprak (Durum başlığı hariç).*

| Kod | Ad | Kanıt (dosya · satır) |
|---|---|---|
| **K6.2.1** | **rbac-authorization.md — Rol Bazlı Erişim Kontrolü** | rbac-authorization.md · L10–L253 · 9 yaprak |
| K6.2.1.1 | Genel Bakış | rbac-authorization.md · L10 |
| K6.2.1.2 | Teknik Detaylar | rbac-authorization.md · L14 |
| K6.2.1.3 | Rol Hiyerarşisi | rbac-authorization.md · L16 |
| K6.2.1.4 | İzin Matrisi | rbac-authorization.md · L40 |
| K6.2.1.5 | Permission Formatı | rbac-authorization.md · L55 |
| K6.2.1.6 | Dinamik İzin Kontrolü | rbac-authorization.md · L67 |
| K6.2.1.7 | Konfigürasyon / Kod | rbac-authorization.md · L74 |
| K6.2.1.8 | Güvenlik Kontrolleri | rbac-authorization.md · L233 |
| K6.2.1.9 | Bağımlılıklar | rbac-authorization.md · L246 |

**K6.2 Ara Toplam:** ikinci 1 · üçüncü 1 · yaprak 9

### K6.3 — CSRF + Input Validation (17 yaprak)

*Dosyalar: csrf-protection.md (9 yaprak) · input-validation.md (8 yaprak — toplam 9 anchor, Durum hariç).*

| Kod | Ad | Kanıt (dosya · satır) |
|---|---|---|
| **K6.3.1** | **csrf-protection.md — CSRF Koruması** | csrf-protection.md · L10–L239 · 9 yaprak |
| K6.3.1.1 | Genel Bakış | csrf-protection.md · L10 |
| K6.3.1.2 | Teknik Detaylar | csrf-protection.md · L14 |
| K6.3.1.3 | CSRF Saldırı Senaryosu | csrf-protection.md · L16 |
| K6.3.1.4 | Koruma Stratejisi: Double Submit Cookie | csrf-protection.md · L26 |
| K6.3.1.5 | Koruma Stratejisi: Synchronizer Token | csrf-protection.md · L36 |
| K6.3.1.6 | SameSite Cookie Politikası | csrf-protection.md · L46 |
| K6.3.1.7 | Konfigürasyon / Kod | csrf-protection.md · L54 |
| K6.3.1.8 | Güvenlik Kontrolleri | csrf-protection.md · L220 |
| K6.3.1.9 | Bağımlılıklar | csrf-protection.md · L233 |
| **K6.3.2** | **input-validation.md — Girdi Doğrulama** | input-validation.md · L10–L460 · 8 yaprak |
| K6.3.2.1 | Genel Bakış | input-validation.md · L10 |
| K6.3.2.2 | Teknik Detaylar | input-validation.md · L14 |
| K6.3.2.3 | Saldırı Vektörleri | input-validation.md · L16 |
| K6.3.2.4 | Validation Seviyeleri | input-validation.md · L29 |
| K6.3.2.5 | Whitelist Yaklaşımı | input-validation.md · L43 |
| K6.3.2.6 | Konfigürasyon / Kod | input-validation.md · L51 |
| K6.3.2.7 | Güvenlik Kontrolleri | input-validation.md · L439 |
| K6.3.2.8 | Bağımlılıklar | input-validation.md · L452 |

**K6.3 Ara Toplam:** ikinci 1 · üçüncü 2 · yaprak 17

### K6.4 — CSP + Security Headers (18 yaprak)

*Dosyalar: csp-policy.md (9 yaprak) · security-headers.md (9 yaprak) — Content Security Policy direktifleri ve HTTP güvenlik başlıkları.*

| Kod | Ad | Kanıt (dosya · satır) |
|---|---|---|
| **K6.4.1** | **csp-policy.md — Content Security Policy** | csp-policy.md · L10–L216 · 9 yaprak |
| K6.4.1.1 | Genel Bakış | csp-policy.md · L10 |
| K6.4.1.2 | Teknik Detaylar | csp-policy.md · L14 |
| K6.4.1.3 | CSP Direktifleri | csp-policy.md · L16 |
| K6.4.1.4 | Nonce Üretimi | csp-policy.md · L39 |
| K6.4.1.5 | CSP Raporlama | csp-policy.md · L43 |
| K6.4.1.6 | Strict-Dynamic | csp-policy.md · L47 |
| K6.4.1.7 | Konfigürasyon / Kod | csp-policy.md · L51 |
| K6.4.1.8 | Güvenlik Kontrolleri | csp-policy.md · L197 |
| K6.4.1.9 | Bağımlılıklar | csp-policy.md · L210 |
| **K6.4.2** | **security-headers.md — HTTP Güvenlik Başlıkları** | security-headers.md · L10–L272 · 9 yaprak |
| K6.4.2.1 | Genel Bakış | security-headers.md · L10 |
| K6.4.2.2 | Teknik Detaylar | security-headers.md · L14 |
| K6.4.2.3 | Güvenlik Başlıkları Matrisi | security-headers.md · L16 |
| K6.4.2.4 | HSTS Preload | security-headers.md · L30 |
| K6.4.2.5 | X-Frame-Options Stratejisi | security-headers.md · L37 |
| K6.4.2.6 | Permissions-Policy | security-headers.md · L43 |
| K6.4.2.7 | Konfigürasyon / Kod | security-headers.md · L52 |
| K6.4.2.8 | Güvenlik Kontrolleri | security-headers.md · L253 |
| K6.4.2.9 | Bağımlılıklar | security-headers.md · L266 |

**K6.4 Ara Toplam:** ikinci 1 · üçüncü 2 · yaprak 18

### K6.5 — Rate Limiting (9 yaprak)

*Dosya: rate-limiting.md — APCu tabanlı istek limiti kanıtları (ADR-013); 9 yaprak (Durum başlığı hariç).*

| Kod | Ad | Kanıt (dosya · satır) |
|---|---|---|
| **K6.5.1** | **rate-limiting.md — Hız Sınırlandırma** | rate-limiting.md · L10–L344 · 9 yaprak |
| K6.5.1.1 | Genel Bakış | rate-limiting.md · L10 |
| K6.5.1.2 | Teknik Detaylar | rate-limiting.md · L14 |
| K6.5.1.3 | Algoritmalar | rate-limiting.md · L16 |
| K6.5.1.4 | Limit Stratejisi | rate-limiting.md · L22 |
| K6.5.1.5 | Response Headers | rate-limiting.md · L39 |
| K6.5.1.6 | Rate Limit Aşımında | rate-limiting.md · L48 |
| K6.5.1.7 | Konfigürasyon / Kod | rate-limiting.md · L55 |
| K6.5.1.8 | Güvenlik Kontrolleri | rate-limiting.md · L325 |
| K6.5.1.9 | Bağımlılıklar | rate-limiting.md · L338 |

**K6.5 Ara Toplam:** ikinci 1 · üçüncü 1 · yaprak 9

### K6.6 — Encryption + Vault (18 yaprak)

*Dosyalar: encryption-aes256.md (9 yaprak) · vault-secrets.md (9 yaprak) — AES-256-GCM/Argon2id ve Vault AppRole/rotation kanıtları.*

| Kod | Ad | Kanıt (dosya · satır) |
|---|---|---|
| **K6.6.1** | **encryption-aes256.md — AES-256-GCM Şifreleme** | encryption-aes256.md · L10–L338 · 9 yaprak |
| K6.6.1.1 | Genel Bakış | encryption-aes256.md · L10 |
| K6.6.1.2 | Teknik Detaylar | encryption-aes256.md · L14 |
| K6.6.1.3 | AES-256-GCM Yapısı | encryption-aes256.md · L16 |
| K6.6.1.4 | Anahtar Yaşam Döngüsü | encryption-aes256.md · L32 |
| K6.6.1.5 | Key Derivation | encryption-aes256.md · L41 |
| K6.6.1.6 | Encryption at Rest vs in Transit | encryption-aes256.md · L48 |
| K6.6.1.7 | Konfigürasyon / Kod | encryption-aes256.md · L54 |
| K6.6.1.8 | Güvenlik Kontrolleri | encryption-aes256.md · L318 |
| K6.6.1.9 | Bağımlılıklar | encryption-aes256.md · L331 |
| **K6.6.2** | **vault-secrets.md — Credential Vault** | vault-secrets.md · L10–L328 · 9 yaprak |
| K6.6.2.1 | Genel Bakış | vault-secrets.md · L10 |
| K6.6.2.2 | Teknik Detaylar | vault-secrets.md · L14 |
| K6.6.2.3 | Vault Yapısı | vault-secrets.md · L16 |
| K6.6.2.4 | Secret Tipleri | vault-secrets.md · L34 |
| K6.6.2.5 | AppRole Authentication | vault-secrets.md · L43 |
| K6.6.2.6 | Secret Rotation | vault-secrets.md · L51 |
| K6.6.2.7 | Konfigürasyon / Kod | vault-secrets.md · L58 |
| K6.6.2.8 | Güvenlik Kontrolleri | vault-secrets.md · L308 |
| K6.6.2.9 | Bağımlılıklar | vault-secrets.md · L321 |

**K6.6 Ara Toplam:** ikinci 1 · üçüncü 2 · yaprak 18

### K6.7 — Audit Logging (9 yaprak)

*Dosya: audit-logging.md — log kategorileri, JSON formatı ve tamper-proof mekanizması; 9 yaprak (Durum başlığı hariç).*

| Kod | Ad | Kanıt (dosya · satır) |
|---|---|---|
| **K6.7.1** | **audit-logging.md — Denetim Günlüğü** | audit-logging.md · L10–L453 · 9 yaprak |
| K6.7.1.1 | Genel Bakış | audit-logging.md · L10 |
| K6.7.1.2 | Teknik Detaylar | audit-logging.md · L14 |
| K6.7.1.3 | Log Kategorileri | audit-logging.md · L16 |
| K6.7.1.4 | Log Formatı (JSON) | audit-logging.md · L33 |
| K6.7.1.5 | Tamper-Proof Mekanizması | audit-logging.md · L64 |
| K6.7.1.6 | Log Saklama Politikası | audit-logging.md · L68 |
| K6.7.1.7 | Konfigürasyon / Kod | audit-logging.md · L78 |
| K6.7.1.8 | Güvenlik Kontrolleri | audit-logging.md · L433 |
| K6.7.1.9 | Bağımlılıklar | audit-logging.md · L446 |

**K6.7 Ara Toplam:** ikinci 1 · üçüncü 1 · yaprak 9

### K6.8 — README (Bileşen Haritası + ADR) (28 yaprak)

*Dosya: README.md — 14 başlık yaprağı (8 H2 + 6 H3) + 14 tablo satırı (§2 bileşen: 8, §8 ADR: 6) = 28 yaprak; 4 alt kat.*

| Kod | Ad | Kanıt (dosya · satır) |
|---|---|---|
| **K6.8.1** | **README §1–§7 H2 Başlıkları** | README.md · L26–L130 · 7 yaprak |
| K6.8.1.1 | 1. Genel Bakış | README.md · L26 |
| K6.8.1.2 | 2. Bileşen Haritası | README.md · L32 |
| K6.8.1.3 | 3. Authentication (K6-01) | README.md · L47 |
| K6.8.1.4 | 4. CSRF (K6-03) | README.md · L80 |
| K6.8.1.5 | 5. CSP (K6-04) | README.md · L93 |
| K6.8.1.6 | 6. Encryption (K6-06) | README.md · L113 |
| K6.8.1.7 | 7. Rate Limiting (K6-05) | README.md · L130 |
| **K6.8.2** | **README §2 Bileşen Haritası Satırları** | README.md · L36–L43 · 8 yaprak |
| K6.8.2.1 | K6-01 Auth System (JWT + Session hybrid, ADR-043) | README.md · L36 |
| K6.8.2.2 | K6-02 RBAC (Rol bazlı erişim) | README.md · L37 |
| K6.8.2.3 | K6-03 CSRF (Cross-Site Request Forgery, ADR-010) | README.md · L38 |
| K6.8.2.4 | K6-04 CSP (Content Security Policy, ADR-012) | README.md · L39 |
| K6.8.2.5 | K6-05 Rate Limit (APCu tabanlı, ADR-013) | README.md · L40 |
| K6.8.2.6 | K6-06 Encryption (AES-256-GCM, ADR-022) | README.md · L41 |
| K6.8.2.7 | K6-07 Credential Vault (Güvenli depolama, ADR-034) | README.md · L42 |
| K6.8.2.8 | K6-08 Audit Trail (Günlük kaydı) | README.md · L43 |
| **K6.8.3** | **README §3–§7 H3 Alt Başlıkları** | README.md · L49–L132 · 6 yaprak |
| K6.8.3.1 | 3.1 JWT + Session Hybrid | README.md · L49 |
| K6.8.3.2 | 3.2 Token Struct | README.md · L58 |
| K6.8.3.3 | 4.1 Token Kuralları | README.md · L82 |
| K6.8.3.4 | 5.1 Policy | README.md · L95 |
| K6.8.3.5 | 6.1 Parametreler | README.md · L115 |
| K6.8.3.6 | 7.1 Kurallar | README.md · L132 |
| **K6.8.4** | **README §8 İlgili ADR'ler** | README.md · L144–L153 · 7 yaprak |
| K6.8.4.1 | 8. İlgili ADR'ler (H2 başlık) | README.md · L144 |
| K6.8.4.2 | ADR-010 — csrf_token key zorunlu | README.md · L148 |
| K6.8.4.3 | ADR-012 — strict-dynamic, nonce-based CSP | README.md · L149 |
| K6.8.4.4 | ADR-013 — APCu, 60 req/60s | README.md · L150 |
| K6.8.4.5 | ADR-022 — AES-256-GCM, Argon2id | README.md · L151 |
| K6.8.4.6 | ADR-034 — AES-256-GCM credential vault | README.md · L152 |
| K6.8.4.7 | ADR-043 — Auth subdomain konsolidasyonu | README.md · L153 |

**K6.8 Ara Toplam:** ikinci 1 · üçüncü 4 · yaprak 28

### K6.9 — OWASP Threat Model + index.md (6 yaprak)

*Dosya: index.md — OWASP Top 10 tehdit modeli ve Zero Trust düğümleri; 6 yaprak (Durum başlığı hariç). Ayrı bir owasp*.md dosyası diskte yoktur (Katalog Notu 4).*

| Kod | Ad | Kanıt (dosya · satır) |
|---|---|---|
| **K6.9.1** | **index.md — OWASP / Zero Trust İndeksi** | index.md · L10–L94 · 6 yaprak |
| K6.9.1.1 | Genel Bakış | index.md · L10 |
| K6.9.1.2 | Güvenlik Mimarisi Diyagramı | index.md · L14 |
| K6.9.1.3 | Katman Sorumlulukları | index.md · L44 |
| K6.9.1.4 | Zero Trust Prensibi | index.md · L62 |
| K6.9.1.5 | Threat Model (OWASP Top 10) | index.md · L70 |
| K6.9.1.6 | Bağımlılıklar | index.md · L85 |

**K6.9 Ara Toplam:** ikinci 1 · üçüncü 1 · yaprak 6

---

## Kanıt Kataloğu (K6)

> **Yöntem:** Katalog, k6-guvenlik/ dizinindeki 16 dosyanın tamamını kapsar. Kanıt kaynakları: (a) MD başlık satırı (##/###), (b) README indeks tablosu satırı. Her dosya için anchor sayısı, dışlama sayısı ve yaprak sayısı diskten (top-level grep ^#{2,3} ) sayılmıştır. 154 anchor − 18 dışlama = 136 başlık yaprağı + 14 README tablo satırı = 150 yaprak.

| # | Dosya | K6 Düğümü | Kanıt Aralığı | Anchor | Dışlanan | Yaprak |
|---|-------|-----------|---------------|--------|----------|--------|
| 1 | authentication-jwt.md | K6.1.1 | L10–L210 | 10 | 1 | 9 |
| 2 | session-management.md | K6.1.2 | L10–L380 | 10 | 1 | 9 |
| 3 | password-hashing.md | K6.1.3 | L10–L358 | 10 | 1 | 9 |
| 4 | oauth2-integration.md | K6.1.4 | L10–L498 | 10 | 1 | 9 |
| 5 | rbac-authorization.md | K6.2.1 | L10–L253 | 10 | 1 | 9 |
| 6 | csrf-protection.md | K6.3.1 | L10–L239 | 10 | 1 | 9 |
| 7 | input-validation.md | K6.3.2 | L10–L460 | 9 | 1 | 8 |
| 8 | csp-policy.md | K6.4.1 | L10–L216 | 10 | 1 | 9 |
| 9 | security-headers.md | K6.4.2 | L10–L272 | 10 | 1 | 9 |
| 10 | rate-limiting.md | K6.5.1 | L10–L344 | 10 | 1 | 9 |
| 11 | encryption-aes256.md | K6.6.1 | L10–L338 | 10 | 1 | 9 |
| 12 | vault-secrets.md | K6.6.2 | L10–L328 | 10 | 1 | 9 |
| 13 | audit-logging.md | K6.7.1 | L10–L453 | 10 | 1 | 9 |
| 14 | index.md | K6.9.1 | L10–L94 | 7 | 1 | 6 |
| 15 | README.md | K6.8.1–K6.8.4 | L26–L153 | 14 | 0 | 14 + 14 tablo satırı = 28 |
| 16 | CLAUDE.md | — (kural dosyası) | L14–L47 | 4 | 4 | 0 |
| — | **TOPLAM** | **9 düğüm / 18 kat** | — | **154** | **18** | **150** |

### Katalog — Düğüm → Dosya Eşlemesi (18 kat)

| 3. Seviye Düğüm | Dosya | Yaprak |
|---|---|---|
| K6.1.1 Authentication & Session | authentication-jwt.md | 9 |
| K6.1.2 Session Management | session-management.md | 9 |
| K6.1.3 Password Hashing | password-hashing.md | 9 |
| K6.1.4 OAuth2 Integration | oauth2-integration.md | 9 |
| K6.2.1 RBAC Authorization | rbac-authorization.md | 9 |
| K6.3.1 CSRF Protection | csrf-protection.md | 9 |
| K6.3.2 Input Validation | input-validation.md | 8 |
| K6.4.1 CSP Policy | csp-policy.md | 9 |
| K6.4.2 Security Headers | security-headers.md | 9 |
| K6.5.1 Rate Limiting | rate-limiting.md | 9 |
| K6.6.1 Encryption AES-256 | encryption-aes256.md | 9 |
| K6.6.2 Vault Secrets | vault-secrets.md | 9 |
| K6.7.1 Audit Logging | audit-logging.md | 9 |
| K6.8.1 README H2 Başlıkları | README.md | 7 |
| K6.8.2 README §2 Bileşen Satırları | README.md | 8 |
| K6.8.3 README H3 Alt Başlıkları | README.md | 6 |
| K6.8.4 README §8 ADR Satırları | README.md | 7 |
| K6.9.1 OWASP / Zero Trust | index.md | 6 |
| — | **TOPLAM** | **150** |

### Katalog — Dışlamalar (18)

| # | Dışlanan Başlık | Dosya · Satır | Gerekçe |
|---|---|---|---|
| 1 | Durum: Implementasyon | audit-logging.md · L453 | Şablon durum satırı — kanıt üretmez |
| 2 | Durum: Implementasyon | authentication-jwt.md · L210 | Şablon durum satırı — kanıt üretmez |
| 3 | Durum: Implementasyon | csrf-protection.md · L239 | Şablon durum satırı — kanıt üretmez |
| 4 | Durum: Implementasyon | csp-policy.md · L216 | Şablon durum satırı — kanıt üretmez |
| 5 | Durum: Implementasyon | encryption-aes256.md · L338 | Şablon durum satırı — kanıt üretmez |
| 6 | Durum: Implementasyon | index.md · L94 | Şablon durum satırı — kanıt üretmez |
| 7 | Durum: Implementasyon | input-validation.md · L460 | Şablon durum satırı — kanıt üretmez |
| 8 | Durum: Implementasyon | oauth2-integration.md · L498 | Şablon durum satırı — kanıt üretmez |
| 9 | Durum: Implementasyon | password-hashing.md · L358 | Şablon durum satırı — kanıt üretmez |
| 10 | Durum: Implementasyon | rate-limiting.md · L344 | Şablon durum satırı — kanıt üretmez |
| 11 | Durum: Implementasyon | rbac-authorization.md · L253 | Şablon durum satırı — kanıt üretmez |
| 12 | Durum: Implementasyon | security-headers.md · L272 | Şablon durum satırı — kanıt üretmez |
| 13 | Durum: Implementasyon | session-management.md · L380 | Şablon durum satırı — kanıt üretmez |
| 14 | Durum: Implementasyon | vault-secrets.md · L328 | Şablon durum satırı — kanıt üretmez |
| 15 | 1. Hard Guardrails | CLAUDE.md · L14 | Kural dosyası — uygulama kanıtı değil |
| 16 | 2. Yasaklı Örüntüler | CLAUDE.md · L23 | Kural dosyası — uygulama kanıtı değil |
| 17 | 3. Şifreleme Parametreleri | CLAUDE.md · L37 | Kural dosyası — uygulama kanıtı değil |
| 18 | 4. İlgili ADR'ler | CLAUDE.md · L47 | Kural dosyası — uygulama kanıtı değil |
| — | **TOPLAM DIŞLAMA** | **18** | — |

### Katalog — Sayım Özeti

| Ölçüm | Değer |
|---|---|
| Dosya sayısı (katalog) | 16 (13 içerik + index.md + README.md + CLAUDE.md) |
| Toplam anchor (^#{2,3} ) | 154 |
| Dışlama (14 Durum + 4 CLAUDE) | 18 |
| Başlık yaprağı | 136 |
| README tablo satırı yaprağı (§2:8 + §8:6) | 14 |
| **Toplam yaprak** | **150** |
| İkinci düğüm (K6.a) | 9 |
| Üçüncü düğüm / kat (K6.a.b) | 18 |
| Hedef ikinci / üçüncü / yaprak | 9 / 5 / 150 |
| Durum | İkinci = 9 (tam) · üçüncü = 18 (≥5 kat) · yaprak = 150 (tam) |

### Katalog — Tutarlılık Kontrolleri

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | anchor − dışlama = başlık yaprağı | 154 − 18 = 136 ✓ |
| 136 + 14 = toplam yaprak | 150 ✓ |
| 2 | İkinci düğüm yaprak toplamı (36+9+17+18+9+18+9+28+6) | 150 ✓ |
| 3 | Üçüncü düğüm toplamı (4+1+2+2+1+2+1+4+1) | 18 ✓ |
| 4 | Dışlama tablosu satır sayısı | 18 (14 Durum + 4 CLAUDE) ✓ |
| 5 | Katalog dosya sayısı | 16 (dizin glob ile birebir) ✓ |
| 6 | K6.8 = 14 başlık + 14 tablo satırı | 28 ✓ |

### Katalog Notları

1. **Durum dışlaması (14):** 13 içerik dosyası + index.md dosyasındaki "Durum: Implementasyon" başlıkları şablon boilerplate'idir; somut kanıt satırı içermez, yaprak sayılmaz (Dışlamalar tablosu 1–14).
2. **CLAUDE.md dışlaması (4):** K6 katmanının kural/guardrail dosyasıdır; hard guardrail, yasaklı örüntü, şifreleme parametresi ve ADR başlıkları uygulama kanıtı değil, kural metnidir — dosya 0 yaprakla katalogda kalır (Dışlamalar 15–18).
3. **README iki kanıt tipi:** README.md hem H2/H3 başlıklarından (14) hem de indeks tablo satırlarından (§2 bileşen: 8 satır L36–L43, §8 ADR: 6 satır L148–L153) yaprak üretir; bu iki tipin toplamı K6.8 = 28 yapraktır ve TOPLAM tablo satırları sayımın içine girmez.
4. **OWASP kaynağı:** Ayrı bir owasp*.md dosyası diskte yoktur; OWASP Top 10 tehdit modeli kanıtı index.md · L70 ("Threat Model (OWASP Top 10)") başlığındadır — K6.9 bu nedenle OWASP + index.md düğümü adını taşır.
5. **input-validation.md 9 anchor:** Diğer 12 içerik dosyası 10 anchor taşırken input-validation.md 9 taşır (4 H2 + 4 H3 + 1 Durum); Durum dışlandıktan sonra 8 yaprak kalır — K6.3 toplamı 9 + 8 = 17 yapraktır.
6. **Tek dosyalı katmanlar:** K6.2, K6.5 ve K6.7 ikinci düğümleri tek dosyadan beslenir; 3. seviye = dosya (kat) kuralı nedeniyle yine de 3. seviye düğüm oluşturulmuş, doğrudan 2. seviyeden yaprak sarkmamıştır.

---

*K6 Güvenlik Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-24 — genişletme: 3 turlu agent tartışması*
*Mode: Red Team · Human Mode · Truth Mode*
