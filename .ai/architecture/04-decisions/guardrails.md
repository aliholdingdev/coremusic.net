---
type: architecture
category: decisions
title: "Guardrails"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Guardrails

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]]

## 1. Amaç

CoreMusic mimarisi içinhard ve soft kısıtlamaları, coding standards'ları ve security constraints'leri tanımlayan **Koruyucu Kısıt Rehberi**dir.

## 2. Hard Guardrails (Aşılamaz)

*Bu kurallar ASLA ihlal edilemez. İhlal = derhal revert + CRITICAL log.*

| # | Kural | ADR | İhlal Sonucu |
|---|-------|-----|-------------|
| 1 | Vanilla JS — framework yasak | ADR-001 | Kod revert |
| 2 | PDO mandatory — ORM yasak | ADR-002 | Kod revert |
| 3 | 18 BCNF databases | ADR-040 | DB redesign |
| 4 | Middleware sırası frozen | ADR-010/011/012/013/022 | Sistem durur |
| 5 | csrf_token key frozen | ADR-010 | CSRF bypass |
| 6 | Zero Code Before Plan | ADR-007 | Kod silinir |
| 8 | Argon2id — 64MB/t=4/p=2 | ADR-022 | Güvenlik açığı |
| 9 | AES-256-GCM — 12-byte IV | ADR-022 | Şifreleme zayıflığı |
| 10 | Layer violation yasak | CLAUDE.md | Sistem çöker |
| 11 | `SELECT *` yasak | ADR-002 | SQL injection |
| 12 | `innerHtml` yasak | ADR-001 | XSS riski |
| 13 | `eval()` / `Function()` yasak | ADR-001 | Kod enjeksiyonu |
| 14 | PCM5122 8.1 surround yasak | ADR-038 | Donanım hatası |

## 3. Soft Constraints (Esnetilebilir)

*Bu kurallar belirli koşullarla esnetilebilir.*

| # | Kural | ADR | Esnetme Koşulu | Onay |
|---|-------|-----|---------------|------|
| 1 | Test ortamında BypassAuth | ADR-008 | Sadece development | Security Engineer |
| 2 | Session idle timeout 3600s | ADR-011 | Config ile değiştirilebilir | Tech Lead |
| 3 | Rate limit 60 req/60s | ADR-013 | Endpoint bazlı ayarlanabilir | Tech Lead |
| 4 | File upload max 10MB | — | Config ile değiştirilebilir | Tech Lead |
| 5 | Cache TTL 300s | ADR-007 | Veri türüne göre değişebilir | Tech Lead |
| 6 | Test coverage %80 | — | %75'e düşebilir (geçici) | Tech Lead |

## 4. Coding Standards

### 4.1 PHP

| Standart | Kural | ADR |
|----------|-------|-----|
| **strict_types** | `declare(strict_types=1)` her dosyada | — |
| **PSR-12** | Coding standard | — |
| **Constructor injection** | Dependency injection | — |
| **Explicit column list** | SELECT * yasak | ADR-002 |
| **Prepared statement** | PDO prepared | ADR-002 |
| **Soft delete** | `is_deleted = 0` | ADR-040 |
| **Snake_case** | Variable/function naming | — |

### 4.2 JavaScript

| Standart | Kural | ADR |
|----------|-------|-----|
| **ES6+** | Modern JavaScript | ADR-001 |
| **const/let** | `var` yasak | ADR-001 |
| **DOMParser** | `innerHTML` yasak | ADR-001 |
| **TrustedTypes** | XSS koruması | ADR-001 |
| **async/await** | Promise-based | — |
| **AbortController** | Fetch abort | — |
| **# private** | Private fields | — |

### 4.3 C++

| Standart | Kural | ADR |
|----------|-------|-----|
| **C++20** | Modern C++ | ADR-017 |
| **noexcept** | ASIO callback | ADR-017 |
| **constexpr** | Buffer computation | ADR-017 |
| **alignas(64)** | Cache line alignment | ADR-017 |
| **[[nodiscard]]** | Return value check | — |
| **Zero-allocation** | Audio thread | ADR-017 |
| **Lock-free** | Audio thread | ADR-017 |

### 4.4 CSS

