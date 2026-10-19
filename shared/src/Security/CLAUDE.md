---
title: "CoreMusic — shared/src/Security Bağlam"
type: context
folder: "shared/src/Security"
category: layer1-security
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
---

# Security — CLAUDE.md (Detaylı)

**Zorunlu Bağlantılar:** · [[../CLAUDE.md]] · [[../../.ai/architecture/k6-guvenlik]]

---

## 1. Bağlam

Güvenlik yardımcıları, rate limiter, session key yönetimi, UUID üretimi ve return URL politikası. OWASP Top 10:2025 uyumlu.

---

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Toplam dosya | 5 PHP dosyası |
| ADR | ADR-010, ADR-013, ADR-022 |

### 2.1 Dosya Envanteri

| Dosya | Amaç |
|-------|------|
| `CacheRateLimiter.php` | APCu tabanlı rate limiter implementasyonu |
| `ReturnUrlPolicy.php` | Redirect URL whitelist politikası |
| `SecurityHelper.php` | Genel güvenlik yardımcı fonksiyonları |
| `SessionKeys.php` | Session key sabitleri ve yönetimi |
| `UuidV7.php` | UUID v7 üretimi (time-based) |

---

## 3. bileşen Detayları

### 3.1 CacheRateLimiter
- **Depolama:** APCu (in-memory)
- **Limit:** 60 istek/60 saniye (ADR-013)
- **Key format:** `rate_limit_{ip}_{endpoint}`
- **Header:** `X-RateLimit-Remaining`, `X-RateLimit-Reset`

### 3.2 ReturnUrlPolicy
- **Amaç:** Open redirect önleme
- **Whitelist:** `.env` dosyasından okunur
- **Kural:** Yalnızca aynı domain'e redirect

### 3.3 SecurityHelper
- **Fonksiyonlar:** Token üretimi, hash, validate
- **Kullanım:** Tüm middleware ve service'ler tarafından

### 3.4 SessionKeys
- **Session key:** `COREMUSIC_SESS`
- **CSRF key:** `csrf_token`
- **Auth key:** `_auth`
- **CSP key:** `csp_nonce`

### 3.5 UuidV7
- **Format:** UUID v7 (time-based, monotonic)
- **Kullanım:** User ID, session ID, token ID

---

## 4. Komşu İlişkileri

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Shared library üst bağlam |
| Kullanıcı | [[../Middleware/CLAUDE.md]] | Middleware güvenlik |
| Kullanıcı | [[../Session/CLAUDE.md]] | Session yönetimi |
| Referans | [[../../.ai/architecture/k6-guvenlik]] | Güvenlik mimarisi |

---

## 5. Yasaklar

| # | Yasak | Neden |
|---|-------|-------|
| 1 | MD5/SHA1 hash | Güvensiz (ADR-022) |
| 2 |mcrypt | Deprecated |
| 3 | Düz metin secret | `.env` / credential vault |
| 4 | `eval()` / `Function()` | Güvenlik |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
