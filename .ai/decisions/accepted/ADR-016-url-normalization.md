---
type: decision
id: "016"
title: "ADR-016: URL Normalization"
category: "routing"
status: "frozen"
date: "2026-04-01"
updated: "2026-08-15"
authority: "Backend Architect"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [routing, url, normalization, subdomain, frozen]
risk-level: "low"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-004-multi-domain-spa]]"
---

# ADR-016: URL Normalization

---

## 1. Executive Summary

CoreMusic URL'leri standartlaştırılmıştır. Subdomain routing, case normalization ve trailing slash politikası tanımlanmıştır.

## 2. Decision

### URL Kuralları

| # | Kural | Durum |
|---|-------|-------|
| 1 | Subdomain routing | ✅ Zorunlu |
| 2 | Lowercase URL | ✅ Zorunlu |
| 3 | No trailing slash (except root) | ✅ Zorunlu |
| 4 | Hyphen separator | ✅ Zorunlu |
| 5 | UTF-8 encoding | ✅ Zorunlu |

### Subdomain Routing

| Subdomain | Port | Amaç |
|-----------|------|------|
| music | 81 | Ana medya |
| admin | 80 | Yönetim |
| auth | — | Auth |
| home | 81 | Ev |
| car | — | Araç |
| studio | 81 | Stüdyo |
| pro | 81 | Profesyonel |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-016: URL Normalization v2.0.0 — CoreMusic Routing*
*Authority: Backend Architect · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