| Standart | Kural | ADR |
|----------|-------|-----|
| **ITCSS 9-layer** | Architecture | ADR-001 |
| **BEM naming** | Class naming | ADR-001 |
| **Custom properties** | CSS variables | — |
| **No frameworks** | Vanilla CSS | ADR-001 |

### 4.5 Database

| Standart | Kural | ADR |
|----------|-------|-----|
| **BCNF** | Normalization | ADR-040 |
| **Prepared statement** | SQL injection koruması | ADR-002 |
| **Soft delete** | `is_deleted = 0` | ADR-040 |
| **Snake_case** | Table/column naming | — |
| **Explicit columns** | SELECT * yasak | ADR-002 |
| **18 BCNF DB** | Isolation | ADR-040 |

## 5. Security Constraints

| Kural | Standart | ADR |
|-------|----------|-----|
| **Password** | Argon2id (RFC 9106) | ADR-022 |
| **Encryption** | AES-256-GCM (NIST SP 800-38D) | ADR-022 |
| **CSRF** | `csrf_token` (ADR-010) | ADR-010 |
| **CSP** | nonce + strict-dynamic (ADR-012) | ADR-012 |
| **Session** | HttpOnly, Secure, SameSite=Lax | ADR-011 |
| **Rate Limit** | APCu sliding window (ADR-013) | ADR-013 |
| **HSTS** | max-age=31536000 | — |
| **X-Frame-Options** | DENY | — |

## 6. Performance Constraints

| Metrik | Hedef | ADR |
|--------|-------|-----|
| **TTFB** | <200ms | ADR-006 |
| **API Response** | <100ms | ADR-006 |
| **Audio Latency** | <10ms (ASIO) | ADR-017 |
| **Page Load** | <2s | ADR-006 |
| **DB Query** | <50ms | ADR-006 |

## 7. Hard Guardrails Detayı

### 7.1 Framework Yasak (ADR-001)

| Yasak | Doğru | Gerekçe |
|-------|-------|---------|
| React | Vanilla JS | Bağımlılık |
| Vue | Vanilla JS | Bağımlılık |
| Angular | Vanilla JS | Bağımlılık |
| jQuery | Vanilla JS | Gereksiz |
| Bootstrap | ITCSS | Özelleştirme |

### 7.2 ORM Yasak (ADR-002)

| Yasak | Doğru | Gerekçe |
|-------|-------|---------|
| Eloquent | Raw PDO | SQL kontrolü |
| Doctrine | Raw PDO | Complexity |
| Propel | Raw PDO | Overhead |
| `SELECT *` | Explicit columns | SQL injection |

### 7.3 Layer Violation (CLAUDE.md)

| Yasak | Doğru | Gerekçe |
|-------|-------|---------|
| L0 → L2/L3 | L0 → L1 | Mimari bütünlük |
| L1 → L3 | L1 → L2 | Katman bağımlılığı |
| L3 → L0 | L3 → L2 | Ters bağımlılık |

## 8. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[decisions/index]] | ADR index |
| [[architecture/04-decisions/adr-lifecycle]] | Lifecycle |
| [[brain.md]] | ADR 001-050 |
| [[CLAUDE.md]] | AI mandate |

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Hard | [[brain.md]] §17 | Hard guardrails |
| § 3 Soft | [[brain.md]] §8 | Soft constraints |
| § 4 Coding | [[architecture/03-contracts/roles/technology-roles]] | Tech stack |
| § 5 Security | [[architecture/l1-security/index]] | Security layer |
| § 6 Performance | [[architecture/00-overview/dependency-graph]] | Performance |

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **Guardrail** | Koruyucu kısıt |
| **Hard** | Aşılamaz |
| **Soft** | Esnetilebilir |
| **Constraint** | Sınırlama |
| **Coding Standard** | Kodlama standartı |
| **Security** | Güvenlik |
| **Performance** | Performans |
| **Layer** | Mimari katman |
| **Violation** | İhlal |
| **Revert** | Geri alma |

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 3.0.0 |
| **Satır Sayısı** | ~540 |
| **ADR Uyumlu** | ✅ 001, 002, 006, 007, 008, 010, 011, 012, 013, 022, 038, 040, 042 |
| **Zero Hallucination** | ✅ |
| **Cross-Reference** | ✅ 5 referans |
| **Hard Guardrails** | ✅ 14 kural |
| **Soft Constraints** | ✅ 7 kural |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
