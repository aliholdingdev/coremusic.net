---
type: architecture
category: l2
title: "L2 — Routing Layer Index"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 4.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# L2 — Routing Layer Index

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]] · [[engine.md]]

---

## 1. Amaç

SPA (Single Page Application) routing, middleware pipeline ve servis yönlendirme mekanizmalarını tanımlar. [[ADR-004-multi-domain-spa]], [[ADR-009-clean-url-redirect]], [[ADR-016-url-normalization]] ve [[ADR-021-spa-router-immutable-contract]] ile uyumludur.

---

## 2. Mimari Konum

```
L3 Presentation (Frontend)
  ↓ JS Router (Router.js)
L2 Routing (Bu Katman)
  ↓ PHP PageRouter
L1 Security (Middleware)
  ↓ SessionManager → Csrf
L0 Infrastructure (Database/Cache)
```

**Bağımlılık:** ✅ L2 → L1, L2 → L3 | ❌ L2 → L0

---

## 3. Dosya Yapısı

| Dosya | Amaç |
|-------|------|
| [[spa-router]] | SPA PageRouter PHP implementation |
| [[subdomain-routing]] | Subdomain detection ve routing |
| [[url-normalization]] | URL normalization ve clean URL |
| [[middleware-pipeline]] | Middleware orchestration |
| [[service-discovery]] | Service discovery ve health check |
| [[js-router]] | Frontend JS Router (Router.js) |

---

## 4. İlgili ADR'ler

| ADR | Konu | Durum |
|-----|------|-------|
| [[ADR-004-multi-domain-spa]] | Multi-domain SPA | Frozen |
| [[ADR-009-clean-url-redirect]] | Clean URL | Frozen |
| [[ADR-016-url-normalization]] | URL normalization | Frozen |
| [[ADR-021-spa-router-immutable-contract]] | SPA router contract | Frozen |

---

## 5. Hard Guardrails

| # | Kural | ADR | İhlal Sonucu |
|---|-------|-----|-------------|
| 1 | Middleware sırası değişmez | ADR-010/011/012/013 | CSP/CSRF bozulması |
| 2 | SPA router contract immutable | ADR-021 | Routing bozulması |
| 3 | Subdomain routing zorunlu | ADR-016 | Servis erişimi |
| 4 | Clean URL redirect zorunlu | ADR-009 | SEO sorunu |
| 5 | URL normalization zorunlu | ADR-016 | Duplicate content |

---

## 6. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Mimari | [[architecture/l1-security]] | Security layer |
| § 2 Mimari | [[architecture/l3-presentation]] | Presentation layer |
| § 4 ADR'ler | [[ADR-004-multi-domain-spa]] | SPA |

---

## 7. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 4.0.0 |
| **Satır Sayısı** | ~120 |
| **Dosya Sayısı** | 6 alt dosya |
| **ADR Uyumlu** | ✅ 004, 009, 016, 021 |
| **Zero Hallucination** | ✅ |
| **MSA Uyumlu** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
