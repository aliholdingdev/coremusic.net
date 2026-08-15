---
type: decision
id: "007"
title: "ADR-007: Cache Namespace Standard"
category: "architecture"
status: "frozen"
date: "2026-02-15"
updated: "2026-08-15"
authority: "Backend Architect"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [architecture, cache, namespace, zero-code-before-plan, frozen]
risk-level: "high"
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
---

# ADR-007: Cache Namespace Standard

---

## 1. Executive Summary

CoreMusic'te cache namespace'leri standartlaştırılmıştır. Her domain kendi namespace'ini kullanır. Zero Code Before Plan prensibi bu ADR'de tanımlanmıştır.

## 2. Decision

### Cache Namespace Formatı

```
{domain}:{subdomain}:{key}
```

| Örnek | Namespace |
|-------|-----------|
| User cache | `auth:user:{userId}` |
| Session cache | `auth:session:{sessionId}` |
| Music cache | `musics:song:{songId}` |
| Rate limit | `security:ratelimit:{ip}` |
| CSRF token | `security:csrf:{sessionId}` |

### Zero Code Before Plan

| # | Kural | Durum |
|---|-------|-------|
| 1 | Plan onayı olmadan kod yok | ✅ Zorunlu |
| 2 | Mimari onay zorunlu | ✅ Zorunlu |
| 3 | Template kullanımı zorunlu | ✅ Zorunlu |
| 4 | Vault-first okuma | ✅ Zorunlu |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-007: Cache Namespace Standard v2.0.0 — CoreMusic Architecture*
*Authority: Backend Architect · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
