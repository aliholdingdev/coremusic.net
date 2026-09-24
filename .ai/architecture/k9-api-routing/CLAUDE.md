---
title: "CoreMusic — K9 API & Routing CLAUDE.md"
type: layer-guide
folder: "architecture/k9-api-routing"
category: vault
date: 2026-09-20
status: active
version: 1.0.0
authority: reference
---

# K9 API & Routing — CLAUDE.md

## 1. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | API-First (ADR-084) | Sözleşme ihlali |
| 2 | SPA DB görmemeli | Layer violation |
| 3 | OpenAPI spec zorunlu | Doküman eksik |
| 4 | BFF pattern | Doğru response |

## 2. SPA → ApiClient Kuralı

```
SPA → ApiClient → HTTP → Gateway → Middleware → Use Case → Domain → Repository → Infrastructure

SPA asla PDO, MySQL, Repository, Entity, Infrastructure, Filesystem,
FFmpeg, Redis, Cache veya SQL GÖRMEZ.
```

## 3. BFF Response Formatları

| İstemci | BFF | Response |
|---------|-----|----------|
| SPA | SPA BFF | Tam veri |
| Mobile | Mobile BFF | Minimal |
| Embedded | Embedded BFF | Ultra-minimal |
| Admin | Admin BFF | Full + audit |

## 4. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-083 | SPA Router Architecture |
| ADR-084 | API Gateway Architecture |

---

*K9 CLAUDE.md v1.0.0 — CoreMusic*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
