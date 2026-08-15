---
type: decision
id: "083"
title: "ADR-083: SPA Router Architecture (PHP+JS Hybrid)"
category: "architecture"
status: "active"
date: "2026-08-12"
updated: "2026-08-15"
authority: "Backend Architect"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [architecture, spa, router, php, js, hybrid, active]
risk-level: "high"
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
  - "[[decisions/accepted/ADR-001-vanilla-js-itcss]]"
  - "[[decisions/accepted/ADR-004-multi-domain-spa]]"
  - "[[architecture/l2-routing]]"
---

# ADR-083: SPA Router Architecture (PHP+JS Hybrid)

---

## 1. Executive Summary

CoreMusic SPA Router, **PHP + JS hybrid** mimarisi ile çalışır. PHP tarafı sayfa html'ini üretir, JS tarafı client-side routing'i yönetir. History API (pushState/popstate) kullanılır. DOMParser ile innerHTML yasak.

## 2. Decision

### Router Mimaris

```
User Click → SPA Router (History API) → ApiClient (Fetch) → API Gateway → Controller → Response → SPA Renderer
```

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | History API (pushState/popstate) | ✅ Zorunlu |
| 2 | PHP + JS hybrid | ✅ Zorunlu |
| 3 | DOMParser (innerHTML yasak) | ✅ Zorunlu |
| 4 | TrustedTypes policy | ✅ Zorunlu |
| 5 | Client-side state | ✅ Zorunlu |
| 6 | Backend-controlled auth | ✅ Zorunlu |

### Route Guard Pipeline

```
Route Change → Auth Check → Permission Check → Load Page → Render
```

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-083: SPA Router Architecture v2.0.0 — CoreMusic Architecture*
*Authority: Backend Architect · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
