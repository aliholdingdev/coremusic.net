---
title: "CoreMusic — Backend Kuralları (PHP 8.4)"
type: guide
category: governance
date: 2026-10-22
updated: 2026-10-22
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/CLAUDE.md §6A-§6B, §21 · .ai/AGENTS.md §16 (Backend standardı)"
docType: claude
---

# CoreMusic — Backend Kuralları (Backend Rules — PHP)

**docType:** claude · **Klasör:** `.agents/rules/` · **Sorumlu:** backend-architect (birincil) + security (ikincil)

**Zorunlu Bağlantılar:** [[RULES]] · [[architecture-rules]] · [[security-rules]] · [[database-rules]] · [[../../.ai/CLAUDE]]

> **eli10:** PHP tarafında yazdığın her satır: strict_types, PDO prepared, API-first; framework/ORM/eval yok.

---

## 1. Amaç

PHP 8.4 backend geliştirme kurallarını (standart, katman, routing, middleware erişimi, hata yönetimi) vault SSOT'larından toplar. Backend kalite hedefi **%100** (`.ai/AGENTS.md` §16).

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `*.php` (Controller, Service, Repository, Middleware, DTO, UseCase) | Şema/BCNF → [[database-rules]] |
| `shared/src/**` (PSR-4, `coremusic/shared`) | Secret/CSRF/CSP → [[security-rules]] |
| routing / PageRouter / ApiClient sözleşmeleri | Mimari katman tanımı → [[architecture-rules]] |

---

## 3. Mimari

### 3.1 Kod Standartları (`@.ai/AGENTS.md` §16 · kök `AGENTS.md` §5 · `@.ai/CLAUDE.md` §12)

| Kural | Değer |
|-------|-------|
| Dil | PHP **8.4+**, her dosyada `declare(strict_types=1)` |
| Stil | **PSR-12** |
| DI | php-di · Routing: fast-route / PageRouter (composer.json kanıtlı) |
| Asenkron | `async/await` desenleri; `ConfigureAwait(false)` (C# tarafı için — kök AGENTS §5) |
| DTO | record/immutable tipler; framework bağımsız |

### 3.2 Veri Erişimi

| Kural | Değer | Kaynak |
|-------|-------|--------|
| ORM | **YASAK** — raw PDO (ADR-002) | Guardrail #9 |
| Prepared statement | **Zorunlu** | `.ai/AGENTS.md` §16 |
| `SELECT *` | **YASAK** — explicit columns | `@.ai/CLAUDE.md` §21 |
| `unserialize(user_input)` | **YASAK** | kök `AGENTS.md` §5 |
| N+1 sorgu | Yasak — repository'de join/plan yazılır | kök AGENTS §5 (SQL) |

### 3.3 Katman Zinciri (SPA → Kod — `@.ai/CLAUDE.md` §6A.5)

```
SPA → ApiClient → HTTP → Gateway → Middleware → Use Case → Domain → Repository → Infrastructure
```

- Controller'da iş mantığı **yok** (Presentation katmanı); UseCase/Service'e iner.
- Servisler birbirini **doğrudan çağırmaz** — PSR-14 Event Bus (ADR-086).
- OpenAPI sözleşmesi ihlal edilemez (Guardrail: API-First, ADR-084).

### 3.4 Middleware Erişimi

Zincir immutable'dır ([[security-rules]] §3.2 / [[architecture-rules]] §3.3). Backend kodu middleware sırasına dokunmaz; yeni middleware talebi mimari onayı ister (Guardrail #14).

### 3.5 Hata & Logging

| Kural | Değer |
|-------|-------|
| Exception | Typed exception (`shared/src/Exception/`); genel `Exception` catch yasak |
| Log | `shared/src/Log/` üzerinden; hassas veri `[REDACTED]` |
| Audit | K6 Audit katmanına olay yazımı (Güvenlik) |
| Bilinmeyen API/class | `// ⚠️ VERIFICATION REQUIRED` (`.ai/AGENTS.md` §17 #5) |

---

## 4. Kurallar

| # | Kural | İhlal |
|---|-------|-------|
| 1 | `strict_types=1` + PSR-12 her dosyada | Red |
| 2 | ORM / `SELECT *` / `eval` yasak | Revert |
| 3 | Endpoint önce OpenAPI | Kod geçersiz |
| 4 | Middleware sırasına dokunma | Sistem durdurulur |
| 5 | Domain boundary: `*.php` = backend agent | Layer violation → revert |
| 6 | Test coverage ≥%80 (soft: %75 geçici, Tech Lead onayı) | Kapı kırmızı (`.ai/CLAUDE.md` §8) |

---

## 5. Workflow

```text
Backend iş (endpoint/service/repo/middleware değişikliği)
  → Vault: @.ai/CLAUDE.md §6A-§6B + .ai/.decisions/index (ADR-083/084/085/086)
  → Skill: php-backend-standards
  → OpenAPI sözleşmesi → DTO → UseCase → Repository
  → PHPUnit (shared/tests/ — 22 dosya) + PHPStan
  → Security review (auth/CSRF etkiliyse) → MO: log.md
```

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter 7 alan + docType | Mevcut |
| 2 | Standartlar `.ai/AGENTS.md` §16 ile eşleşme | strict_types · PSR-12 · prepared |
| 3 | Yasaklar §21 listesiyle kapsanıyor | ORM · SELECT * · eval · unserialize |
| 4 | Katman zinciri vault §6A.5 ile birebir | §3.3 |
| 5 | Test hedefi yazılı | ≥%80 |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Quality (Backend) | [[../../.ai/AGENTS]] §16 | %100 standart |
| API-First | [[../../.ai/CLAUDE]] §6A | Sözleşme zinciri |
| Yasaklar | [[../../.ai/CLAUDE]] §21 | Forbidden patterns |
| ADR indeksi | [[../../.ai/.decisions/index]] | ADR-083/084/085/086 |
| Skill | `.claude/skills/php-backend-standards/SKILL.md` | Uygulama kılavuzu |
| Master | [[RULES]] §4.2 | Özet |

**Last Updated:** 2026-10-22 · **Şablon:** context-template (Guardrail #16)
