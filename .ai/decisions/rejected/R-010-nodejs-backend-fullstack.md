---
type: decision
id: "R-010"
title: "REJECTED: Node.js Backend (Full Stack)"
category: "architecture"
status: "rejected"
date: "2026-08-15"
updated: "2026-08-15"
authority: "Backend Architect"
governance: "Red Team · Human Mode · Truth Mode"
version: 1.0.0
tags: [rejected, architecture, nodejs, fullstack]
risk-level: "high"
rejection-reason: "PHP zorunlu, Node.js sadece download service"
rejected-by: "ADR-039"
references:
  - "[[decisions/accepted/ADR-039-7-service-platform-architecture]]"
---

# REJECTED: Node.js Backend (Full Stack)

---

## 1. Executive Summary

Tüm backend'in Node.js ile yazılması **reddedilmiştir**.

## 2. Red Nedeni

| # | Neden | Ağırlık |
|---|-------|---------|
| 1 | PHP 8.4 zorunlu | Kritik |
| 2 | PHP ekosistemi güçlü | Yüksek |
| 3 | Node.js sadece download service | Yüksek |
| 4 | Teknoloji çeşitliliği riski | Orta |

## 3. Alternatif Çözüm

**Seçilen:** PHP 8.4 backend + Node.js download service (ADR-039)
- PHP: Control, Media, Auth, AI services
- Node.js: Download service (sadece)
- C++: Audio, Device, Network services

---

*Reddedildi: 2026-08-15 · Governance: Red Team · Human Mode · Truth Mode*
