---
type: decision
id: "004"
title: "ADR-004: Multi-Domain SPA Architecture"
category: "architecture"
status: "frozen"
date: "2026-02-01"
updated: "2026-08-15"
authority: "Backend Architect"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [architecture, spa, multi-domain, subdomain, frozen]
risk-level: "critical"
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
  - "[[decisions/accepted/ADR-043-auth-subdomain-consolidation]]"
  - "[[architecture/l2-routing]]"
---

# ADR-004: Multi-Domain SPA Architecture

---

## 1. Executive Summary

CoreMusic, **multi-subdomain SPA** mimarisi ile çalışır. Her subdomain (music, admin, home, car, studio, pro, media, download) kendi SPA'sını çalıştırır. Subdomain'ler arası geçiş client-side routing ile yapılır. auth.coremusic.net merkezi auth servisidir.

## 2. Status

| Alan | Değer |
|------|-------|
| **Durum** | frozen |
| **Versiyon** | 2.0.0 |
| **Risk Seviyesi** | critical |

## 3. Decision

### Subdomain Haritası

| Subdomain | Port | Amaç |
|-----------|------|------|
| coremusic.net | 80 | Landing page |
| music.coremusic.net | 81 | Ana medya paneli |
| admin.coremusic.net | 80 | Yönetim paneli |
| auth.coremusic.net | — | Merkezi auth |
| home.coremusic.net | 81 | Ev medya merkezi |
| car.coremusic.net | — | Araç içi |
| studio.coremusic.net | 81 | Stüdyo |
| pro.coremusic.net | 81 | Profesyonel |
| media.coremusic.net | 5000/6000 | Medya processing |
| download.coremusic.net | 3001 | İndirme servisi |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | Her subdomain kendi SPA'sı | ✅ Zorunlu |
| 2 | auth.coremusic.net merkezi | ✅ Zorunlu |
| 3 | Cross-subdomain session | ✅ Zorunlu |
| 4 | Client-side routing | ✅ Zorunlu |
| 5 | History API | ✅ Zorunlu |

---

## 4. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-004: Multi-Domain SPA Architecture v2.0.0 — CoreMusic Architecture*
*Authority: Backend Architect · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
