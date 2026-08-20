---
title: "ADR-007: Cache Namespace Standard"
status: frozen
date: 2026-02-15
tags: [architecture, cache, namespace, zero-code-before-plan, frozen]
---

# ADR-007: Cache Namespace Standard

---

## 1. Executive Summary

CoreMusic'te cache namespace'leri standartlaÅŸtÄ±rÄ±lmÄ±ÅŸtÄ±r. Her domain kendi namespace'ini kullanÄ±r. Zero Code Before Plan prensibi bu ADR'de tanÄ±mlanmÄ±ÅŸtÄ±r.

## 2. Decision

### Cache Namespace FormatÄ±

```
{domain}:{subdomain}:{key}
```

| Ã–rnek | Namespace |
|-------|-----------|
| User cache | `auth:user:{userId}` |
| Session cache | `auth:session:{sessionId}` |
| Music cache | `musics:song:{songId}` |
| Rate limit | `security:ratelimit:{ip}` |
| CSRF token | `security:csrf:{sessionId}` |

### Zero Code Before Plan

| # | Kural | Durum |
|---|-------|-------|
| 1 | Plan onayÄ± olmadan kod yok | âœ… Zorunlu |
| 2 | Mimari onay zorunlu | âœ… Zorunlu |
| 3 | Template kullanÄ±mÄ± zorunlu | âœ… Zorunlu |
| 4 | Vault-first okuma | âœ… Zorunlu |

---

## 3. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Frozen |

---

*ADR-007: Cache Namespace Standard v2.0.0 â€” CoreMusic Architecture*
*Authority: Backend Architect Â· Last Updated: 2026-08-15*
*Status: Frozen Â· Governance: Red Team Â· Human Mode Â· Truth Mode*