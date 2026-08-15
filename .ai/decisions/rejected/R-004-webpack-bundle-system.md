---
type: decision
id: "R-004"
title: "REJECTED: Webpack Bundle System"
category: "frontend"
status: "rejected"
date: "2026-08-15"
updated: "2026-08-15"
authority: "UI Designer"
governance: "Red Team · Human Mode · Truth Mode"
version: 1.0.0
tags: [rejected, frontend, webpack, build, bundle]
risk-level: "medium"
rejection-reason: "Karmaşıklık, over-engineering, ADR-001"
rejected-by: "ADR-001"
references:
  - "[[decisions/accepted/ADR-001-vanilla-js-itcss]]"
---

# REJECTED: Webpack Bundle System

---

## 1. Executive Summary

Webpack veya benzeri build tool'larının (Vite, Rollup, esbuild) kullanımı **reddedilmiştir**.

## 2. Red Nedeni

| # | Neden | Ağırlık |
|---|-------|---------|
| 1 | Over-engineering | Yüksek |
| 2 | Karmaşıklık | Yüksek |
| 3 | Vanilla JS yeterli | Orta |
| 4 | Debug zorluğu | Orta |

## 3. Alternatif Çözüm

**Seçilen:** ES6 modules ile native import/export
- `<script type="module">`
- Native ES6 imports
- No build step

---

*Reddedildi: 2026-08-15 · Governance: Red Team · Human Mode · Truth Mode*
