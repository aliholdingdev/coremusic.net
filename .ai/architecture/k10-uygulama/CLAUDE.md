---
title: "CoreMusic — K10 Uygulama CLAUDE.md"
type: layer-guide
folder: "architecture/k10-uygulama"
category: vault
date: 2026-09-20
status: active
version: 1.0.0
authority: SSOT
---

# K10 Uygulama — CLAUDE.md

## 1. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Tek component sistemi | Kod revert edilir |
| 2 | Ayrı HTML yasak | Kod revert edilir |
| 3 | Mockup okunmadan kod yasak | CRITICAL log |
| 4 | SPA DB görmemeli | Layer violation |

## 2. Panel Portları

| Panel | Subdomain | Port |
|-------|-----------|------|
| Music | music.coremusic.net | 81 |
| Home | home.coremusic.net | 81 |
| Admin | admin.coremusic.net | 80 |
| Download | download.coremusic.net | 3001 |
| Car | car.coremusic.net | — |
| Studio | studio.coremusic.net | 81 |

## 3. 4-Tier Device Manager

| Tier | Cihazlar | Viewport |
|------|----------|----------|
| Tier 1 | Phone | ≤767px |
| Tier 2 | Embedded, Tablet | ≤1024px |
| Tier 3 | Laptop, Desktop | 1025-2560px |
| Tier 4 | 4K TV, 4K Monitor | ≥2561px |

## 4. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-045 | Multi-domain view mode |
| ADR-046 | Cross-view state koruma |

---

*K10 CLAUDE.md v1.0.0 — CoreMusic*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
