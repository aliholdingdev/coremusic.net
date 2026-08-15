---
type: decision
id: "009"
title: "ADR-009: Clean URL Redirect"
category: "routing"
status: "frozen"
date: "2026-03-01"
updated: "2026-08-15"
authority: "Backend Architect"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [routing, clean-url, redirect, frozen]
risk-level: "low"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-016-url-normalization]]"
---

# ADR-009: Clean URL Redirect

---

## 1. Executive Summary

CoreMusic'te **clean URL** yapısı kullanılır. Kullanıcı dostu URL'ler, otomatik redirect ile desteklenir.

## 2. Decision

### URL Formatı

| Eski | Yeni |
|------|------|
| /index.php?page=music | /music |
| /index.php?page=admin | /admin |
| /index.php?page=home | /home |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | Clean URL zorunlu | ✅ Zorunlu |
| 2 | 301 redirect | ✅ Zorunlu |
| 3 | SEO-friendly | ✅ Zorunlu |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-009: Clean URL Redirect v2.0.0 — CoreMusic Routing*
*Authority: Backend Architect · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
