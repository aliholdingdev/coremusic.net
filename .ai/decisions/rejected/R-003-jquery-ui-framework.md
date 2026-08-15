---
type: decision
id: "R-003"
title: "REJECTED: jQuery UI Framework"
category: "frontend"
status: "rejected"
date: "2026-08-15"
updated: "2026-08-15"
authority: "UI Designer"
governance: "Red Team · Human Mode · Truth Mode"
version: 1.0.0
tags: [rejected, frontend, jquery, framework]
risk-level: "high"
rejection-reason: "Framework yasağı (ADR-001), legacy teknoloji"
rejected-by: "ADR-001"
references:
  - "[[decisions/accepted/ADR-001-vanilla-js-itcss]]"
---

# REJECTED: jQuery UI Framework

---

## 1. Executive Summary

jQuery veya jQuery UI kullanımı **reddedilmiştir**.

## 2. Red Nedeni

| # | Neden | Ağırlık |
|---|-------|---------|
| 1 | ADR-001: Framework yasağı | Kritik |
| 2 | Legacy teknoloji | Yüksek |
| 3 | Vanilla JS modern ve yeterli | Yüksek |
| 4 | Performance overhead | Orta |

## 3. Reddetilen Yaklaşım

jQuery:
- Framework yasağını ihlal eder
- Legacy API (DOM manipulation)
- Modern tarayıcılar jQuery gerektirmez
- Bundle boyutu gereksiz

## 4. Alternatif Çözüm

**Seçilen:** Vanilla JS ES6+ (ADR-001)
- Fetch API (AJAX yerine)
- querySelectorAll (jQuery select yerine)
- classList (jQuery class yerine)
- addEventListener (jQuery event yerine)

---

*Reddedildi: 2026-08-15 · Governance: Red Team · Human Mode · Truth Mode*
