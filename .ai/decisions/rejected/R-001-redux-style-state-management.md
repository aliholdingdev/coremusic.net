---
type: decision
id: "R-001"
title: "REJECTED: Redux-Style State Management"
category: "frontend"
status: "rejected"
date: "2026-08-15"
updated: "2026-08-15"
authority: "UI Designer"
governance: "Red Team · Human Mode · Truth Mode"
version: 1.0.0
tags: [rejected, frontend, state, redux, framework]
risk-level: "high"
rejection-reason: "Framework yasağı (ADR-001), bağımlılık artışı"
rejected-by: "ADR-001"
references:
  - "[[decisions/accepted/ADR-001-vanilla-js-itcss]]"
---

# REJECTED: Redux-Style State Management

---

## 1. Executive Summary

Redux veya benzeri state management kütüphanelerinin (MobX, Zustand, Pinia) kullanımı **reddedilmiştir**.

## 2. Red Nedeni

| # | Neden | Ağırlık |
|---|-------|---------|
| 1 | ADR-001: Framework yasağı | Kritik |
| 2 | Gereksiz bağımlılık | Yüksek |
| 3 | Over-engineering | Yüksek |
| 4 | Vanilla JS yeterli | Orta |

## 3. Reddetilen Yaklaşım

Redux-style state management:
- External dependency gerektirir
- ADR-001 (Vanilla JS) ile çelişir
- Karmaşıklık yaratır
- CoreMusic için over-engineering'dir

## 4. Alternatif Çözüm

**Seçilen:** Vanilla JS ile basit state management
- Client-side state objesi
- Event-driven updates
- sessionStorage persistence

---

*Reddedildi: 2026-08-15 · Governance: Red Team · Human Mode · Truth Mode*
